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

        Log::info("User {$user->id} was force logged out by " . Auth::id());
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
            ->map(fn($group) => $group->count())
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
}
