<?php

namespace App\Services;

use App\Models\DutySession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DutyEngine
{
    private const SIMILARITY_THRESHOLD = 70.0;

    private const SECTOR_MAP = [
        'Main Gate' => 'Security',
        'Admin Office' => 'Administration',
        'Library' => 'Academics',
        'Auditorium' => 'Events',
        'Clinic' => 'Health Services',
        'Cafeteria' => 'Logistics',
    ];

    public function __construct(
        private readonly NameNormalizationService $nameService
    ) {}

    public function processDutyLogs(Collection $logs): Collection
    {
        $grouped = $this->groupLogsByName($logs);
        $sessions = collect();

        foreach ($grouped as $name => $nameLogs) {
            $canonical = $this->nameService->getCanonicalName($sessions, $name);
            $pairs = $this->pairTimeInTimeOut($nameLogs);

            foreach ($pairs as $pair) {
                $session = $this->buildSession($canonical, $pair);
                $sessions->push($session);
            }
        }

        return $sessions;
    }

    public function calculateDuration($timeIn, $timeOut): int
    {
        if (!$timeIn || !$timeOut) {
            return 0;
        }

        $start = $timeIn instanceof \DateTimeInterface ? $timeIn : now()->parse($timeIn);
        $end = $timeOut instanceof \DateTimeInterface ? $timeOut : now()->parse($timeOut);

        if ($end <= $start) {
            return 0;
        }

        return (int) $start->diffInMinutes($end);
    }

    public function assignSector(string $location): string
    {
        foreach (self::SECTOR_MAP as $key => $sector) {
            if (str_contains(strtolower($location), strtolower($key))) {
                return $sector;
            }
        }

        return 'General';
    }

    public function generateIntegrityScore($timeIn, $timeOut): float
    {
        if ($timeIn && $timeOut) {
            return 100.0;
        }

        // Missing timeout - partial integrity (has time_in but no time_out)
        if ($timeIn && !$timeOut) {
            return 70.0;
        }

        return 40.0;
    }

    public function determineStatus($timeIn, $timeOut, bool $hasPair, ?int $duration = null): string
    {
        if ($timeIn && $timeOut) {
            if ($duration === null) {
                return 'COMPLETE';
            }

            return $duration >= 1 ? 'COMPLETE' : 'INVALID_LOG';
        }

        if ($timeIn && !$timeOut) {
            // Record exists with time_in but no time_out - mark as MISSING_TIMEOUT
            return 'MISSING_TIMEOUT';
        }

        return 'INVALID_LOG';
    }

    private function groupLogsByName(Collection $logs): Collection
    {
        $grouped = collect();

        foreach ($logs as $log) {
            $name = $log instanceof \App\Models\Attendance
                ? $log->full_name
                : ($log['full_name'] ?? '');

            if (empty($name)) {
                continue;
            }

            $matched = false;

            foreach ($grouped as $existingName => $existingLogs) {
                if ($this->nameService->areNamesSimilar($name, $existingName, self::SIMILARITY_THRESHOLD)) {
                    $grouped[$existingName]->push($log);
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                $grouped[$name] = collect([$log]);
            }
        }

        return $grouped;
    }

    private function pairTimeInTimeOut(Collection $logs): Collection
    {
        $sorted = $logs->sortBy(function ($log) {
            return $log instanceof \App\Models\Attendance
                ? $log->date_time
                : ($log['date_time'] ?? now());
        })->values();

        $pairs = collect();
        $timeIn = null;
        $location = '';

        foreach ($sorted as $log) {
            $attendance = $this->normalizeAttendanceLabel(
                $log instanceof \App\Models\Attendance
                    ? (string) $log->attendance
                    : (string) ($log['attendance'] ?? '')
            );
            $dateTime = $log instanceof \App\Models\Attendance
                ? $log->date_time
                : ($log['date_time'] ?? null);
            $logLocation = $log instanceof \App\Models\Attendance
                ? (string) ($log->location ?? '')
                : (string) ($log['location'] ?? '');

            if ($logLocation !== '') {
                $location = $logLocation;
            }

            if ($attendance === 'time in') {
                if ($timeIn) {
                    $pairs->push([
                        'time_in' => $timeIn,
                        'time_out' => null,
                        'location' => $location,
                    ]);
                }
                $timeIn = $dateTime;
            } elseif ($attendance === 'time out' && $timeIn) {
                $pairs->push([
                    'time_in' => $timeIn,
                    'time_out' => $dateTime,
                    'location' => $location,
                ]);

                $timeIn = null;
            }
        }

        if ($timeIn) {
            $pairs->push([
                'time_in' => $timeIn,
                'time_out' => null,
                'location' => $location,
            ]);
        }

        return $pairs;
    }

    private function normalizeAttendanceLabel(string $attendance): string
    {
        $value = strtolower(trim($attendance));

        if (in_array($value, ['time in', 'time_in', 'timein'], true)) {
            return 'time in';
        }

        if (in_array($value, ['time out', 'time_out', 'timeout'], true)) {
            return 'time out';
        }

        return $value;
    }

    private function buildSession(string $name, array $pair): DutySession
    {
        $timeIn = $pair['time_in'];
        $timeOut = $pair['time_out'];
        $location = $pair['location'] ?? '';
        $duration = $this->calculateDuration($timeIn, $timeOut);
        $hasPair = $timeIn !== null;

        return new DutySession([
            'full_name' => $name,
            'date' => $timeIn ? ($timeIn instanceof \DateTimeInterface ? $timeIn->toDateString() : now()->parse($timeIn)->toDateString()) : now()->toDateString(),
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'duration_minutes' => $duration,
            'location' => $location,
            'sector' => $this->assignSector($location),
            'integrity_score' => $this->generateIntegrityScore($timeIn, $timeOut),
            'status' => $this->determineStatus($timeIn, $timeOut, $hasPair, $duration),
        ]);
    }
}
