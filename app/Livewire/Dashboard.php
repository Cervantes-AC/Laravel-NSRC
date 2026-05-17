<?php

namespace App\Livewire;

use App\Services\MetricsService;
use App\Models\DutySession;
use Illuminate\Support\Collection;
use Livewire\Component;

class Dashboard extends Component
{
    public array $stats = [];

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

        $this->stats = [
            'total_records' => DutySession::count(),
            'today_count' => $summary['today_count'],
            'active_now' => $summary['active_sessions'],
            'missing_timeouts' => DutySession::where('status', 'MISSING_TIMEOUT')->count(),
            'avg_duration' => $summary['average_duration_minutes'],
        ];
    }

    public function loadRecentSessions(): void
    {
        $this->recentSessions = DutySession::with('volunteer')
            ->latest()
            ->take(10)
            ->get();
    }

    public function refresh(): void
    {
        app(MetricsService::class);
        $this->loadStats(app(MetricsService::class));
        $this->loadRecentSessions();
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('components.layouts.app');
    }
}
