<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RankingService
{
    public function __construct(
        private readonly MetricsService $metrics,
    ) {}

    public function getRankings(string $sortBy = 'total_minutes', int $limit = 50): Collection
    {
        return Cache::remember("volunteer_rankings_{$sortBy}_{$limit}", 300, function () use ($sortBy, $limit) {
            $rankings = $this->metrics->getVolunteerRankings();

            return $rankings->sortByDesc($sortBy)->take($limit)->values();
        });
    }

    public function clearCache(): void
    {
        Cache::flush();
    }
}
