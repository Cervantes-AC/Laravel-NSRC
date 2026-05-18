<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountApproved;
use App\Mail\AccountRejected;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class AccountsController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.accounts.index', compact('users'));
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

    public function edit(User $user)
    {
        return view('admin.accounts.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
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

        $user->update($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'REGISTRY',
            'action' => 'UPDATE_ACCOUNT',
            'details' => "Updated account for: {$user->full_name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Account updated successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:approve,reject,delete',
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();

        foreach ($users as $user) {
            match ($request->action) {
                'approve' => $user->update(['status' => 'active']),
                'reject' => $user->update(['status' => 'rejected']),
                'delete' => $user->update(['status' => 'inactive']),
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

        return redirect()->route('admin.accounts.index')->with('success', 'Bulk action completed successfully.');
    }
}
