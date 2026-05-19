<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function fetchData(Request $request): JsonResponse
    {
        $options = array_filter([
            'name' => $request->input('name'),
            'date' => $request->input('date'),
        ]);

        $service = app(MySQLAttendanceService::class);
        $data = $service->fetchAttendanceData($options);

        return response()->json([
            'success' => true,
            'count' => count($data),
            'data' => $data,
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        try {
            $options = array_filter([
                'name' => $request->input('name'),
                'date' => $request->input('date'),
            ]);

            $service = app(MySQLAttendanceService::class);
            $records = $service->fetchAttendanceData($options);

            if (empty($records)) {
                return response()->json([
                    'imported' => 0,
                    'skipped' => 0,
                    'attendance_total' => Attendance::count(),
                    'sessions_created' => 0,
                    'sessions_updated' => 0,
                    'errors' => ['No data retrieved from MySQL source'],
                ]);
            }

            usort($records, fn ($a, $b) => $a['dateTime'] <=> $b['dateTime']);

            $imported = 0;
            $skipped = 0;
            $errors = [];
            $seenInBatch = [];

            DB::beginTransaction();
            try {
                foreach ($records as $index => $record) {
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

                $dutyEngine = app(DutyEngine::class);
                $logs = Attendance::query()->orderBy('date_time')->get();
                $sessions = $dutyEngine->processDutyLogs($logs);
                $createdCount = 0;
                $updatedCount = 0;

                foreach ($sessions as $session) {
                    // Resolve volunteer_id with exact match, fuzzy match, or skip if not found
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
                        'trace_id' => 'SYNC-'.strtoupper(substr(md5(uniqid()), 0, 8)),
                    ];

                    $match = DutySession::query()
                        ->where('full_name', $session->full_name)
                        ->whereDate('date', $session->date)
                        ->when($session->time_in, fn ($q) => $q->where('time_in', $session->time_in))
                        ->first();

                    if ($match) {
                        $match->update($attributes);
                        $updatedCount++;
                    } else {
                        DutySession::create($attributes);
                        $createdCount++;
                    }
                }

                VolunteerMetrics::query()->delete();
                app(MetricsService::class)->calculateVolunteerMetrics(DutySession::query()->get());

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            return response()->json([
                'imported' => $imported,
                'skipped' => $skipped,
                'attendance_total' => Attendance::count(),
                'sessions_created' => $createdCount,
                'sessions_updated' => $updatedCount,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            Log::error('MySQL attendance sync failed', ['error' => $e->getMessage()]);

            return response()->json([
                'imported' => 0,
                'skipped' => 0,
                'attendance_total' => Attendance::count(),
                'sessions_created' => 0,
                'sessions_updated' => 0,
                'errors' => [$e->getMessage()],
            ], 500);
        }
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
        Log::warning('No volunteer found for attendance record', ['full_name' => $fullName]);

        return null;
    }
}
