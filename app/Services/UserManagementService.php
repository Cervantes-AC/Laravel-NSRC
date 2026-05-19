<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\DutySession;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UserManagementService
{
    public function impersonateUser(User $user): void
    {
        $originalUser = Auth::user();

        Session::put('impersonated_by', $originalUser?->id);
        Auth::login($user);

        AuditLog::create([
            'user_id' => $originalUser?->id,
            'full_name' => $originalUser?->full_name ?? 'System',
            'type' => 'SECURITY',
            'action' => 'IMPERSONATE_USER',
            'details' => "Impersonated user: {$user->full_name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        Log::info("User {$originalUser?->id} impersonated user {$user->id}");
    }

    public function forceLogoutUser(User $user): void
    {
        if ($user->id === Auth::id()) {
            return;
        }

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'SECURITY',
            'action' => 'FORCE_LOGOUT',
            'details' => "Force logged out user: {$user->full_name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        Log::info("User {$user->id} was force logged out by ".Auth::id());
    }

    public function getUserLoginHistory(User $user): Collection
    {
        return AuditLog::where('user_id', $user->id)
            ->where('action', 'LOGIN')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getUserActivityAnalytics(User $user): array
    {
        $sessions = DutySession::where('volunteer_id', $user->id)->get();
        $auditLogs = AuditLog::where('user_id', $user->id)->get();

        $totalSessions = $sessions->count();
        $totalMinutes = $sessions->sum('duration_minutes');
        $avgDuration = $totalSessions > 0 ? round($totalMinutes / $totalSessions, 2) : 0;

        $sessionsByStatus = $sessions->groupBy('status')
            ->map(fn ($group) => $group->count())
            ->toArray();

        $recentActivity = $auditLogs->sortByDesc('created_at')
            ->take(10)
            ->values()
            ->toArray();

        return [
            'user_id' => $user->id,
            'full_name' => $user->full_name,
            'role' => $user->role,
            'total_sessions' => $totalSessions,
            'total_minutes' => $totalMinutes,
            'average_duration_minutes' => $avgDuration,
            'sessions_by_status' => $sessionsByStatus,
            'total_audit_logs' => $auditLogs->count(),
            'recent_activity' => $recentActivity,
        ];
    }

    public function getSystemWideAnalytics(): array
    {
        $totalUsers = User::count();
        $activeToday = User::whereDate('last_login_at', today())->count();
        $activeThisWeek = User::where('last_login_at', '>=', now()->subWeek())->count();
        $newThisMonth = User::where('created_at', '>=', now()->subMonth())->count();
        $pendingApproval = User::where('status', 'pending')->count();
        $suspendedAccounts = User::where('status', 'suspended')->count();

        $mostActiveUsers = User::withCount('dutySessions')
            ->orderByDesc('duty_sessions_count')
            ->take(5)
            ->get(['id', 'full_name', 'email', 'role', 'last_login_at'])
            ->toArray();

        $roleDistribution = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        $statusDistribution = User::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $recentRegistrations = User::where('created_at', '>=', now()->subDays(7))
            ->orderByDesc('created_at')
            ->take(10)
            ->get(['id', 'full_name', 'email', 'role', 'status', 'created_at'])
            ->toArray();

        $recentLogins = User::whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subDays(7))
            ->orderByDesc('last_login_at')
            ->take(10)
            ->get(['id', 'full_name', 'email', 'last_login_at'])
            ->toArray();

        return [
            'total_users' => $totalUsers,
            'active_today' => $activeToday,
            'active_this_week' => $activeThisWeek,
            'new_this_month' => $newThisMonth,
            'pending_approval' => $pendingApproval,
            'suspended_accounts' => $suspendedAccounts,
            'most_active_users' => $mostActiveUsers,
            'role_distribution' => $roleDistribution,
            'status_distribution' => $statusDistribution,
            'recent_registrations' => $recentRegistrations,
            'recent_logins' => $recentLogins,
        ];
    }
}
