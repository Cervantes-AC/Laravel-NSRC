<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DutySession;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function data(Request $request): JsonResponse
    {
        $period = $request->input('period', 'month');
        $status = $request->input('status', '');
        $sector = $request->input('sector', '');
        $dateFrom = $request->input('dateFrom', '');
        $dateTo = $request->input('dateTo', '');

        [$start, $end] = $this->getDateRange($period, $dateFrom, $dateTo);
        $groupFormat = $this->getDateGroupFormat($period, $dateFrom, $dateTo);
        $labelFormat = $this->getDateLabelFormat($period, $dateFrom, $dateTo);

        $query = DutySession::query();

        if ($start) { $query->where('date', '>=', $start->copy()->startOfDay()); }
        if ($end) { $query->where('date', '<=', $end->copy()->endOfDay()); }
        if ($status !== '') { $query->where('status', $status); }
        if ($sector !== '') { $query->where('sector', $sector); }

        $records = (clone $query)->orderBy('date')->get(['date', 'duration_minutes'])
            ->groupBy(fn (DutySession $s) => $s->date?->format($groupFormat))
            ->map(fn ($sessions) => [
                'label' => $sessions->first()->date?->format($labelFormat) ?? 'N/A',
                'count' => $sessions->count(),
                'total_minutes' => $sessions->sum('duration_minutes'),
            ])
            ->values();

        $chartData = [
            'labels' => $records->pluck('label')->all(),
            'datasets' => [
                ['label' => 'Sessions', 'data' => $records->pluck('count')->all()],
                ['label' => 'Hours', 'data' => $records->map(fn ($r) => round($r['total_minutes'] / 60, 1))->all()],
            ],
        ];

        $totalSessions = (clone $query)->count();
        $totalHours = (int) (clone $query)->sum('duration_minutes');
        $activeVolunteers = (clone $query)->distinct('volunteer_id')->count('volunteer_id');

        $sessionsByStatus = (clone $query)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $sessionsBySector = (clone $query)
            ->select('sector', DB::raw('count(*) as count'))
            ->whereNotNull('sector')
            ->groupBy('sector')
            ->pluck('count', 'sector')
            ->toArray();

        $peakDay = (clone $query)->whereNotNull('date')->get(['date'])
            ->groupBy(fn (DutySession $s) => $s->date?->format('l') ?? 'N/A')
            ->map(fn ($sessions, string $dayName) => ['day_name' => $dayName, 'count' => $sessions->count()])
            ->sortByDesc('count')
            ->first();

        $totalHoursRounded = round($totalHours / 60, 1);
        $avgPerVolunteer = $activeVolunteers > 0 ? round($totalHoursRounded / $activeVolunteers, 1) : 0;
        $totalInRange = (clone $query)->count();
        $completeInRange = (clone $query)->where('status', 'COMPLETE')->count();
        $efficiency = $totalInRange > 0 ? round(($completeInRange / $totalInRange) * 100, 1) : 0;

        return response()->json([
            'chartData' => $chartData,
            'totalSessions' => $totalSessions,
            'totalHours' => $totalHours,
            'activeVolunteers' => $activeVolunteers,
            'sessionsByStatus' => $sessionsByStatus,
            'sessionsBySector' => $sessionsBySector,
            'insights' => [
                'peak_day' => $peakDay['day_name'] ?? 'N/A',
                'peak_day_count' => $peakDay['count'] ?? 0,
                'avg_hours_per_volunteer' => $avgPerVolunteer,
                'total_hours_rounded' => $totalHoursRounded,
                'efficiency' => $efficiency,
            ],
            'sectors' => DutySession::query()->whereNotNull('sector')->distinct()->orderBy('sector')->pluck('sector'),
        ]);
    }

    private function getDateRange(string $period, string $dateFrom, string $dateTo): array
    {
        if ($dateFrom || $dateTo) {
            return [
                $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null,
                $dateTo ? Carbon::parse($dateTo)->endOfDay() : null,
            ];
        }
        return match ($period) {
            'week' => [now()->subDays(7), now()],
            'month' => [now()->subDays(30), now()],
            '3m' => [now()->subDays(90), now()],
            '6m' => [now()->subDays(180), now()],
            'year' => [now()->subYear(), now()],
            default => [now()->subDays(30), now()],
        };
    }

    private function getDateGroupFormat(string $period, string $dateFrom, string $dateTo): string
    {
        if ($dateFrom || $dateTo) {
            $start = $dateFrom ? Carbon::parse($dateFrom) : now()->subDays(30);
            $end = $dateTo ? Carbon::parse($dateTo) : now();
            return $start->diffInDays($end) > 180 ? 'Y-m' : 'Y-m-d';
        }
        return in_array($period, ['6m', 'year', 'all']) ? 'Y-m' : 'Y-m-d';
    }

    private function getDateLabelFormat(string $period, string $dateFrom, string $dateTo): string
    {
        if ($dateFrom || $dateTo) {
            $start = $dateFrom ? Carbon::parse($dateFrom) : now()->subDays(30);
            $end = $dateTo ? Carbon::parse($dateTo) : now();
            return $start->diffInDays($end) > 180 ? 'Y M' : 'M d';
        }
        return match ($period) { 'week' => 'D m/d', '6m', 'year', 'all' => 'Y M', default => 'M d' };
    }
}
