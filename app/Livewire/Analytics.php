<?php

namespace App\Livewire;

use App\Models\DutySession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Analytics extends Component
{
    public array $chartData = [];

    public array $sessionsByStatus = [];

    public array $sessionsBySector = [];

    public int $totalSessions = 0;

    public int $totalHours = 0;

    public int $activeVolunteers = 0;

    public string $period = 'month';

    public string $status = '';

    public string $sector = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public array $insights = [];

    public function mount(): void
    {
        $this->loadChartData();
        $this->loadStats();
        $this->loadDistribution();
        $this->loadInsights();
    }

    public function getDateRange(): array
    {
        if ($this->dateFrom || $this->dateTo) {
            return [
                $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : null,
                $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : null,
            ];
        }

        return match ($this->period) {
            'week' => [now()->subDays(7), now()],
            'month' => [now()->subDays(30), now()],
            '3m' => [now()->subDays(90), now()],
            '6m' => [now()->subDays(180), now()],
            'year' => [now()->subYear(), now()],
            'all' => [null, null],
            default => [now()->subDays(30), now()],
        };
    }

    public function getDateGroupFormat(): string
    {
        if ($this->dateFrom || $this->dateTo) {
            $start = $this->dateFrom ? Carbon::parse($this->dateFrom) : now()->subDays(30);
            $end = $this->dateTo ? Carbon::parse($this->dateTo) : now();

            return $start->diffInDays($end) > 180 ? 'Y-m' : 'Y-m-d';
        }

        return match ($this->period) {
            '6m', 'year', 'all' => 'Y-m',
            default => 'Y-m-d',
        };
    }

    public function getDateLabelFormat(): string
    {
        if ($this->dateFrom || $this->dateTo) {
            $start = $this->dateFrom ? Carbon::parse($this->dateFrom) : now()->subDays(30);
            $end = $this->dateTo ? Carbon::parse($this->dateTo) : now();

            return $start->diffInDays($end) > 180 ? 'Y M' : 'M d';
        }

        return match ($this->period) {
            'week' => 'D m/d',
            '6m', 'year', 'all' => 'Y M',
            default => 'M d',
        };
    }

    public function loadChartData(): void
    {
        $query = $this->filteredSessionQuery()->whereNotNull('date');

        $groupFormat = $this->getDateGroupFormat();
        $labelFormat = $this->getDateLabelFormat();

        $records = $query->orderBy('date')->get(['date', 'duration_minutes'])
            ->groupBy(fn (DutySession $session) => $session->date?->format($groupFormat))
            ->map(fn ($sessions) => [
                'label' => $sessions->first()->date?->format($labelFormat) ?? 'N/A',
                'count' => $sessions->count(),
                'total_minutes' => $sessions->sum('duration_minutes'),
            ])
            ->values();

        $this->chartData = [
            'labels' => $records->pluck('label')->all(),
            'datasets' => [
                [
                    'label' => 'Sessions',
                    'data' => $records->pluck('count')->all(),
                ],
                [
                    'label' => 'Hours',
                    'data' => $records->map(fn (array $record) => round($record['total_minutes'] / 60, 1))->all(),
                ],
            ],
        ];
    }

    public function loadStats(): void
    {
        $query = $this->filteredSessionQuery();

        $this->totalSessions = (clone $query)->count();
        $this->totalHours = (int) (clone $query)->sum('duration_minutes');
        $this->activeVolunteers = (clone $query)->distinct('volunteer_id')->count('volunteer_id');
    }

    public function loadDistribution(): void
    {
        $query = $this->filteredSessionQuery();

        $this->sessionsByStatus = (clone $query)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $this->sessionsBySector = (clone $query)
            ->select('sector', DB::raw('count(*) as count'))
            ->whereNotNull('sector')
            ->groupBy('sector')
            ->pluck('count', 'sector')
            ->toArray();
    }

    public function loadInsights(): void
    {
        $query = $this->filteredSessionQuery();

        $peakDay = (clone $query)
            ->whereNotNull('date')
            ->get(['date'])
            ->groupBy(fn (DutySession $session) => $session->date?->format('l') ?? 'N/A')
            ->map(fn ($sessions, string $dayName) => [
                'day_name' => $dayName,
                'count' => $sessions->count(),
            ])
            ->sortByDesc('count')
            ->first();

        $totalHoursRounded = round($this->totalHours / 60, 1);
        $avgPerVolunteer = $this->activeVolunteers > 0 ? round($totalHoursRounded / $this->activeVolunteers, 1) : 0;

        $totalInRange = (clone $query)->count();
        $completeInRange = (clone $query)->where('status', 'COMPLETE')->count();
        $efficiency = $totalInRange > 0 ? round(($completeInRange / $totalInRange) * 100, 1) : 0;

        $this->insights = [
            'peak_day' => $peakDay['day_name'] ?? 'N/A',
            'peak_day_count' => $peakDay['count'] ?? 0,
            'avg_hours_per_volunteer' => $avgPerVolunteer,
            'total_hours_rounded' => $totalHoursRounded,
            'efficiency' => $efficiency,
        ];
    }

    public function filter(string $period): void
    {
        $this->period = $period;
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->reload();
    }

    public function updatedStatus(): void
    {
        $this->reload();
    }

    public function updatedSector(): void
    {
        $this->reload();
    }

    public function updatedDateFrom(): void
    {
        $this->reload();
    }

    public function updatedDateTo(): void
    {
        $this->reload();
    }

    public function clearFilters(): void
    {
        $this->reset(['status', 'sector', 'dateFrom', 'dateTo']);
        $this->reload();
    }

    private function reload(): void
    {
        $this->loadChartData();
        $this->loadStats();
        $this->loadDistribution();
        $this->loadInsights();
    }

    private function filteredSessionQuery()
    {
        [$start, $end] = $this->getDateRange();

        $query = DutySession::query();

        if ($start) {
            $query->where('date', '>=', $start->copy()->startOfDay());
        }
        if ($end) {
            $query->where('date', '<=', $end->copy()->endOfDay());
        }
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }
        if ($this->sector !== '') {
            $query->where('sector', $this->sector);
        }

        return $query;
    }

    public function render()
    {
        return view('livewire.analytics', [
            'sectors' => DutySession::query()->whereNotNull('sector')->distinct()->orderBy('sector')->pluck('sector'),
        ]);
    }
}
