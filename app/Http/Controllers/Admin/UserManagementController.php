<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\DataExportService;
use App\Services\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function __construct(
        private readonly UserManagementService $userManagement,
    ) {}

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderByDesc('id')->paginate($request->input('per_page', 15));

        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'pending' => User::where('status', 'pending')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'member' => User::where('role', 'member')->count(),
        ];

        return view('admin.accounts.index', compact('users', 'stats'));
    }

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

    public function bulkExport(DataExportService $exportService)
    {
        $users = User::select('name', 'email', 'full_name', 'role', 'status', 'school_id', 'college', 'major', 'year_level', 'created_at', 'last_login_at')
            ->get()
            ->map(fn ($u) => collect([
                'Username' => $u->name,
                'Email' => $u->email,
                'Full Name' => $u->full_name,
                'Role' => $u->role,
                'Status' => $u->status,
                'School ID' => $u->school_id,
                'College' => $u->college,
                'Major' => $u->major,
                'Year Level' => $u->year_level,
                'Created' => $u->created_at->format('Y-m-d'),
                'Last Login' => $u->last_login_at?->format('Y-m-d H:i') ?? 'Never',
            ]));

        return $exportService->exportToCSV($users, 'users_export_'.now()->format('Ymd'));
    }

    public function userActivityAnalytics()
    {
        $analytics = $this->userManagement->getSystemWideAnalytics();

        return view('admin.accounts.analytics', compact('analytics'));
    }
}
