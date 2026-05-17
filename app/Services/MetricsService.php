<?php

namespace App\Services;

use App\Models\User;
use App\Models\VolunteerMetrics;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MetricsService
{
    private const REGULAR_HOURS_LIMIT = 480;

    public function calculateVolunteerMetrics(Collection $sessions): Collection
    {
        $grouped = $sessions->groupBy('volunteer_id');
        $metrics = collect();

        foreach ($grouped as $volunteerId => $volunteerSessions) {
            $totalMinutes = $volunteerSessions->sum('duration_minutes');
            $invalidCount = $volunteerSessions->filter(
                fn($s) => $s->status === 'INVALID_LOG'
            )->count();
            $sessionCount = $volunteerSessions->count();

            $regularMinutes = min($totalMinutes, self::REGULAR_HOURS_LIMIT);
            $overtimeMinutes = max(0, $totalMinutes - self::REGULAR_HOURS_LIMIT);
            $undertimeMinutes = $totalMinutes < self::REGULAR_HOURS_LIMIT
                ? self::REGULAR_HOURS_LIMIT - $totalMinutes
                : 0;

            $volunteer = User::find($volunteerId);

            $record = VolunteerMetrics::updateOrCreate(
                ['volunteer_id' => $volunteerId],
                [
                    'full_name' => $volunteer?->full_name ?? 'Unknown',
                    'total_regular_minutes' => $regularMinutes,
                    'total_overtime_minutes' => $overtimeMinutes,
                    'total_undertime_minutes' => $undertimeMinutes,
                    'invalid_record_count' => $invalidCount,
                    'session_count' => $sessionCount,
                ]
            );

            $metrics->push($record);
        }

        return $metrics;
    }

    public function getSystemSummary(): array
    {
        $totalUsers = User::count();
        $activeSessions = \App\Models\DutySession::whereNull('time_out')->count();
        $todayCount = \App\Models\DutySession::whereDate('created_at', today())->count();
        $avgDuration = \App\Models\DutySession::avg('duration_minutes') ?? 0;

        return [
            'total_users' => $totalUsers,
            'active_sessions' => $activeSessions,
            'today_count' => $todayCount,
            'average_duration_minutes' => round($avgDuration, 2),
        ];
    }

    public function getVolunteerRankings(): Collection
    {
        return VolunteerMetrics::orderByDesc('total_regular_minutes')
            ->with('volunteer')
            ->get();
    }
}
