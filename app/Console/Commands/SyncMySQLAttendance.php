<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\DutySession;
use App\Models\User;
use App\Models\VolunteerMetrics;
use App\Services\DutyEngine;
use App\Services\MetricsService;
use App\Services\MySQLAttendanceService;
use App\Services\NameNormalizationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncMySQLAttendance extends Command
{
    protected $signature = 'attendance:sync-mysql
                            {--date= : Optional date filter (M/d/yyyy, e.g. 5/3/2026)}
                            {--name= : Optional volunteer name filter}';

    protected $description = 'Mirror attendance from MySQL source table and rebuild attendance summaries';

    public function handle(MySQLAttendanceService $syncService): int
    {
        $this->info('Starting MySQL attendance sync...');

        $options = array_filter([
            'date' => $this->option('date'),
            'name' => $this->option('name'),
        ]);

        if (! empty($options['date'])) {
            $this->info("Filtering by date: {$options['date']}");
        }

        if (! empty($options['name'])) {
            $this->info("Filtering by name: {$options['name']}");
        }

        try {
            $result = $this->performSync($syncService, $options);

            if (! empty($result['errors']) && $result['imported'] === 0 && $result['sessions_created'] === 0) {
                $this->warn($result['errors'][0] ?? 'Sync produced no results');

                return 1;
            }

            $this->newLine();
            $this->info('Sync completed!');
            $this->line("Source rows mirrored: <fg=green>{$result['attendance_total']}</>");
            $this->line("New attendance logs: <fg=green>{$result['imported']}</>");
            $this->line("Skipped (duplicates): <fg=yellow>{$result['skipped']}</>");
            $this->line("Attendance summaries created: <fg=green>{$result['sessions_created']}</>");
            $this->line("Attendance summaries updated: <fg=cyan>{$result['sessions_updated']}</>");

            if (! empty($result['errors'])) {
                $this->newLine();
                $this->error('Errors encountered:');
                foreach ($result['errors'] as $error) {
                    $this->line("  - {$error}");
                }
            }

            return empty($result['errors']) ? 0 : 1;
        } catch (\Exception $e) {
            $this->error('Sync failed: '.$e->getMessage());
            Log::error('MySQL attendance sync failed', [
                'error' => $e->getMessage(),
            ]);

            return 1;
        }
    }

    /**
     * Perform the sync using MySQL data source
     */
    private function performSync(MySQLAttendanceService $mysqlService, array $options): array
    {
        $records = $mysqlService->fetchAttendanceData($options);

        if (empty($records)) {
            return [
                'imported' => 0,
                'skipped' => 0,
                'attendance_total' => Attendance::count(),
                'sessions_created' => 0,
                'sessions_updated' => 0,
                'errors' => ['No data retrieved from MySQL source'],
            ];
        }

        usort($records, fn ($a, $b) => $a['dateTime'] <=> $b['dateTime']);

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $seenInBatch = [];
        $replace = ($options['replace'] ?? true) && empty($options['name']) && empty($options['date']);

        if ($replace) {
            $this->clearSpreadsheetMirrors();
        }

        foreach ($records as $index => $record) {
            try {
                if ($this->storeAttendanceLog($record, $mysqlService, $seenInBatch)) {
                    $imported++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors[] = 'Row '.($index + 1).': '.$e->getMessage();
                Log::error('Failed to store MySQL attendance row', [
                    'record' => $record,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $sessionStats = $this->rebuildDutySessions($options);

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'attendance_total' => Attendance::count(),
            'sessions_created' => $sessionStats['created'],
            'sessions_updated' => $sessionStats['updated'],
            'errors' => $errors,
        ];
    }

    /**
     * Store a single attendance log
     */
    private function storeAttendanceLog(array $record, MySQLAttendanceService $mysqlService, array &$seenInBatch): bool
    {
        $signature = $mysqlService->recordSignature($record);

        if (isset($seenInBatch[$signature])) {
            return false;
        }
        $seenInBatch[$signature] = true;

        $exists = Attendance::query()
            ->where('source_signature', $signature)
            ->exists();

        if ($exists) {
            return false;
        }

        Attendance::create([
            'full_name' => $record['fullName'],
            'attendance' => $record['attendance'],
            'date_time' => $record['dateTime'],
            'location' => $record['location'],
            'shift_type' => $record['shiftType'],
            'source_signature' => $signature,
            'source_payload' => $record['payload'] ?? [],
        ]);

        return true;
    }

    /**
     * Rebuild duty sessions from attendance logs
     */
    private function rebuildDutySessions(array $options): array
    {
        $query = Attendance::query()->orderBy('date_time');

        if (! empty($options['name'])) {
            $query->where('full_name', 'like', '%'.$options['name'].'%');
        }

        if (! empty($options['date'])) {
            try {
                $date = Carbon::createFromFormat('n/j/Y', $options['date']);
                $query->whereDate('date_time', $date);
            } catch (\Exception) {
                Log::warning('Invalid date filter for duty session rebuild', ['date' => $options['date']]);
            }
        }

        $logs = $query->get();

        if ($logs->isEmpty()) {
            return ['created' => 0, 'updated' => 0];
        }

        $dutyEngine = app(DutyEngine::class);
        $sessions = $dutyEngine->processDutyLogs($logs);
        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($sessions, &$created, &$updated) {
            foreach ($sessions as $session) {
                $volunteerId = $this->resolveVolunteerId($session->full_name);
                $traceId = $this->buildTraceId($session);

                $attributes = [
                    'full_name' => $session->full_name,
                    'date' => $session->date,
                    'time_in' => $session->time_in,
                    'time_out' => $session->time_out,
                    'duration_minutes' => $session->duration_minutes,
                    'status' => $session->status,
                    'location' => $session->location,
                    'sector' => $session->sector,
                    'integrity_score' => $session->integrity_score,
                    'volunteer_id' => $volunteerId,
                    'trace_id' => $traceId,
                ];

                $match = DutySession::query()
                    ->where('full_name', $session->full_name)
                    ->whereDate('date', $session->date)
                    ->when($session->time_in, fn ($q) => $q->where('time_in', $session->time_in))
                    ->first();

                if ($match) {
                    $match->update($attributes);
                    $updated++;
                } else {
                    DutySession::create($attributes);
                    $created++;
                }
            }

            VolunteerMetrics::query()->delete();
            app(MetricsService::class)->calculateVolunteerMetrics(DutySession::query()->get());
        });

        return ['created' => $created, 'updated' => $updated];
    }

    private function clearSpreadsheetMirrors(): void
    {
        DB::transaction(function () {
            VolunteerMetrics::query()->delete();
            DutySession::withTrashed()->forceDelete();
            Attendance::query()->delete();
        });
    }

    private function resolveVolunteerId(string $fullName): ?int
    {
        $exact = User::query()
            ->where('full_name', $fullName)
            ->value('id');

        if ($exact) {
            return (int) $exact;
        }

        $nameService = app(NameNormalizationService::class);
        foreach (User::query()->whereNotNull('full_name')->get(['id', 'full_name']) as $user) {
            if ($nameService->areNamesSimilar($fullName, $user->full_name, 85.0)) {
                return $user->id;
            }
        }

        return $this->createSpreadsheetVolunteer($fullName)->id;
    }

    private function createSpreadsheetVolunteer(string $fullName): User
    {
        $slug = Str::slug($fullName) ?: 'mysql-volunteer';
        $email = $slug.'@mysql.local';
        $suffix = 1;

        while (User::query()->where('email', $email)->exists()) {
            $email = $slug.'-'.$suffix.'@mysql.local';
            $suffix++;
        }

        return User::create([
            'name' => $fullName,
            'full_name' => $fullName,
            'email' => $email,
            'password' => Hash::make(Str::random(32)),
            'role' => 'member',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }

    private function buildTraceId(DutySession $session): string
    {
        $seed = implode('|', [
            $session->full_name,
            $session->date?->toDateString() ?? '',
            $session->time_in?->format('Y-m-d H:i:s') ?? '',
        ]);

        return 'MS-'.strtoupper(substr(md5($seed), 0, 8));
    }
}
