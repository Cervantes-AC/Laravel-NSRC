<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\DutySession;
use App\Models\User;
use App\Services\MetricsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public int $totalRecords = 0;

    public int $todayCount = 0;

    public int $activeNow = 0;

    public int $missingTimeouts = 0;

    public float $avgDuration = 0;

    public int $totalUsers = 0;

    public float $completionRate = 0;

    public float $avgIntegrityScore = 0;

    public array $weeklyMetrics = [];

    public Collection $recentSessions;

    public Collection $recentActivity;

    public array $sessionsByStatus = [];

    public array $sessionsBySector = [];

    public string $dateFilter = 'today';

    public string $statusFilter = '';

    public string $sectorFilter = '';

    public string $userName = '';

    public string $userRole = '';

    public string $greeting = '';

    public string $userInitials = '';

    public function mount(MetricsService $metricsService): void
    {
        $this->recentSessions = collect();
        $this->recentActivity = collect();
        $this->loadStats($metricsService);
        $this->loadRecentSessions();
        $this->loadRecentActivity();
        $this->loadDistribution();
        $this->loadUserInfo();
    }

    public function loadUserInfo(): void
    {
        $user = Auth::user();
        $this->userName = $user->full_name ?? $user->name;
        $this->userRole = $user->role ?? 'member';
        $this->userInitials = strtoupper(substr($this->userName, 0, 2));

        $hour = now()->hour;
        $this->greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };
    }

    public function loadStats(MetricsService $metricsService): void
    {
        $summary = $metricsService->getSystemSummary();

        $this->totalRecords = DutySession::count();
        $this->todayCount = (int) $summary['today_count'];
        $this->activeNow = (int) $summary['active_sessions'];
        $this->missingTimeouts = DutySession::where('status', 'MISSING_TIMEOUT')->count();
        $this->avgDuration = (float) $summary['average_duration_minutes'];
        $this->totalUsers = (int) $summary['total_users'];
        $this->completionRate = (float) $summary['completion_rate'];
        $this->avgIntegrityScore = (float) $summary['avg_integrity_score'];

        $user = Auth::user();
        if ($user->role === 'member') {
            $this->weeklyMetrics = $metricsService->getWeeklyMetrics($user->id);
        }
    }

    public function loadRecentSessions(): void
    {
        $query = $this->filteredSessionQuery()->with('volunteer');

        $this->recentSessions = $query->orderByDesc('date')->orderByDesc('time_in')->take(10)->get();
    }

    public function loadRecentActivity(): void
    {
        $this->recentActivity = AuditLog::with('user')
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($log) => [
                'action' => $log->action,
                'description' => $log->description ?? $log->action,
                'user' => $log->user?->full_name ?? $log->user?->name ?? 'System',
                'time' => $log->created_at->diffForHumans(),
                'type' => $log->type ?? 'info',
            ]);
    }

    public function loadDistribution(): void
    {
        $query = $this->filteredSessionQuery();

        $this->sessionsByStatus = (clone $query)->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $this->sessionsBySector = (clone $query)->select('sector', DB::raw('count(*) as count'))
            ->whereNotNull('sector')
            ->groupBy('sector')
            ->pluck('count', 'sector')
            ->toArray();
    }

    public function updatedDateFilter(): void
    {
        $this->loadRecentSessions();
        $this->loadDistribution();
    }

    public function updatedStatusFilter(): void
    {
        $this->loadRecentSessions();
        $this->loadDistribution();
    }

    public function updatedSectorFilter(): void
    {
        $this->loadRecentSessions();
        $this->loadDistribution();
    }

    public function clearFilters(): void
    {
        $this->reset(['dateFilter', 'statusFilter', 'sectorFilter']);
        $this->dateFilter = 'today';
        $this->loadRecentSessions();
        $this->loadDistribution();
    }

    #[On('dashboard-refresh')]
    public function refresh(): void
    {
        $metrics = app(MetricsService::class);
        $this->loadStats($metrics);
        $this->loadRecentSessions();
        $this->loadRecentActivity();
        $this->loadDistribution();
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'sectors' => DutySession::query()->whereNotNull('sector')->distinct()->orderBy('sector')->pluck('sector'),
        ]);
    }

    private function filteredSessionQuery()
    {
        $query = DutySession::query();

        if ($this->dateFilter === 'today') {
            $query->whereDate('date', today());
        } elseif ($this->dateFilter === 'week') {
            $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($this->dateFilter === 'month') {
            $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->sectorFilter !== '') {
            $query->where('sector', $this->sectorFilter);
        }

        $user = Auth::user();
        if ($user?->role === 'member') {
            $query->where('volunteer_id', $user->id);
        }

        return $query;
    }
}
