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

        if ($timeIn && !$timeOut) {
            return 60.0;
        }

        return 40.0;
    }

    public function determineStatus($timeIn, $timeOut, bool $hasPair): string
    {
        if ($timeIn && $timeOut) {
            return 'COMPLETE';
        }

        if ($timeIn && !$timeOut && $hasPair) {
            return 'ONGOING';
        }

        if ($timeIn && !$timeOut) {
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
        $timeOut = null;

        foreach ($sorted as $log) {
            $attendance = $log instanceof \App\Models\Attendance
                ? $log->attendance
                : ($log['attendance'] ?? '');
            $dateTime = $log instanceof \App\Models\Attendance
                ? $log->date_time
                : ($log['date_time'] ?? null);
            $location = $log instanceof \App\Models\Attendance
                ? $log->location
                : ($log['location'] ?? '');

            if (strtolower($attendance) === 'time in' && !$timeIn) {
                $timeIn = $dateTime;
                $timeOut = null;
            } elseif (strtolower($attendance) === 'time out' && $timeIn) {
                $timeOut = $dateTime;

                $pairs->push([
                    'time_in' => $timeIn,
                    'time_out' => $timeOut,
                    'location' => $location,
                ]);

                $timeIn = null;
                $timeOut = null;
            }
        }

        if ($timeIn) {
            $pairs->push([
                'time_in' => $timeIn,
                'time_out' => null,
                'location' => $location ?? '',
            ]);
        }

        return $pairs;
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
            'status' => $this->determineStatus($timeIn, $timeOut, $hasPair),
        ]);
    }
}
