<?php

namespace App\Http\Controllers\Admin;

use App\Events\AccountDeleted;
use App\Http\Controllers\Controller;
use App\Mail\AccountApproved;
use App\Mail\AccountRejected;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\CrudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AccountsController extends Controller
{
    public function __construct(
        private CrudService $crudService,
    ) {}

    public function index()
    {
        $users = User::all();

        return view('admin.accounts.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.accounts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,member'],
            'status' => ['required', 'in:active,inactive,suspended,pending,rejected'],
            'school_id' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'college' => ['nullable', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'string', 'max:50'],
            'personal_contact_number' => ['nullable', 'string', 'max:20'],
            'current_address' => ['nullable', 'string', 'max:255'],
            'home_address' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = $this->crudService->create(User::class, $validated, [
            'action' => 'CREATE_ACCOUNT',
            'type' => 'REGISTRY',
        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.accounts.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $submittedLock = $request->input('lock_version');
        if ($submittedLock !== null && $this->crudService->hasConflict($user, (int) $submittedLock)) {
            return back()->withErrors([
                'lock_version' => 'This record was modified by another user. Please refresh and try again.',
            ])->withInput();
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,member'],
            'status' => ['required', 'in:active,inactive,suspended,pending,rejected'],
            'school_id' => ['nullable', 'string', 'max:255'],
            'personal_contact_number' => ['nullable', 'string', 'max:255'],
            'current_address' => ['nullable', 'string', 'max:255'],
            'home_address' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $validated['lock_version'] = $user->lock_version + 1;
        $user->update($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'REGISTRY',
            'action' => 'UPDATE_ACCOUNT',
            'details' => "Updated account for: {$user->full_name} (ID: {$user->id})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $userId = $user->id;
        $userFullName = $user->full_name;
        $userEmail = $user->email;

        $cascadeWarning = $this->crudService->getCascadeWarning($user);

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'REGISTRY',
            'action' => 'DELETE_ACCOUNT',
            'details' => "Soft deleted account for: {$userFullName} (ID: {$userId}, Email: {$userEmail})"
                . ($cascadeWarning ? " | {$cascadeWarning}" : ''),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        event(new AccountDeleted($user));

        $user->delete();

        $message = 'Account deleted successfully.';
        if ($cascadeWarning) {
            $message .= ' ' . $cascadeWarning;
        }

        return redirect()->route('admin.accounts.index')->with('success', $message);
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $user->restore();

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'REGISTRY',
            'action' => 'RESTORE_ACCOUNT',
            'details' => "Restored account for: {$user->full_name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Account restored successfully.');
    }

    public function approve(User $user)
    {
        $user->update(['status' => 'active']);

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'REGISTRY',
            'action' => 'APPROVE_ACCOUNT',
            'details' => "Approved account for: {$user->full_name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        if ($user->email_notifications_enabled ?? true) {
            Mail::to($user->email)->send(new AccountApproved($user));
        }

        return redirect()->route('admin.accounts.index')->with('success', 'Account approved successfully.');
    }

    public function reject(User $user)
    {
        $reason = request()->input('reason');
        $user->update(['status' => 'rejected']);

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'REGISTRY',
            'action' => 'REJECT_ACCOUNT',
            'details' => "Rejected account for: {$user->full_name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        if ($user->email_notifications_enabled ?? true) {
            Mail::to($user->email)->send(new AccountRejected($user, $reason));
        }

        return redirect()->route('admin.accounts.index')->with('success', 'Account rejected successfully.');
    }

    public function suspend(User $user)
    {
        $user->update(['status' => 'suspended']);

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'SECURITY',
            'action' => 'SUSPEND_ACCOUNT',
            'details' => "Suspended account for: {$user->full_name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Account suspended successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:approve,reject,delete',
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();
        $count = $users->count();

        foreach ($users as $user) {
            match ($request->action) {
                'approve' => $user->update(['status' => 'active']),
                'reject' => $user->update(['status' => 'rejected']),
                'delete' => $user->delete(),
            };

            AuditLog::create([
                'user_id' => Auth::id(),
                'full_name' => Auth::user()?->full_name ?? 'System',
                'type' => 'OPERATIONS',
                'action' => 'BULK_' . strtoupper($request->action) . '_ACCOUNTS',
                'details' => "Bulk {$request->action} applied to user ID: {$user->id} ({$user->full_name})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);
        }

        return redirect()->route('admin.accounts.index')->with('success', "Bulk {$request->action} completed for {$count} account(s).");
    }

    public function analytics(): View
    {
        $analytics = [
            'total_users' => User::count(),
            'active_today' => User::whereHas('dutySessions', fn ($q) => $q->whereDate('date', today()))->count(),
            'active_this_week' => User::whereHas('dutySessions', fn ($q) => $q->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]))->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'pending_approval' => User::where('status', 'pending')->count(),
            'suspended_accounts' => User::where('status', 'suspended')->count(),
            'role_distribution' => User::selectRaw('role, COUNT(*) as count')->groupBy('role')->pluck('count', 'role')->toArray(),
            'status_distribution' => User::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status')->toArray(),
            'most_active_users' => User::withCount('dutySessions')
                ->orderByDesc('duty_sessions_count')
                ->limit(10)
                ->get()
                ->map(fn ($u) => [
                    'full_name' => $u->full_name,
                    'email' => $u->email,
                    'role' => $u->role,
                    'duty_sessions_count' => $u->duty_sessions_count,
                    'last_login_at' => $u->last_login_at,
                ])
                ->toArray(),
            'recent_registrations' => User::where('created_at', '>=', now()->subWeek())
                ->latest()
                ->limit(10)
                ->get(['full_name', 'email', 'created_at'])
                ->toArray(),
            'recent_logins' => User::whereNotNull('last_login_at')
                ->where('last_login_at', '>=', now()->subWeek())
                ->latest('last_login_at')
                ->limit(10)
                ->get(['full_name', 'email', 'last_login_at'])
                ->toArray(),
        ];

        return view('admin.accounts.analytics', compact('analytics'));
    }
}
