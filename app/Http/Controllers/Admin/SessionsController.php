<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDutySessionRequest;
use App\Http\Requests\UpdateDutySessionRequest;
use App\Models\AuditLog;
use App\Models\DutySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', DutySession::class);

        return view('admin.sessions.index');
    }

    public function show(DutySession $session)
    {
        $this->authorize('view', $session);

        return view('admin.sessions.show', compact('session'));
    }

    public function create()
    {
        $this->authorize('create', DutySession::class);

        return view('admin.sessions.create');
    }

    public function store(StoreDutySessionRequest $request)
    {
        $this->authorize('create', DutySession::class);

        DutySession::create($request->validated());

        return redirect()->route('admin.sessions.index')->with('success', 'Duty session created successfully.');
    }

    public function edit(DutySession $session)
    {
        $this->authorize('update', $session);

        return view('admin.sessions.edit', compact('session'));
    }

    public function update(UpdateDutySessionRequest $request, DutySession $session)
    {
        $this->authorize('update', $session);

        $session->update($request->validated());

        return redirect()->route('admin.sessions.index')->with('success', 'Duty session updated successfully.');
    }

    public function destroy(DutySession $session)
    {
        $this->authorize('delete', $session);

        $session->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'OPERATIONS',
            'action' => 'DELETE_SESSION',
            'details' => "Soft deleted duty session ID: {$session->id} for {$session->full_name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.sessions.index')->with('success', 'Duty session deleted successfully.');
    }

    public function restore($id)
    {
        $session = DutySession::withTrashed()->findOrFail($id);
        $this->authorize('restore', $session);
        $session->restore();

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'OPERATIONS',
            'action' => 'RESTORE_SESSION',
            'details' => "Restored duty session ID: {$session->id} for {$session->full_name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.sessions.index')->with('success', 'Duty session restored successfully.');
    }
}
