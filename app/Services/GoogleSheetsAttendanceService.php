<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSheetsAttendanceService
{
    /**
     * Fetch attendance data from Google Sheets with optional filters.
     *
     * @param  array{name?: string, date?: string}  $options  date format: M/d/yyyy
     * @return array<int, array{fullName: string, attendance: string, dateTime: \Carbon\Carbon, location: ?string, shiftType: ?string}>
     */
    public function fetchAttendanceData(array $options = []): array
    {
        try {
            $response = Http::timeout($this->timeout())
                ->get($this->apiUrl(), $this->buildQueryParams($options));

            if (! $response->successful()) {
                Log::warning('Google Sheets API returned non-successful status', [
                    'status' => $response->status(),
                ]);

                return [];
            }

            $data = $response->json();

            if (! $this->isValidResponse($data)) {
                Log::warning('Invalid Google Sheets API response format', [
                    'response' => $data,
                ]);

                return [];
            }

            return $this->mapGoogleSheetData($data['data']);
        } catch (\Exception $e) {
            Log::error('Failed to fetch attendance data from Google Sheets', [
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
     * @param  array<int, array{fullName: string, attendance: string, dateTime: \Carbon\Carbon, location: ?string, shiftType: ?string}>  $records
     */
    public function recordSignature(array $record): string
    {
        return strtolower(trim($record['fullName']))
            . '|' . $record['dateTime']->format('Y-m-d H:i:s')
            . '|' . $this->normalizeAttendanceType($record['attendance']);
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

    private function apiUrl(): string
    {
        return (string) config('attendance.google_sheets.api_url');
    }

    private function timeout(): int
    {
        return (int) config('attendance.google_sheets.request_timeout', 30);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, string>
     */
    private function buildQueryParams(array $options): array
    {
        $params = [];

        if (! empty($options['name'])) {
            $params['name'] = $options['name'];
        }

        if (! empty($options['date'])) {
            $params['date'] = $options['date'];
        }

        return $params;
    }

    private function isValidResponse(mixed $data): bool
    {
        if (! is_array($data)) {
            return false;
        }

        if (! isset($data['success']) || $data['success'] !== true) {
            return false;
        }

        if (! isset($data['data']) || ! is_array($data['data'])) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<int, array<string, mixed>>  $googleData
     * @return array<int, array{fullName: string, attendance: string, dateTime: \Carbon\Carbon, location: ?string, shiftType: ?string}>
     */
    private function mapGoogleSheetData(array $googleData): array
    {
        return array_values(array_filter(array_map(function ($item) {
            $fullName = trim((string) ($item['full_name'] ?? ''));
            $attendance = $this->normalizeAttendanceType((string) ($item['attendance'] ?? ''));
            $timestamp = $item['timestamp'] ?? $item['date_time'] ?? null;

            if ($fullName === '' || $attendance === '' || empty($timestamp)) {
                return null;
            }

            try {
                $dateTime = Carbon::parse($timestamp);
            } catch (\Exception) {
                Log::warning('Skipping Google Sheets row with invalid timestamp', [
                    'timestamp' => $timestamp,
                    'full_name' => $fullName,
                ]);

                return null;
            }

            return [
                'fullName' => $fullName,
                'attendance' => $attendance === 'time in' ? 'Time in' : ($attendance === 'time out' ? 'Time out' : $attendance),
                'dateTime' => $dateTime,
                'location' => isset($item['location']) ? trim((string) $item['location']) : null,
                'shiftType' => isset($item['shift_type']) ? trim((string) $item['shift_type']) : null,
            ];
        }, $googleData)));
    }
}
