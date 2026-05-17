<?php

namespace App\Livewire;

use App\Models\DutySession;
use App\Services\MetricsService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public int $totalRecords = 0;

    public int $todayCount = 0;

    public int $activeNow = 0;

    public int $missingTimeouts = 0;

    public float $avgDuration = 0;

    public Collection $recentSessions;

    public string $dateFilter = 'today';

    public function mount(MetricsService $metricsService): void
    {
        $this->recentSessions = collect();
        $this->loadStats($metricsService);
        $this->loadRecentSessions();
    }

    public function loadStats(MetricsService $metricsService): void
    {
        $summary = $metricsService->getSystemSummary();

        $this->totalRecords = DutySession::count();
        $this->todayCount = (int) $summary['today_count'];
        $this->activeNow = (int) $summary['active_sessions'];
        $this->missingTimeouts = DutySession::where('status', 'MISSING_TIMEOUT')->count();
        $this->avgDuration = (float) $summary['average_duration_minutes'];
    }

    public function loadRecentSessions(): void
    {
        $query = DutySession::with('volunteer')->latest();

        if ($this->dateFilter === 'today') {
            $query->whereDate('date', today());
        }

        $this->recentSessions = $query->take(10)->get();
    }

    public function updatedDateFilter(): void
    {
        $this->loadRecentSessions();
    }

    #[On('dashboard-refresh')]
    public function refresh(): void
    {
        $metrics = app(MetricsService::class);
        $this->loadStats($metrics);
        $this->loadRecentSessions();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
