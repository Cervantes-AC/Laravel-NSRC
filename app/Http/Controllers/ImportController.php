<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Models\Attendance;
use App\Models\DutySession;
use App\Models\User;
use App\Models\VolunteerMetrics;
use App\Services\DataExportService;
use App\Services\DutyEngine;
use App\Services\ImportService;
use App\Services\MetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportController extends Controller
{
    public function __construct(
        protected ImportService $importService,
    ) {}

    public function index()
    {
        return view('admin.import.index');
    }

    public function export(string $type)
    {
        $data = match ($type) {
            'personnel' => User::where('role', '!=', 'admin')->get(['id', 'full_name', 'email', 'role', 'status', 'created_at']),
            'sessions' => DutySession::with('volunteer')->get()->map(fn ($s) => [
                'id' => $s->id,
                'full_name' => $s->full_name,
                'date' => $s->date?->format('Y-m-d'),
                'time_in' => $s->time_in?->format('H:i:s'),
                'time_out' => $s->time_out?->format('H:i:s'),
                'duration_minutes' => $s->duration_minutes,
                'status' => $s->status,
                'location' => $s->location,
                'sector' => $s->sector,
            ]),
            default => collect([]),
        };

        if ($data->isEmpty()) {
            return redirect()->route('admin.import.index')->with('warning', "No $type data to export.");
        }

        $filename = $type . '-export-' . now()->format('Y-m-d-His');
        return app(DataExportService::class)->exportToCSV(collect($data), $filename);
    }

    public function preview(ImportRequest $request)
    {
        $result = $this->importService->previewImportData($request->file('file'));

        if (!empty($result['errors'])) {
            return back()->withErrors($result['errors'])->withInput();
        }

        return view('admin.import.preview', [
            'preview' => $result['preview'],
            'total_rows' => $result['total_rows'],
            'filename' => $request->file('file')->getClientOriginalName(),
        ]);
    }

    public function process(ImportRequest $request)
    {
        $result = $this->importService->processImport($request->file('file'));

        if (!empty($result['errors'])) {
            return redirect()->route('admin.import.index')
                ->with('warning', "Imported {$result['success']} records with {$result['failed']} failures.");
        }

        if ($result['success'] > 0) {
            $dutyEngine = app(DutyEngine::class);
            $logs = Attendance::query()->orderBy('date_time')->get();
            $sessions = $dutyEngine->processDutyLogs($logs);

            foreach ($sessions as $session) {
                $volunteerId = \App\Models\User::where('full_name', $session->full_name)->value('id');
                $session->volunteer_id = $volunteerId;

                $attributes = $session->getAttributes();
                $attributes['trace_id'] = 'IMP-' . strtoupper(substr(md5($session->full_name . ($session->date ?? now()) . ($session->time_in ?? now())), 0, 8));

                $match = \App\Models\DutySession::query()
                    ->where('full_name', $session->full_name)
                    ->whereDate('date', $session->date)
                    ->when($session->time_in, fn ($q) => $q->where('time_in', $session->time_in))
                    ->first();

                if ($match) {
                    $match->update($attributes);
                } else {
                    \App\Models\DutySession::create($attributes);
                }
            }

            VolunteerMetrics::query()->delete();
            app(MetricsService::class)->calculateVolunteerMetrics(\App\Models\DutySession::query()->get());
        }

        return redirect()->route('admin.import.index')
            ->with('success', "Successfully imported {$result['success']} records and processed into duty sessions.");
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance-import-template.csv"',
        ];

        $columns = ['timestamp', 'full_name', 'attendance'];

        $callback = function () use ($columns) {
            $handle = fopen('php://output', 'w+');
            fputcsv($handle, $columns);
            fputcsv($handle, [now()->format('n/j/Y H:i:s'), 'Juan Dela Cruz', 'Time in']);
            fputcsv($handle, [now()->addHours(4)->format('n/j/Y H:i:s'), 'Juan Dela Cruz', 'Time out']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
