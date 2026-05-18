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
        return redirect()
            ->route('admin.attendance.index')
            ->with('warning', 'Attendance summaries are auto-generated from attendance logs. Use file import or manual entry instead.');
    }

    public function store(StoreDutySessionRequest $request)
    {
        return redirect()
            ->route('admin.attendance.index')
            ->with('warning', 'Manual attendance entry is disabled. Use file import to add attendance data.');
    }

    public function edit(DutySession $session)
    {
        return redirect()
            ->route('admin.attendance.show', $session)
            ->with('warning', 'Attendance summaries are generated from attendance logs and cannot be edited here.');
    }

    public function update(UpdateDutySessionRequest $request, DutySession $session)
    {
        return redirect()
            ->route('admin.attendance.show', $session)
            ->with('warning', 'Manual attendance updates are disabled. Use file import to update attendance data.');
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

        return redirect()->route('admin.attendance.index')->with('success', 'Attendance summary hidden successfully.');
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

        return redirect()->route('admin.attendance.index')->with('success', 'Attendance summary restored successfully.');
    }
}
