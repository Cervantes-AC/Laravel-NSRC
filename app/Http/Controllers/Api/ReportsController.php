<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DutySession;
use App\Services\DataExportService;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function exportCsv(Request $request, DataExportService $exportService): JsonResponse
    {
        $raw = $request->input('data', []);
        $records = $raw['records'] ?? $raw;
        $data = collect($records);
        $exportService->exportToCSV($data, 'report_' . now()->format('Ymd_His'));
        return response()->json(['message' => 'CSV exported']);
    }

    public function exportPdf(Request $request, DataExportService $exportService): JsonResponse
    {
        $exportService->exportToPDF('exports.report', $request->input('data', []), 'report_' . now()->format('Ymd_His'));
        return response()->json(['message' => 'PDF exported']);
    }
}
