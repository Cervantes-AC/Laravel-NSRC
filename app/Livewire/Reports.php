<?php

namespace App\Livewire;

use App\Services\DataExportService;
use App\Services\ReportService;
use Livewire\Component;

class Reports extends Component
{
    public string $reportType = 'user_activity';

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $status = '';

    public string $personnel = '';

    public array $results = [];

    public function generateReport(ReportService $reportService): void
    {
        $filters = array_filter([
            'date_from' => $this->dateFrom ?: null,
            'date_to' => $this->dateTo ?: null,
            'status' => $this->status ?: null,
            'user_id' => $this->personnel ?: null,
        ]);

        $this->results = match ($this->reportType) {
            'user_activity' => $reportService->generateUserActivityReport($filters),
            'transaction_summary' => $reportService->generateTransactionSummary($filters),
            'audit_trail' => $reportService->generateAuditTrailReport($filters),
            'system_usage' => $reportService->generateSystemUsageStats($filters),
            default => $reportService->generateUserActivityReport($filters),
        };
    }

    public function exportCSV(DataExportService $exportService): void
    {
        $data = collect($this->results['data'] ?? []);
        $exportService->exportToCSV($data, 'report_' . now()->format('Ymd_His'));
    }

    public function exportPDF(DataExportService $exportService): void
    {
        $exportService->exportToPDF(
            'exports.report',
            $this->results,
            'report_' . now()->format('Ymd_His')
        );
    }

    public function render()
    {
        return view('livewire.reports')
            ->layout('components.layouts.app');
    }
}
