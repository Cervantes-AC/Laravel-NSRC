<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DutySession;
use App\Models\User;
use App\Models\VolunteerMetrics;
use App\Services\DutyEngine;
use App\Services\MetricsService;
use App\Services\MySQLAttendanceService;
use App\Services\NameNormalizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search', '');
        $status = $request->input('status', '');
        $sector = $request->input('sector', '');
        $location = $request->input('location', '');
        $duration = $request->input('duration', '');
        $integrity = $request->input('integrity', '');
        $dateFrom = $request->input('dateFrom', '');
        $dateTo = $request->input('dateTo', '');
        $perPage = (int) $request->input('perPage', 25);
        $page = (int) $request->input('page', 1);

        $query = DutySession::query()->with('volunteer');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%'.$search.'%')
                    ->orWhereHas('volunteer', function ($v) use ($search) {
                        $v->where('full_name', 'like', '%'.$search.'%')
                            ->orWhere('name', 'like', '%'.$search.'%')
                            ->orWhere('school_id', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($sector !== '') {
            $query->where('sector', $sector);
        }

        if ($location !== '') {
            $query->where('location', $location);
        }

        if ($duration === 'completed_hours') {
            $query->whereNotNull('duration_minutes')->where('duration_minutes', '>', 0);
        } elseif ($duration === 'under_4h') {
            $query->whereBetween('duration_minutes', [1, 239]);
        } elseif ($duration === '4h_8h') {
            $query->whereBetween('duration_minutes', [240, 480]);
        } elseif ($duration === 'over_8h') {
            $query->where('duration_minutes', '>', 480);
        } elseif ($duration === 'missing') {
            $query->whereNull('duration_minutes');
        }

        if ($integrity === 'high') {
            $query->where('integrity_score', '>=', 90);
        } elseif ($integrity === 'medium') {
            $query->whereBetween('integrity_score', [70, 89.99]);
        } elseif ($integrity === 'low') {
            $query->where('integrity_score', '<', 70);
        }

        if ($dateFrom) {
            $query->whereDate('date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('date', '<=', $dateTo);
        }

        $totalMinutes = (clone $query)->sum('duration_minutes');
        $completeCount = (clone $query)->where('status', 'COMPLETE')->count();
        $filteredCount = (clone $query)->count();
        $totalPages = max(1, (int) ceil($filteredCount / $perPage));

        $sessions = $query->orderByDesc('date')->orderByDesc('time_in')
            ->skip(($page - 1) * $perPage)->take($perPage)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'full_name' => $s->full_name,
                'school_id' => $s->volunteer?->school_id,
                'date' => $s->date?->format('M d, Y'),
                'time_in' => $s->time_in?->format('h:i A'),
                'time_out' => $s->time_out?->format('h:i A'),
                'duration_minutes' => $s->duration_minutes,
                'status' => $s->status,
                'location' => $s->location,
                'sector' => $s->sector,
                'integrity_score' => $s->integrity_score,
                'view_url' => route('admin.attendance.show', $s),
            ]);

        $sectors = DutySession::query()->whereNotNull('sector')->distinct()->orderBy('sector')->pluck('sector');
        $locations = DutySession::query()->whereNotNull('location')->distinct()->orderBy('location')->pluck('location');

        return response()->json([
            'sessions' => $sessions,
            'sectors' => $sectors,
            'locations' => $locations,
            'filteredCount' => $filteredCount,
            'totalMinutes' => $totalMinutes,
            'completeCount' => $completeCount,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'perPage' => $perPage,
        ]);
    }

    public function processLocal(): JsonResponse
    {
        $logs = Attendance::query()->orderBy('date_time')->get();

        if ($logs->isEmpty()) {
            return response()->json(['message' => 'No attendance records found to process. Import attendance data first.']);
        }

        $dutyEngine = app(DutyEngine::class);
        $sessions = $dutyEngine->processDutyLogs($logs);
        $created = 0;
        $updated = 0;

        foreach ($sessions as $session) {
            $volunteerId = User::where('full_name', $session->full_name)->value('id');

            $attributes = [
                'full_name' => $session->full_name,
                'date' => $session->date,
                'time_in' => $session->time_in,
                'time_out' => $session->time_out,
                'duration_minutes' => $session->duration_minutes,
                'status' => $session->status,
                'location' => $session->location,
                'sector' => $session->sector,
                'integrity_score' => $session->integrity_score,
                'volunteer_id' => $volunteerId,
                'trace_id' => 'LOCAL-'.strtoupper(substr(md5($session->full_name.$session->date.($session->time_in ?? now())), 0, 8)),
            ];

            $match = DutySession::query()
                ->where('full_name', $session->full_name)
                ->whereDate('date', $session->date)
                ->when($session->time_in, fn ($q) => $q->where('time_in', $session->time_in))
                ->first();

            if ($match) {
                $match->update($attributes);
                $updated++;
            } else {
                DutySession::create($attributes);
                $created++;
            }
        }

        VolunteerMetrics::query()->delete();
        app(MetricsService::class)->calculateVolunteerMetrics(DutySession::query()->get());

        return response()->json([
            'message' => sprintf('Local processing complete: %d attendance records processed, %d sessions created, %d updated.', $logs->count(), $created, $updated),
        ]);
    }

    public function sync(): JsonResponse
    {
        $service = app(MySQLAttendanceService::class);
        $records = $service->fetchAttendanceData();

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $seenInBatch = [];

        if (! empty($records)) {
            foreach ($records as $record) {
                $signature = $service->recordSignature($record);
                if (isset($seenInBatch[$signature])) {
                    $skipped++;

                    continue;
                }
                $seenInBatch[$signature] = true;

                $exists = Attendance::where('source_signature', $signature)->exists();
                if ($exists) {
                    $skipped++;

                    continue;
                }

                Attendance::create([
                    'full_name' => $record['fullName'],
                    'attendance' => $record['attendance'],
                    'date_time' => $record['dateTime'],
                    'location' => $record['location'],
                    'shift_type' => $record['shiftType'],
                    'source_signature' => $signature,
                    'source_payload' => $record['payload'] ?? [],
                ]);
                $imported++;
            }
        }

        $existingCount = Attendance::count();
        if ($existingCount === 0) {
            return response()->json(['message' => 'No attendance records found. Import data via the Import page or configure a MySQL source.']);
        }

        $dutyEngine = app(DutyEngine::class);
        $logs = Attendance::query()->orderBy('date_time')->get();
        $sessions = $dutyEngine->processDutyLogs($logs);
        $created = 0;
        $updated = 0;

        foreach ($sessions as $session) {
            $volunteerId = $this->resolveVolunteerId($session->full_name);

            $attributes = [
                'full_name' => $session->full_name,
                'date' => $session->date,
                'time_in' => $session->time_in,
                'time_out' => $session->time_out,
                'duration_minutes' => $session->duration_minutes,
                'status' => $session->status,
                'location' => $session->location,
                'sector' => $session->sector,
                'integrity_score' => $session->integrity_score,
                'volunteer_id' => $volunteerId,
                'trace_id' => 'SYNC-'.strtoupper(substr(md5($session->full_name.$session->date.($session->time_in ?? now())), 0, 8)),
            ];

            $match = DutySession::query()
                ->where('full_name', $session->full_name)
                ->whereDate('date', $session->date)
                ->when($session->time_in, fn ($q) => $q->where('time_in', $session->time_in))
                ->first();

            if ($match) {
                $match->update($attributes);
                $updated++;
            } else {
                DutySession::create($attributes);
                $created++;
            }
        }

        VolunteerMetrics::query()->delete();
        app(MetricsService::class)->calculateVolunteerMetrics(DutySession::query()->get());

        return response()->json([
            'message' => sprintf('Sync complete: %d imported from source, %d skipped, %d duty sessions created, %d updated.', $imported, $skipped, $created, $updated),
        ]);
    }

    /**
     * Resolve volunteer ID from full name using exact match, fuzzy match, or skip if not found.
     * Returns null if no matching volunteer is found.
     */
    private function resolveVolunteerId(string $fullName): ?int
    {
        // Try exact match first
        $exact = User::query()
            ->where('full_name', $fullName)
            ->value('id');

        if ($exact) {
            return (int) $exact;
        }

        // Try fuzzy match with 85% similarity threshold
        $nameService = app(NameNormalizationService::class);
        foreach (User::query()->whereNotNull('full_name')->get(['id', 'full_name']) as $user) {
            if ($nameService->areNamesSimilar($fullName, $user->full_name, 85.0)) {
                return $user->id;
            }
        }

        // No match found - return null (will be handled by MetricsService filter)
        return null;
    }
}
