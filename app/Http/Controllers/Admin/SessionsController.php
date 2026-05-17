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
        $query = DutySession::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sector')) {
            $query->where('sector', $request->sector);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }

        $sessions = $query->latest()->paginate(15);

        return view('admin.sessions.index', compact('sessions'));
    }

    public function show(DutySession $session)
    {
        return view('admin.sessions.show', compact('session'));
    }

    public function create()
    {
        return view('admin.sessions.create');
    }

    public function store(StoreDutySessionRequest $request)
    {
        DutySession::create($request->validated());

        return redirect()->route('admin.sessions.index')->with('success', 'Duty session created successfully.');
    }

    public function edit(DutySession $session)
    {
        return view('admin.sessions.edit', compact('session'));
    }

    public function update(UpdateDutySessionRequest $request, DutySession $session)
    {
        $session->update($request->validated());

        return redirect()->route('admin.sessions.index')->with('success', 'Duty session updated successfully.');
    }

    public function destroy(DutySession $session)
    {
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
