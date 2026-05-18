<?php

namespace App\Services;

use App\Models\DutySession;
use App\Models\User;
use App\Models\VolunteerMetrics;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MetricsService
{
    private const STANDARD_MINUTES = 480;

    private const MIN_SESSION_MINUTES = 60;

    public function calculateVolunteerMetrics(Collection $sessions): Collection
    {
        $grouped = $sessions->groupBy('volunteer_id');
        $metrics = collect();

        foreach ($grouped as $volunteerId => $volunteerSessions) {
            $dailyGroups = $volunteerSessions->groupBy(fn ($s) => $s->date instanceof \Carbon\Carbon ? $s->date->toDateString() : $s->date);

            $totalRegularMinutes = 0;
            $totalOvertimeMinutes = 0;
            $totalUndertimeMinutes = 0;
            $totalMinutes = 0;
            $invalidRecordCount = 0;
            $sessionCount = $volunteerSessions->count();

            foreach ($dailyGroups as $daySessions) {
                $dayMinutes = $daySessions->sum('duration_minutes');
                $totalMinutes += $dayMinutes;

                $invalidRecordCount += $daySessions->filter(fn ($s) => $s->status !== 'COMPLETE')->count();

                if ($dayMinutes <= self::STANDARD_MINUTES) {
                    $totalRegularMinutes += $dayMinutes;
                    $totalUndertimeMinutes += max(0, self::STANDARD_MINUTES - $dayMinutes);
                } else {
                    $totalRegularMinutes += self::STANDARD_MINUTES;
                    $totalOvertimeMinutes += ($dayMinutes - self::STANDARD_MINUTES);
                }
            }

            $volunteer = User::find($volunteerId);

            $record = VolunteerMetrics::updateOrCreate(
                ['volunteer_id' => $volunteerId],
                [
                    'full_name' => $volunteer?->full_name ?? 'Unknown',
                    'total_regular_minutes' => $totalRegularMinutes,
                    'total_overtime_minutes' => $totalOvertimeMinutes,
                    'total_undertime_minutes' => $totalUndertimeMinutes,
                    'total_minutes' => $totalMinutes,
                    'invalid_record_count' => $invalidRecordCount,
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
        $activeSessions = DutySession::where('status', 'ONGOING')->orWhereNull('time_out')->count();
        $todayCount = DutySession::whereDate('date', today())->count();
        $avgDuration = DutySession::whereNotNull('duration_minutes')->avg('duration_minutes') ?? 0;
        $totalSessions = DutySession::count();
        $completeSessions = DutySession::where('status', 'COMPLETE')->count();
        $completionRate = $totalSessions > 0 ? round(($completeSessions / $totalSessions) * 100, 1) : 0;
        $avgIntegrity = DutySession::avg('integrity_score') ?? 0;

        return [
            'total_users' => $totalUsers,
            'active_sessions' => $activeSessions,
            'today_count' => $todayCount,
            'average_duration_minutes' => round($avgDuration, 2),
            'total_sessions' => $totalSessions,
            'complete_sessions' => $completeSessions,
            'completion_rate' => $completionRate,
            'avg_integrity_score' => round($avgIntegrity, 1),
        ];
    }

    public function getVolunteerRankings(string $sortBy = 'total_hours'): Collection
    {
        $query = VolunteerMetrics::with('volunteer');

        return match ($sortBy) {
            'total_hours' => $query->orderByDesc('total_minutes')->get(),
            'total_sessions' => $query->orderByDesc('session_count')->get(),
            'avg_duration' => $query->orderByDesc('total_regular_minutes')->get(),
            'compliance' => $query->orderBy('invalid_record_count')->orderByDesc('total_minutes')->get(),
            default => $query->orderByDesc('total_minutes')->get(),
        };
    }

    public function getWeeklyMetrics(int $userId): array
    {
        $weekSessions = DutySession::where('volunteer_id', $userId)
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->get();

        $regular = 0;
        $overtime = 0;
        $undertime = 0;

        foreach ($weekSessions as $session) {
            $mins = (int) $session->duration_minutes;
            if ($mins <= self::STANDARD_MINUTES) {
                $regular += $mins;
                $undertime += max(0, self::STANDARD_MINUTES - $mins);
            } else {
                $regular += self::STANDARD_MINUTES;
                $overtime += ($mins - self::STANDARD_MINUTES);
            }
        }

        $target = self::STANDARD_MINUTES * 5;
        $total = $regular + $overtime;
        $compliancePct = $target > 0 ? min(100, round(($total / $target) * 100)) : 0;

        return [
            'regular_minutes' => $regular,
            'overtime_minutes' => $overtime,
            'undertime_minutes' => $undertime,
            'total_minutes' => $total,
            'compliance_percentage' => $compliancePct,
            'session_count' => $weekSessions->count(),
        ];
    }
}
