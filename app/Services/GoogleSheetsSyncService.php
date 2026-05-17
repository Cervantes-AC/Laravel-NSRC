<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\DutySession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoogleSheetsSyncService
{
    public function __construct(
        private readonly GoogleSheetsAttendanceService $sheetsService,
        private readonly DutyEngine $dutyEngine,
        private readonly NameNormalizationService $nameService,
    ) {}

    /**
     * @param  array{name?: string, date?: string, rebuild?: bool}  $options
     * @return array{imported: int, skipped: int, sessions_created: int, sessions_updated: int, errors: array<int, string>}
     */
    public function sync(array $options = []): array
    {
        $records = $this->sheetsService->fetchAttendanceData($options);

        if (empty($records)) {
            return [
                'imported' => 0,
                'skipped' => 0,
                'sessions_created' => 0,
                'sessions_updated' => 0,
                'errors' => ['No data retrieved from Google Sheets'],
            ];
        }

        usort($records, fn ($a, $b) => $a['dateTime'] <=> $b['dateTime']);

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $seenInBatch = [];

        foreach ($records as $index => $record) {
            try {
                if ($this->storeAttendanceLog($record, $seenInBatch)) {
                    $imported++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors[] = 'Row ' . ($index + 1) . ': ' . $e->getMessage();
                Log::error('Failed to store Google Sheets attendance row', [
                    'record' => $record,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $sessionStats = $this->rebuildDutySessions($options);

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'sessions_created' => $sessionStats['created'],
            'sessions_updated' => $sessionStats['updated'],
            'errors' => $errors,
        ];
    }

    /**
     * @param  array{fullName: string, attendance: string, dateTime: Carbon, location: ?string, shiftType: ?string}  $record
     */
    /**
     * @param  array<string, bool>  $seenInBatch
     */
    private function storeAttendanceLog(array $record, array &$seenInBatch): bool
    {
        $signature = $this->sheetsService->recordSignature($record);

        if (isset($seenInBatch[$signature])) {
            return false;
        }
        $seenInBatch[$signature] = true;

        $exists = Attendance::query()
            ->where('full_name', $record['fullName'])
            ->where('attendance', $record['attendance'])
            ->where('date_time', $record['dateTime'])
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
        ]);

        return true;
    }

    /**
     * @param  array{name?: string, date?: string, rebuild?: bool}  $options
     * @return array{created: int, updated: int}
     */
    private function rebuildDutySessions(array $options): array
    {
        $query = Attendance::query()->orderBy('date_time');

        if (! empty($options['name'])) {
            $query->where('full_name', 'like', '%' . $options['name'] . '%');
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

        $sessions = $this->dutyEngine->processDutyLogs($logs);
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
        });

        return ['created' => $created, 'updated' => $updated];
    }

    private function resolveVolunteerId(string $fullName): ?int
    {
        $exact = User::query()
            ->where('full_name', $fullName)
            ->value('id');

        if ($exact) {
            return (int) $exact;
        }

        foreach (User::query()->whereNotNull('full_name')->get(['id', 'full_name']) as $user) {
            if ($this->nameService->areNamesSimilar($fullName, $user->full_name, 85.0)) {
                return $user->id;
            }
        }

        return null;
    }

    private function buildTraceId(DutySession $session): string
    {
        $seed = implode('|', [
            $session->full_name,
            $session->date?->toDateString() ?? '',
            $session->time_in?->format('Y-m-d H:i:s') ?? '',
        ]);

        return 'GS-' . strtoupper(substr(md5($seed), 0, 8));
    }
}
