<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DutySession;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sortBy', 'name');
        $sortDirection = $request->input('sortDirection', 'asc');
        $complianceFilter = $request->input('complianceFilter', 'all');
        $viewMode = $request->input('viewMode', 'list');
        $page = (int) $request->input('page', 1);

        $allUsers = User::query()->where('role', '!=', 'admin')->get();

        $enrichedData = $allUsers->map(function (User $user) {
            $sessions = $user->dutySessions()->get();
            $totalRegularMinutes = 0;
            $totalOvertimeMinutes = 0;
            $totalUndertimeMinutes = 0;
            $invalidRecordCount = 0;

            foreach ($sessions as $session) {
                $issues = $this->deriveIssues($session);
                if (count($issues) > 0) $invalidRecordCount++;

                if ($session->time_in && $session->time_out) {
                    $minutes = $session->time_in->diffInMinutes($session->time_out);
                    if ($minutes < 60) $totalUndertimeMinutes += $minutes;
                    elseif ($minutes <= 480) $totalRegularMinutes += $minutes;
                    else { $totalRegularMinutes += 480; $totalOvertimeMinutes += ($minutes - 480); }
                }
            }

            return [
                'id' => $user->id,
                'fullName' => $user->full_name,
                'volunteerId' => "REG-{$user->id}",
                'email' => $user->email,
                'serialNumber' => $user->serial_number ?? "REG-{$user->id}",
                'role' => $user->role ?? 'member',
                'avatar' => $user->avatar,
                'sessionCount' => $sessions->count(),
                'totalRegularMinutes' => $totalRegularMinutes,
                'totalOvertimeMinutes' => $totalOvertimeMinutes,
                'totalUndertimeMinutes' => $totalUndertimeMinutes,
                'invalidRecordCount' => $invalidRecordCount,
                'lastActive' => $sessions->first()?->date?->format('Y-m-d'),
            ];
        });

        if ($search) {
            $l = strtolower($search);
            $enrichedData = $enrichedData->filter(fn ($item) =>
                str_contains(strtolower($item['fullName']), $l) ||
                str_contains(strtolower($item['serialNumber']), $l) ||
                str_contains(strtolower($item['email']), $l));
        }

        if ($complianceFilter === 'issues_only') {
            $enrichedData = $enrichedData->filter(fn ($item) => $item['invalidRecordCount'] > 0);
        } elseif ($complianceFilter === 'compliance_only') {
            $enrichedData = $enrichedData->filter(fn ($item) => $item['invalidRecordCount'] === 0);
        }

        $enrichedData = $enrichedData->sort(function ($a, $b) use ($sortBy, $sortDirection) {
            $cmp = match ($sortBy) {
                'name' => strcmp($a['fullName'], $b['fullName']),
                'sessions' => $a['sessionCount'] <=> $b['sessionCount'],
                'hours' => ($a['totalRegularMinutes'] + $a['totalOvertimeMinutes']) <=> ($b['totalRegularMinutes'] + $b['totalOvertimeMinutes']),
                'issues' => $a['invalidRecordCount'] <=> $b['invalidRecordCount'],
                default => 0,
            };
            return $sortDirection === 'asc' ? $cmp : -$cmp;
        })->values();

        $totalPersonnel = $enrichedData->count();
        $cleanCount = $enrichedData->filter(fn ($item) => $item['invalidRecordCount'] === 0)->count();
        $issueCount = $totalPersonnel - $cleanCount;
        $totalIssues = $enrichedData->sum('invalidRecordCount');
        $totalHours = $enrichedData->sum(fn ($item) => $item['totalRegularMinutes'] + $item['totalOvertimeMinutes']);

        $perPage = $viewMode === 'grid' ? 12 : 10;
        $totalPages = max(1, (int) ceil($totalPersonnel / $perPage));
        $paginatedData = $enrichedData->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json([
            'personnel' => $paginatedData,
            'totalPersonnel' => $totalPersonnel,
            'cleanCount' => $cleanCount,
            'issueCount' => $issueCount,
            'totalIssues' => $totalIssues,
            'totalHours' => $totalHours,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $name = $request->input('name');
        if (!$name) return response()->json(['sessions' => []]);

        $sessions = DutySession::where('full_name', $name)
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'date' => $s->date?->format('Y-m-d'),
                'time_in' => $s->time_in?->format('h:i A'),
                'time_out' => $s->time_out?->format('h:i A'),
                'duration_minutes' => $s->duration_minutes,
                'status' => $s->status,
                'location' => $s->location,
                'sector' => $s->sector,
            ]);

        return response()->json(['sessions' => $sessions]);
    }

    private function deriveIssues(DutySession $session): array
    {
        $issues = [];
        if (!$session->time_out) {
            $issues[] = ['date' => $session->date->format('Y-m-d'), 'type' => 'MISSING_TIMEOUT', 'description' => "No time-out recorded on {$session->date->format('Y-m-d')}."];
        }
        if ($session->time_in && $session->time_out) {
            if ($session->time_out->timestamp * 1000 - $session->time_in->timestamp * 1000 <= 0) {
                $issues[] = ['date' => $session->date->format('Y-m-d'), 'type' => 'ZERO_DURATION', 'description' => "Time-out is not after time-in on {$session->date->format('Y-m-d')}."];
            }
        }
        if ($session->date && $session->date > now()->toDateString()) {
            $issues[] = ['date' => $session->date->format('Y-m-d'), 'type' => 'FUTURE_DATE', 'description' => "Session date {$session->date->format('Y-m-d')} is in the future."];
        }
        return $issues;
    }
}
