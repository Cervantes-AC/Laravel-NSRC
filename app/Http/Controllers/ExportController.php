<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DutySession;
use App\Models\User;
use App\Models\VolunteerMetrics;
use App\Services\ExportService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    public function __construct(
        protected ExportService $exportService,
        protected NotificationService $notificationService
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
        $user = Auth::user();
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

        if ($users->isEmpty()) {
            $this->notificationService->sendValidationNotification(
                $user,
                'export',
                'warning',
                'No accounts match the current filters. Export cancelled.'
            );

            return redirect()->route('admin.export.index')->with('warning', 'No accounts to export with current filters.');
        }

        $format = $request->get('format', 'csv');
        $filename = 'accounts-export-'.now()->format('Y-m-d-His');

        $this->notificationService->sendActionNotification($user, 'export', 'started', "Exporting {$users->count()} accounts as {$format}...", 'info');

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
            $scheduled = $this->exportService->scheduleExport($data, $filename, $format, $user->email);

            if ($scheduled) {
                $this->notificationService->sendExportNotification($user, 'accounts', 'scheduled', "{$users->count()} records");
                return redirect()->route('admin.export.index')->with('success', 'Export will be emailed when ready.');
            }

            $this->notificationService->sendExportNotification($user, 'accounts', 'failed', 'Failed to schedule export.');
            return redirect()->route('admin.export.index')->with('error', 'Failed to schedule export. Please try again.');
        }

        $this->notificationService->sendExportNotification($user, 'accounts', 'completed', "{$users->count()} records as {$format}");

        return $this->exportService->exportWithData($data, $filename, $format);
    }

    public function sessions(Request $request)
    {
        $user = Auth::user();
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

        if ($sessions->isEmpty()) {
            $this->notificationService->sendValidationNotification(
                $user,
                'export',
                'warning',
                'No sessions match the current filters. Export cancelled.'
            );

            return redirect()->route('admin.export.index')->with('warning', 'No sessions to export with current filters.');
        }

        $format = $request->get('format', 'csv');
        $filename = 'sessions-export-'.now()->format('Y-m-d-His');

        $this->notificationService->sendActionNotification($user, 'export', 'started', "Exporting {$sessions->count()} sessions as {$format}...", 'info');

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
            $scheduled = $this->exportService->scheduleExport($data, $filename, $format, $user->email);

            if ($scheduled) {
                $this->notificationService->sendExportNotification($user, 'sessions', 'scheduled', "{$sessions->count()} records");
                return redirect()->route('admin.export.index')->with('success', 'Export will be emailed when ready.');
            }

            $this->notificationService->sendExportNotification($user, 'sessions', 'failed', 'Failed to schedule export.');
            return redirect()->route('admin.export.index')->with('error', 'Failed to schedule export. Please try again.');
        }

        $this->notificationService->sendExportNotification($user, 'sessions', 'completed', "{$sessions->count()} records as {$format}");

        return $this->exportService->exportWithData($data, $filename, $format);
    }

    public function personnel(Request $request)
    {
        $user = Auth::user();
        $query = User::where('role', '!=', 'admin')->with('dutySessions', 'metrics');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->notificationService->sendValidationNotification(
                $user,
                'export',
                'warning',
                'No personnel match the current filters. Export cancelled.'
            );

            return redirect()->route('admin.export.index')->with('warning', 'No personnel to export with current filters.');
        }

        $format = $request->get('format', 'csv');
        $filename = 'personnel-export-'.now()->format('Y-m-d-His');

        $this->notificationService->sendActionNotification($user, 'export', 'started', "Exporting {$users->count()} personnel records as {$format}...", 'info');

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
            $scheduled = $this->exportService->scheduleExport($data, $filename, $format, $user->email);

            if ($scheduled) {
                $this->notificationService->sendExportNotification($user, 'personnel', 'scheduled', "{$users->count()} records");
                return redirect()->route('admin.export.index')->with('success', 'Export will be emailed when ready.');
            }

            $this->notificationService->sendExportNotification($user, 'personnel', 'failed', 'Failed to schedule export.');
            return redirect()->route('admin.export.index')->with('error', 'Failed to schedule export. Please try again.');
        }

        $this->notificationService->sendExportNotification($user, 'personnel', 'completed', "{$users->count()} records as {$format}");

        return $this->exportService->exportWithData($data, $filename, $format);
    }

    public function attendance(Request $request)
    {
        $user = Auth::user();
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

        if ($records->isEmpty()) {
            $this->notificationService->sendValidationNotification(
                $user,
                'export',
                'warning',
                'No attendance records match the current filters. Export cancelled.'
            );

            return redirect()->route('admin.export.index')->with('warning', 'No attendance records to export with current filters.');
        }

        $format = $request->get('format', 'csv');
        $filename = 'attendance-export-'.now()->format('Y-m-d-His');

        $this->notificationService->sendActionNotification($user, 'export', 'started', "Exporting {$records->count()} attendance records as {$format}...", 'info');

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
            $scheduled = $this->exportService->scheduleExport($data, $filename, $format, $user->email);

            if ($scheduled) {
                $this->notificationService->sendExportNotification($user, 'attendance', 'scheduled', "{$records->count()} records");
                return redirect()->route('admin.export.index')->with('success', 'Export will be emailed when ready.');
            }

            $this->notificationService->sendExportNotification($user, 'attendance', 'failed', 'Failed to schedule export.');
            return redirect()->route('admin.export.index')->with('error', 'Failed to schedule export. Please try again.');
        }

        $this->notificationService->sendExportNotification($user, 'attendance', 'completed', "{$records->count()} records as {$format}");

        return $this->exportService->exportWithData($data, $filename, $format);
    }
}
