<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RankingsController extends Controller
{
    public function index(Request $request, MetricsService $metricsService): JsonResponse
    {
        $sortBy = $request->input('sortBy', 'total_hours');
        $search = $request->input('search', '');
        $period = $request->input('period', 'all');
        $perPage = (int) $request->input('perPage', 15);
        $page = (int) $request->input('page', 1);

        $allRankings = $metricsService->getVolunteerRankings($sortBy);

        if ($period === 'this_week') {
            $allRankings = $allRankings->filter(fn ($r) => $r->volunteer?->dutySessions()
                ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->exists());
        } elseif ($period === 'this_month') {
            $allRankings = $allRankings->filter(fn ($r) => $r->volunteer?->dutySessions()
                ->whereMonth('date', now()->month)->whereYear('date', now()->year)->exists());
        }

        if ($search) {
            $searchLower = strtolower($search);
            $allRankings = $allRankings->filter(fn ($r) => str_contains(strtolower($r->full_name), $searchLower));
        }

        $allRankings = $allRankings->values();
        $total = $allRankings->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));

        $topThree = $allRankings->take(3)->values()->toArray();

        $offset = ($page - 1) * $perPage;
        $rankings = $allRankings->slice($offset, $perPage)->values()->toArray();

        return response()->json([
            'topThree' => $topThree,
            'rankings' => $rankings,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'total' => $total,
        ]);
    }
}
