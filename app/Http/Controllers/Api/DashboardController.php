<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DutySession;
use App\Models\User;
use App\Services\MetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function data(MetricsService $metricsService): JsonResponse
    {
        $summary = $metricsService->getSystemSummary();
        $user = Auth::user();

        $dateFilter = request('dateFilter', 'today');
        $statusFilter = request('statusFilter', '');
        $sectorFilter = request('sectorFilter', '');

        $query = $this->filteredSessionQuery($dateFilter, $statusFilter, $sectorFilter, $user);

        $sessionsByStatus = (clone $query)->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $sessionsBySector = (clone $query)->select('sector', DB::raw('count(*) as count'))
            ->whereNotNull('sector')
            ->groupBy('sector')
            ->pluck('count', 'sector')
            ->toArray();

        $recentSessions = $query->with('volunteer')->orderByDesc('date')->orderByDesc('time_in')->take(10)->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'full_name' => $s->volunteer?->full_name ?? $s->full_name,
                'date' => $s->date?->format('M d, Y'),
                'time_in' => $s->time_in?->format('h:i A'),
                'time_out' => $s->time_out?->format('h:i A'),
                'duration_minutes' => $s->duration_minutes,
                'status' => $s->status,
            ];
        });

        $recentActivity = AuditLog::with('user')->latest()->take(6)->get()->map(fn ($log) => [
            'action' => $log->action,
            'description' => $log->description ?? $log->action,
            'user' => $log->user?->full_name ?? $log->user?->name ?? 'System',
            'time' => $log->created_at->diffForHumans(),
            'type' => $log->type ?? 'info',
        ]);

        $weeklyMetrics = [];
        $hasActiveSession = false;
        if ($user->role === 'member') {
            $weeklyMetrics = $metricsService->getWeeklyMetrics($user->id);
            $hasActiveSession = DutySession::where('volunteer_id', $user->id)->whereNull('time_out')->exists();
        }

        $hour = now()->hour;
        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };

        $userName = $user->full_name ?? $user->name;
        $userInitials = strtoupper(substr($userName, 0, 2));

        $hasActiveSession = $user->role === 'member' && DutySession::where('volunteer_id', $user->id)->whereNull('time_out')->exists();

        return response()->json([
            'totalRecords' => DutySession::count(),
            'todayCount' => (int) ($summary['today_count'] ?? 0),
            'activeNow' => (int) ($summary['active_sessions'] ?? 0),
            'missingTimeouts' => DutySession::where('status', 'MISSING_TIMEOUT')->count(),
            'avgDuration' => (float) ($summary['average_duration_minutes'] ?? 0),
            'totalUsers' => (int) ($summary['total_users'] ?? 0),
            'completionRate' => (float) ($summary['completion_rate'] ?? 0),
            'avgIntegrityScore' => (float) ($summary['avg_integrity_score'] ?? 0),
            'hasActiveSession' => $hasActiveSession,
            'weeklyMetrics' => $weeklyMetrics,
            'recentSessions' => $recentSessions,
            'recentActivity' => $recentActivity,
            'sessionsByStatus' => $sessionsByStatus,
            'sessionsBySector' => $sessionsBySector,
            'greeting' => $greeting,
            'userName' => $userName,
            'userInitials' => $userInitials,
            'userRole' => $user->role,
            'sectors' => DutySession::query()->whereNotNull('sector')->distinct()->orderBy('sector')->pluck('sector'),
        ]);
    }

    private function filteredSessionQuery(string $dateFilter, string $statusFilter, string $sectorFilter, $user)
    {
        $query = DutySession::query();

        if ($dateFilter === 'today') {
            $query->whereDate('date', today());
        } elseif ($dateFilter === 'week') {
            $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($dateFilter === 'month') {
            $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
        }

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if ($sectorFilter !== '') {
            $query->where('sector', $sectorFilter);
        }

        if ($user?->role === 'member') {
            $query->where('volunteer_id', $user->id);
        }

        return $query;
    }
}
