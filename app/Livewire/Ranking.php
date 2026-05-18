<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\VolunteerMetrics;
use App\Services\MetricsService;
use Illuminate\Support\Collection;
use Livewire\Component;

class Ranking extends Component
{
    public string $sortBy = 'total_hours';

    public int $limit = 50;

    public string $search = '';

    public string $period = 'all';

    public bool $showScoringGuide = false;

    public Collection $rankings;

    public array $topThree = [];

    public function mount(MetricsService $metricsService): void
    {
        $this->rankings = collect();
        $this->loadRankings($metricsService);
    }

    public function loadRankings(MetricsService $metricsService): void
    {
        $allRankings = $metricsService->getVolunteerRankings($this->sortBy);

        if ($this->period === 'this_week') {
            $allRankings = $allRankings->filter(fn ($r) => $r->volunteer?->dutySessions()
                ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
                ->exists());
        } elseif ($this->period === 'this_month') {
            $allRankings = $allRankings->filter(fn ($r) => $r->volunteer?->dutySessions()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->exists());
        }

        if ($this->search) {
            $searchLower = strtolower($this->search);
            $allRankings = $allRankings->filter(fn ($r) => str_contains(strtolower($r->full_name), $searchLower));
        }

        $this->topThree = $allRankings->take(3)->values()->toArray();
        $this->rankings = $allRankings->take($this->limit);
    }

    public function updatedSearch(): void
    {
        $metrics = app(MetricsService::class);
        $this->loadRankings($metrics);
    }

    public function updatedSortBy(): void
    {
        $metrics = app(MetricsService::class);
        $this->loadRankings($metrics);
    }

    public function updatedPeriod(): void
    {
        $metrics = app(MetricsService::class);
        $this->loadRankings($metrics);
    }

    public function toggleScoringGuide(): void
    {
        $this->showScoringGuide = !$this->showScoringGuide;
    }

    public function getAchievements($totalMinutes): array
    {
        $hours = $totalMinutes / 60;
        $achievements = [];

        if ($hours >= 100) $achievements[] = ['label' => 'Century', 'icon' => '🏆', 'color' => 'text-yellow-600'];
        elseif ($hours >= 50) $achievements[] = ['label' => 'Veteran', 'icon' => '⭐', 'color' => 'text-amber-600'];
        elseif ($hours >= 25) $achievements[] = ['label' => 'Dedicated', 'icon' => '🔥', 'color' => 'text-orange-600'];
        elseif ($hours >= 10) $achievements[] = ['label' => 'Rising', 'icon' => '💪', 'color' => 'text-blue-600'];
        else $achievements[] = ['label' => 'Beginner', 'icon' => '🌱', 'color' => 'text-green-600'];

        return $achievements;
    }

    public function render()
    {
        return view('livewire.ranking', [
            'rankings' => $this->rankings,
            'topThree' => $this->topThree,
            'showScoringGuide' => $this->showScoringGuide,
        ]);
    }
}
