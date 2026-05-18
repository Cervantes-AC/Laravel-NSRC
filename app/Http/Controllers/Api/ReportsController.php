<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DutySession;
use App\Services\DataExportService;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function generate(Request $request, ReportService $reportService): JsonResponse
    {
        $reportType = $request->input('reportType', 'user_activity');
        $filters = array_filter([
            'date_from' => $request->input('dateFrom'),
            'date_to' => $request->input('dateTo'),
            'status' => $request->input('status'),
            'sector' => $request->input('sector'),
            'personnel_search' => $request->input('personnel'),
        ]);

        $results = match ($reportType) {
            'user_activity' => $reportService->generateUserActivityReport($filters),
            'transaction_summary' => $reportService->generateTransactionSummary($filters),
            'audit_trail' => $reportService->generateAuditTrailReport($filters),
            'system_usage' => $reportService->generateSystemUsageStats($filters),
            default => $reportService->generateUserActivityReport($filters),
        };

        $data = $results['data'] ?? [];
        $records = is_array($data) && isset($data['records']) ? $data['records'] : (is_array($data) ? $data : collect($data));

        if ($records instanceof \Illuminate\Support\Collection) {
            $records = $records->toArray();
        } elseif ($records instanceof \Illuminate\Database\Eloquent\Collection) {
            $records = $records->toArray();
        }

        $count = is_array($records) ? count($records) : 0;
        $totalDuration = 0;
        if (is_array($records)) {
            foreach ($records as $r) {
                $totalDuration += (int) ($r['duration_minutes'] ?? $r->duration_minutes ?? 0);
            }
        }

        return response()->json([
            'results' => $results,
            'reportStats' => [
                'total_records' => $results['meta']['total_records'] ?? $count,
                'total_duration' => $totalDuration,
                'generated_at' => $results['meta']['generated_at'] ?? now()->toDateTimeString(),
            ],
            'sectors' => DutySession::query()->whereNotNull('sector')->distinct()->orderBy('sector')->pluck('sector'),
        ]);
    }

    public function exportCsv(Request $request, DataExportService $exportService): StreamedResponse
    {
        $raw = $request->input('data', []);
        $records = $raw['records'] ?? $raw;
        $data = $this->normalizeRows(collect($records));
        return $exportService->exportToCSV($data, 'formal_attendance_report_' . now()->format('Ymd_His'));
    }

    public function exportPdf(Request $request, DataExportService $exportService): StreamedResponse
    {
        $raw = $request->input('data', []);
        $records = $raw['records'] ?? $raw;
        $rows = $this->normalizeRows(collect($records));

        return $exportService->exportToPDF('reports.export-pdf', [
            'title' => 'Formal Attendance Report',
            'appName' => config('app.name', 'NSRC Attendance Management System'),
            'generatedAt' => now()->format('F j, Y g:i A'),
            'dateFrom' => $request->input('dateFrom'),
            'dateTo' => $request->input('dateTo'),
            'rows' => $rows,
            'report' => [
                'type' => 'Attendance Report',
                'meta' => [
                    'generated_at' => now()->toDateTimeString(),
                    'total_records' => $rows->count(),
                ],
            ],
        ], 'formal_attendance_report_' . now()->format('Ymd_His'));
    }

    private function normalizeRows(\Illuminate\Support\Collection $records): \Illuminate\Support\Collection
    {
        return $records->map(function ($record) {
            $row = $record instanceof \Illuminate\Database\Eloquent\Model ? $record->toArray() : (array) $record;

            if (array_key_exists('full_name', $row) || array_key_exists('duration_minutes', $row)) {
                return [
                    'full_name' => $row['full_name'] ?? data_get($row, 'volunteer.full_name') ?? 'N/A',
                    'date' => $row['date'] ?? 'N/A',
                    'time_in' => $row['time_in'] ?? 'N/A',
                    'time_out' => $row['time_out'] ?? 'N/A',
                    'duration_minutes' => $row['duration_minutes'] ?? 0,
                    'location' => $row['location'] ?? 'N/A',
                    'sector' => $row['sector'] ?? 'N/A',
                    'status' => $row['status'] ?? 'N/A',
                ];
            }

            return $row;
        });
    }
}
