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
use App\Services\NameNormalizationService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportController extends Controller
{
    public function __construct(
        protected ImportService $importService,
        protected NotificationService $notificationService
    ) {}

    public function index()
    {
        return view('admin.import.index');
    }

    public function export(string $type)
    {
        $data = match ($type) {
            'personnel' => User::where('role', '!=', 'admin')->get(['id', 'full_name', 'email', 'role', 'status', 'created_at']),
            'accounts' => User::all(['id', 'name', 'full_name', 'email', 'role', 'status', 'two_factor_enabled', 'created_at']),
            'sessions' => DutySession::with('volunteer')->get()->map(fn ($s) => [
                'full_name' => $s->full_name,
                'date' => $s->date?->format('Y-m-d'),
                'time_in' => $s->time_in?->format('H:i:s'),
                'time_out' => $s->time_out?->format('H:i:s'),
                'duration_minutes' => $s->duration_minutes,
                'status' => $s->status,
                'location' => $s->location,
                'sector' => $s->sector,
            ]),
            'attendance' => Attendance::all()->map(fn ($a) => [
                'full_name' => $a->full_name,
                'attendance' => $a->attendance,
                'date_time' => $a->date_time,
                'location' => $a->location,
                'shift_type' => $a->shift_type,
                'source_signature' => $a->source_signature,
                'source_payload' => is_array($a->source_payload) ? json_encode($a->source_payload) : $a->source_payload,
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
            $this->notificationService->sendValidationNotification(
                Auth::user(),
                'import',
                'error',
                'File validation failed: ' . implode(', ', $result['errors'])
            );
            return back()->withErrors($result['errors'])->withInput();
        }

        $this->notificationService->sendValidationNotification(
            Auth::user(),
            'import',
            'success',
            'File validated successfully. ' . $result['total_rows'] . ' rows ready for import.'
        );

        return view('admin.import.preview', [
            'preview' => $result['preview'],
            'total_rows' => $result['total_rows'],
            'filename' => $request->file('file')->getClientOriginalName(),
        ]);
    }

    public function process(ImportRequest $request)
    {
        $importType = $request->input('import_type', 'sessions');

        if ($importType === 'personnel') {
            return $this->processPersonnelImport($request);
        }

        if ($importType === 'accounts') {
            return $this->processAccountsImport($request);
        }

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
                $volunteerId = $this->resolveVolunteerId($session->full_name);
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

    protected function processPersonnelImport(ImportRequest $request)
    {
        $file = $request->file('file');
        $validation = $this->importService->validateImportFile($file);

        if (!$validation['valid']) {
            return redirect()->route('admin.import.index')
                ->with('warning', implode(', ', $validation['errors']));
        }

        $rows = $this->importService->parseFile($file);
        $success = 0;
        $skipped = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            try {
                $email = trim((string) ($row['email'] ?? ''));
                $fullName = trim((string) ($row['full_name'] ?? $row['name'] ?? ''));
                $name = trim((string) ($row['name'] ?? $fullName));

                if (empty($fullName)) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": Missing full_name";
                    continue;
                }

                $existingUser = null;

                if (!empty($email)) {
                    $existingUser = User::where('email', $email)->first();
                }

                if (!$existingUser) {
                    $existingUser = User::where('full_name', $fullName)->first();
                }

                if ($existingUser) {
                    $skipped++;
                    continue;
                }

                User::create([
                    'name' => $name ?: $fullName,
                    'full_name' => $fullName,
                    'email' => $email ?: strtolower(str_replace(' ', '.', $fullName)) . '@import.local',
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
                    'role' => trim((string) ($row['role'] ?? 'member')),
                    'status' => trim((string) ($row['status'] ?? 'active')),
                    'email_verified_at' => now(),
                ]);

                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        $message = "Imported {$success} personnel records.";
        if ($skipped > 0) {
            $message .= " {$skipped} duplicates skipped.";
        }
        if ($failed > 0) {
            $message .= " {$failed} failures.";
        }

        return redirect()->route('admin.import.index')
            ->with($failed > 0 ? 'warning' : 'success', $message);
    }

    protected function processAccountsImport(ImportRequest $request)
    {
        $file = $request->file('file');
        $validation = $this->importService->validateImportFile($file);

        if (!$validation['valid']) {
            return redirect()->route('admin.import.index')
                ->with('warning', implode(', ', $validation['errors']));
        }

        $rows = $this->importService->parseFile($file);
        $success = 0;
        $skipped = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            try {
                $email = trim((string) ($row['email'] ?? ''));
                $fullName = trim((string) ($row['full_name'] ?? $row['name'] ?? ''));
                $name = trim((string) ($row['name'] ?? $fullName));
                $role = trim((string) ($row['role'] ?? 'member'));
                $status = trim((string) ($row['status'] ?? 'active'));

                if (empty($email)) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": Missing email";
                    continue;
                }

                if (empty($fullName)) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": Missing full_name";
                    continue;
                }

                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    // Update existing account
                    $existingUser->update([
                        'name' => $name ?: $fullName,
                        'full_name' => $fullName,
                        'role' => $role,
                        'status' => $status,
                    ]);
                    $skipped++;
                    continue;
                }

                // Create new account
                User::create([
                    'name' => $name ?: $fullName,
                    'full_name' => $fullName,
                    'email' => $email,
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
                    'role' => $role,
                    'status' => $status,
                    'email_verified_at' => now(),
                ]);

                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        $message = "Imported {$success} accounts.";
        if ($skipped > 0) {
            $message .= " {$skipped} existing accounts updated.";
        }
        if ($failed > 0) {
            $message .= " {$failed} failures.";
        }

        return redirect()->route('admin.import.index')
            ->with($failed > 0 ? 'warning' : 'success', $message);
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

    /**
     * Resolve volunteer ID from full name using exact match, fuzzy match, or skip if not found.
     * Returns null if no matching volunteer is found.
     */
    private function resolveVolunteerId(string $fullName): ?int
    {
        // Try exact match first
        $exact = User::query()
            ->where('full_name', $fullName)
            ->value('id');

        if ($exact) {
            return (int) $exact;
        }

        // Try fuzzy match with 85% similarity threshold
        $nameService = app(NameNormalizationService::class);
        foreach (User::query()->whereNotNull('full_name')->get(['id', 'full_name']) as $user) {
            if ($nameService->areNamesSimilar($fullName, $user->full_name, 85.0)) {
                return $user->id;
            }
        }

        // No match found - return null (will be handled by MetricsService filter)
        return null;
    }
}
