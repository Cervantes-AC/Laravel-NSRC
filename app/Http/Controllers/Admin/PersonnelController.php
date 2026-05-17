<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PersonnelController extends Controller
{
    public function index()
    {
        $users = User::paginate(15);
        return view('admin.personnel.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('admin.personnel.show', compact('user'));
    }

    public function create()
    {
        return view('admin.personnel.create');
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'OPERATIONS',
            'action' => 'CREATE_USER',
            'details' => "Created user: {$user->full_name} (ID: {$user->id})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.personnel.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.personnel.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'OPERATIONS',
            'action' => 'UPDATE_USER',
            'details' => "Updated user: {$user->full_name} (ID: {$user->id})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.personnel.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->update(['status' => 'inactive']);

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'OPERATIONS',
            'action' => 'DEACTIVATE_USER',
            'details' => "Deactivated user: {$user->full_name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.personnel.index')->with('success', 'User deactivated successfully.');
    }
}
