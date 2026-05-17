<?php

namespace App\Livewire;

use App\Services\MetricsService;
use Livewire\Component;

class Ranking extends Component
{
    public string $sortBy = 'total_hours';

    public int $limit = 20;

    public $rankings;

    public function mount(MetricsService $metricsService): void
    {
        $this->loadRankings($metricsService);
    }

    public function loadRankings(MetricsService $metricsService): void
    {
        $this->rankings = $metricsService->getVolunteerRankings()
            ->take($this->limit);
    }

    public function render()
    {
        return view('livewire.ranking', ['rankings' => $this->rankings])
            ->layout('components.layouts.app');
    }
}
