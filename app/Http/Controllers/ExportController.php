<?php

namespace App\Http\Controllers;

use App\Models\DutySession;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\VolunteerMetrics;
use App\Services\ExportService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function __construct(
        protected ExportService $exportService,
        protected NotificationService $notifications
    ) {}

    public function index()
    {
        $stats = [
            'accounts' => User::count(),
            'sessions' => DutySession::count(),
            'personnel' => User::where('role', '!=', 'admin')->count(),
            'attendance' => Attendance::count(),
            'metrics' => VolunteerMetrics::count(),
        ];

        return view('admin.export.index', compact('stats'));
    }

    public function accounts(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get(['id', 'full_name', 'name', 'email', 'role', 'status', 'created_at']);

        $format = $request->get('format', 'csv');
        $filename = 'accounts-export-' . now()->format('Y-m-d-His');

        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->full_name ?? $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'registered' => $user->created_at->format('Y-m-d H:i:s'),
            ];
        });

        if ($request->boolean('send_email')) {
            $this->exportService->scheduleExport($data, $filename, $format);
            return redirect()->route('admin.export.index')->with('success', 'Export will be emailed when ready.');
        }

        return $this->exportService->exportWithData($data, $filename, $format);
    }

    public function sessions(Request $request)
    {
        $query = DutySession::query()->with('volunteer');

        if ($request->dateFrom) {
            $query->whereDate('date', '>=', $request->dateFrom);
        }
        if ($request->dateTo) {
            $query->whereDate('date', '<=', $request->dateTo);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->location) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        $sessions = $query->orderByDesc('date')->get();

        $format = $request->get('format', 'csv');
        $filename = 'sessions-export-' . now()->format('Y-m-d-His');

        $data = $sessions->map(function ($s) {
            return [
                'id' => $s->id,
                'full_name' => $s->full_name,
                'date' => $s->date?->format('Y-m-d'),
                'time_in' => $s->time_in?->format('H:i:s'),
                'time_out' => $s->time_out?->format('H:i:s'),
                'duration_minutes' => $s->duration_minutes,
                'status' => $s->status,
                'location' => $s->location,
                'sector' => $s->sector,
                'integrity_score' => $s->integrity_score,
            ];
        });

        if ($request->boolean('send_email')) {
            $this->exportService->scheduleExport($data, $filename, $format);
            return redirect()->route('admin.export.index')->with('success', 'Export will be emailed when ready.');
        }

        return $this->exportService->exportWithData($data, $filename, $format);
    }

    public function personnel(Request $request)
    {
        $query = User::where('role', '!=', 'admin')->with('dutySessions', 'metrics');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        $format = $request->get('format', 'csv');
        $filename = 'personnel-export-' . now()->format('Y-m-d-His');

        $data = $users->map(function ($u) {
            return [
                'id' => $u->id,
                'full_name' => $u->full_name ?? $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'total_sessions' => $u->dutySessions->count(),
                'total_minutes' => $u->dutySessions->sum('duration_minutes'),
                'avg_session_duration' => $u->dutySessions->count() > 0 ? round($u->dutySessions->sum('duration_minutes') / $u->dutySessions->count(), 2) : 0,
            ];
        });

        if ($request->boolean('send_email')) {
            $this->exportService->scheduleExport($data, $filename, $format);
            return redirect()->route('admin.export.index')->with('success', 'Export will be emailed when ready.');
        }

        return $this->exportService->exportWithData($data, $filename, $format);
    }

    public function attendance(Request $request)
    {
        $query = Attendance::query();

        if ($request->dateFrom) {
            $query->whereDate('date_time', '>=', $request->dateFrom);
        }
        if ($request->dateTo) {
            $query->whereDate('date_time', '<=', $request->dateTo);
        }
        if ($request->filled('full_name')) {
            $query->where('full_name', 'like', "%{$request->full_name}%");
        }

        $records = $query->orderByDesc('date_time')->get();

        $format = $request->get('format', 'csv');
        $filename = 'attendance-export-' . now()->format('Y-m-d-His');

        $data = $records->map(function ($record) {
            return [
                'full_name' => $record->full_name,
                'attendance' => $record->attendance,
                'date_time' => $record->date_time,
                'location' => $record->location,
                'shift_type' => $record->shift_type,
                'source_signature' => $record->source_signature,
                'source_payload' => is_array($record->source_payload) ? json_encode($record->source_payload) : $record->source_payload,
            ];
        });

        if ($request->boolean('send_email')) {
            $this->exportService->scheduleExport($data, $filename, $format);
            return redirect()->route('admin.export.index')->with('success', 'Export will be emailed when ready.');
        }

        return $this->exportService->exportWithData($data, $filename, $format);
    }
}
