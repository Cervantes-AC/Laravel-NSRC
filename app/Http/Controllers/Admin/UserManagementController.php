<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function __construct(
        private readonly UserManagementService $userManagement,
    ) {}

    public function impersonate(User $user): RedirectResponse
    {
        $this->userManagement->impersonateUser($user);

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'Admin',
            'type' => 'SECURITY',
            'action' => 'IMPERSONATE_USER',
            'details' => "Admin impersonating user #{$user->id} ({$user->email})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route($user->role === 'admin' ? 'admin.dashboard' : 'member.dashboard')
            ->with('success', "Now viewing as {$user->full_name}.");
    }

    public function stopImpersonating(): RedirectResponse
    {
        $adminId = session('impersonated_by');
        if ($adminId) {
            Auth::loginUsingId($adminId);
            session()->forget('impersonated_by');
        }

        return redirect()->route('admin.accounts.index')->with('success', 'Returned to admin account.');
    }

    public function forceLogout(User $user): RedirectResponse
    {
        $this->userManagement->forceLogoutUser($user);

        return back()->with('success', "Forced logout for {$user->full_name}.");
    }

    public function loginHistory(User $user)
    {
        $history = $this->userManagement->getUserLoginHistory($user);
        $analytics = $this->userManagement->getUserActivityAnalytics($user);

        return view('admin.accounts.history', compact('user', 'history', 'analytics'));
    }
}
