<?php

namespace App\Http\Controllers;

use App\Services\DataExportService;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected DataExportService $exportService
    ) {}

    public function index()
    {
        return view('reports.index');
    }

    public function insightsPage()
    {
        return view('reports.insights');
    }

    public function generate(Request $request)
    {
        $filters = $request->validate([
            'type' => 'required|string|in:user_activity,transaction_summary,audit_trail,system_usage,custom',
            'user_id' => 'nullable|exists:users,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'status' => 'nullable|string',
            'columns' => 'nullable|array',
            'columns.*' => 'string',
        ]);

        $report = match ($filters['type']) {
            'user_activity' => $this->reportService->generateUserActivityReport($filters),
            'transaction_summary' => $this->reportService->generateTransactionSummary($filters),
            'audit_trail' => $this->reportService->generateAuditTrailReport($filters),
            'system_usage' => $this->reportService->generateSystemUsageStats($filters),
            'custom' => $this->reportService->generateCustomReport($filters, $filters['columns'] ?? []),
            default => throw new \InvalidArgumentException('Invalid report type.'),
        };

        return view('reports.index', compact('report'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,pdf',
            'type' => 'required|string|in:user_activity,transaction_summary,audit_trail,system_usage,custom',
            'columns' => 'nullable|array',
            'columns.*' => 'string',
        ]);

        $filters = $request->except('format');

        $report = match ($filters['type']) {
            'user_activity' => $this->reportService->generateUserActivityReport($filters),
            'transaction_summary' => $this->reportService->generateTransactionSummary($filters),
            'audit_trail' => $this->reportService->generateAuditTrailReport($filters),
            'system_usage' => $this->reportService->generateSystemUsageStats($filters),
            'custom' => $this->reportService->generateCustomReport($filters, $filters['columns'] ?? []),
            default => throw new \InvalidArgumentException('Invalid report type.'),
        };

        $data = collect($report['data'] ?? []);
        $filename = 'report-'.$filters['type'].'-'.now()->format('Y-m-d');

        if ($request->format === 'csv') {
            return $this->exportService->exportToCSV($data, $filename);
        }

        return $this->exportService->exportToPDF('reports.export-pdf', compact('report'), $filename);
    }

    public function scheduled()
    {
        return view('reports.scheduled');
    }

    /**
     * Get AI-powered insights for a report
     */
    public function getInsights(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:user_activity,transaction_summary,audit_trail,system_usage,custom',
            'user_id' => 'nullable|exists:users,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'status' => 'nullable|string',
            'columns' => 'nullable|array',
            'columns.*' => 'string',
        ]);

        $filters = $request->all();

        $report = match ($filters['type']) {
            'user_activity' => $this->reportService->generateUserActivityReport($filters),
            'transaction_summary' => $this->reportService->generateTransactionSummary($filters),
            'audit_trail' => $this->reportService->generateAuditTrailReport($filters),
            'system_usage' => $this->reportService->generateSystemUsageStats($filters),
            'custom' => $this->reportService->generateCustomReport($filters, $filters['columns'] ?? []),
            default => throw new \InvalidArgumentException('Invalid report type.'),
        };

        $insights = $this->reportService->getReportInsights($report['data'], $filters['type']);

        return response()->json($insights);
    }

    /**
     * Switch AI provider
     */
    public function switchProvider(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:groq,openrouter',
        ]);

        try {
            $this->reportService->switchAIProvider($request->provider);

            return response()->json([
                'success' => true,
                'message' => 'Provider switched successfully',
                'current_provider' => $this->reportService->getCurrentProvider(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Switch API key for current provider
     */
    public function switchApiKey(Request $request)
    {
        try {
            $this->reportService->switchAPIKey();

            return response()->json([
                'success' => true,
                'message' => 'API key switched successfully',
                'current_provider' => $this->reportService->getCurrentProvider(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
