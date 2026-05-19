<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\CrudService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonnelController extends Controller
{
    public function __construct(
        private CrudService $crudService,
    ) {}

    public function index(Request $request)
    {
        $query = User::query();

        $trashed = $request->input('trashed');
        if ($trashed === 'only') {
            $query->onlyTrashed();
        } elseif ($trashed === 'with') {
            $query->withTrashed();
        }

        $users = $query->paginate(15);

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
        $user = $this->crudService->create(User::class, $request->validated(), [
            'action' => 'CREATE_USER',
            'type' => 'OPERATIONS',
        ]);

        return redirect()->route('admin.personnel.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.personnel.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $submittedLock = $request->input('lock_version');
        if ($submittedLock !== null && $this->crudService->hasConflict($user, (int) $submittedLock)) {
            return back()->withErrors([
                'lock_version' => 'This record was modified by another user. Please refresh and try again.',
            ])->withInput();
        }

        $data = $request->validated();
        $data['lock_version'] = $user->lock_version + 1;

        $user->update($data);

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
        $this->crudService->delete($user, [
            'action' => 'DEACTIVATE_USER',
            'type' => 'OPERATIONS',
        ]);

        return redirect()->route('admin.personnel.index')->with('success', 'User deactivated successfully.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'OPERATIONS',
            'action' => 'RESTORE_USER',
            'details' => "Restored user: {$user->full_name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.personnel.index')->with('success', 'User restored successfully.');
    }
}
