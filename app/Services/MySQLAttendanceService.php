<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MySQLAttendanceService
{
    /**
     * Fetch attendance data from MySQL with optional filters.
     *
     * @param  array{name?: string, date?: string}  $options  date format: M/d/yyyy
     * @return array<int, array{fullName: string, attendance: string, dateTime: Carbon, location: ?string, shiftType: ?string, payload: array<string, mixed>}>
     */
    public function fetchAttendanceData(array $options = []): array
    {
        try {
            $query = $this->buildQuery($options);
            $records = $query->get();

            if ($records->isEmpty()) {
                Log::info('No attendance records found in MySQL', ['options' => $options]);

                return [];
            }

            return $this->mapMySQLData($records->toArray());
        } catch (\Exception $e) {
            Log::error('Failed to fetch attendance data from MySQL', [
                'error' => $e->getMessage(),
                'options' => $options,
            ]);

            return [];
        }
    }

    public function fetchPersonnelAttendance(string $nameOrId): array
    {
        return $this->fetchAttendanceData(['name' => $nameOrId]);
    }

    public function fetchAttendanceByDate(string $date): array
    {
        return $this->fetchAttendanceData(['date' => $date]);
    }

    public function fetchPersonnelAttendanceByDate(string $nameOrId, string $date): array
    {
        return $this->fetchAttendanceData([
            'name' => $nameOrId,
            'date' => $date,
        ]);
    }

    /**
     * @param  array{fullName: string, attendance: string, dateTime: Carbon, location: ?string, shiftType: ?string, payload?: array<string, mixed>}  $record
     */
    public function recordSignature(array $record): string
    {
        return strtolower(trim($record['fullName']))
            .'|'.$record['dateTime']->format('Y-m-d H:i:s')
            .'|'.$this->normalizeAttendanceType($record['attendance']);
    }

    public function normalizeAttendanceType(string $attendance): string
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

    /**
     * @param  array{name?: string, date?: string}  $options
     */
    private function buildQuery(array $options)
    {
        $table = config('attendance.mysql.table', 'attendance_source');
        $query = DB::table($table);

        if (! empty($options['name'])) {
            $nameColumn = config('attendance.mysql.name_column', 'full_name');
            $query->where($nameColumn, 'like', '%'.$options['name'].'%');
        }

        if (! empty($options['date'])) {
            try {
                $date = Carbon::createFromFormat('n/j/Y', $options['date']);
                $dateColumn = config('attendance.mysql.date_column', 'date_time');
                $query->whereDate($dateColumn, $date);
            } catch (\Exception) {
                Log::warning('Invalid date filter for MySQL attendance fetch', ['date' => $options['date']]);
            }
        }

        $dateColumn = config('attendance.mysql.date_column', 'date_time');

        return $query->orderBy($dateColumn);
    }

    /**
     * @param  array<int, array<string, mixed>>  $mysqlData
     * @return array<int, array{fullName: string, attendance: string, dateTime: Carbon, location: ?string, shiftType: ?string, payload: array<string, mixed>}>
     */
    private function mapMySQLData(array $mysqlData): array
    {
        $nameColumn = config('attendance.mysql.name_column', 'full_name');
        $attendanceColumn = config('attendance.mysql.attendance_column', 'attendance');
        $dateColumn = config('attendance.mysql.date_column', 'date_time');
        $locationColumn = config('attendance.mysql.location_column', 'location');
        $shiftColumn = config('attendance.mysql.shift_column', 'shift_type');

        return array_values(array_filter(array_map(function ($item) use (
            $nameColumn,
            $attendanceColumn,
            $dateColumn,
            $locationColumn,
            $shiftColumn
        ) {
            $fullName = trim((string) ($item->{$nameColumn} ?? ''));
            $attendance = $this->normalizeAttendanceType((string) ($item->{$attendanceColumn} ?? ''));
            $timestamp = $item->{$dateColumn} ?? null;

            if ($fullName === '' || $attendance === '' || empty($timestamp)) {
                return null;
            }

            try {
                $dateTime = Carbon::parse($timestamp);
            } catch (\Exception) {
                Log::warning('Skipping MySQL row with invalid timestamp', [
                    'timestamp' => $timestamp,
                    'full_name' => $fullName,
                ]);

                return null;
            }

            return [
                'fullName' => $fullName,
                'attendance' => $attendance === 'time in' ? 'Time in' : ($attendance === 'time out' ? 'Time out' : $attendance),
                'dateTime' => $dateTime,
                'location' => $this->nullableString($item->{$locationColumn} ?? null),
                'shiftType' => $this->nullableString($item->{$shiftColumn} ?? null),
                'payload' => (array) $item,
            ];
        }, $mysqlData)));
    }

    private function nullableString(mixed $value): ?string
    {
        $string = trim((string) $value);

        return $string === '' ? null : $string;
    }
}
