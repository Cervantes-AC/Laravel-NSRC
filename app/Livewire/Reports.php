<?php

namespace App\Livewire;

use App\Models\DutySession;
use App\Services\DataExportService;
use App\Services\ReportService;
use Livewire\Component;

class Reports extends Component
{
    public string $reportType = 'user_activity';

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $status = '';

    public string $sector = '';

    public string $personnel = '';

    public array $results = [];

    public bool $showFormalTemplate = false;

    public string $selectedTemplate = 'certificate';

    public bool $aiSuccess = false;

    public array $reportStats = [];

    public function generateReport(ReportService $reportService): void
    {
        $filters = array_filter([
            'date_from' => $this->dateFrom ?: null,
            'date_to' => $this->dateTo ?: null,
            'status' => $this->status ?: null,
            'sector' => $this->sector ?: null,
            'personnel_search' => $this->personnel ?: null,
        ]);

        $this->results = match ($this->reportType) {
            'user_activity' => $reportService->generateUserActivityReport($filters),
            'transaction_summary' => $reportService->generateTransactionSummary($filters),
            'audit_trail' => $reportService->generateAuditTrailReport($filters),
            'system_usage' => $reportService->generateSystemUsageStats($filters),
            default => $reportService->generateUserActivityReport($filters),
        };

        $data = $this->results['data'] ?? [];
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

        $this->reportStats = [
            'total_records' => $this->results['meta']['total_records'] ?? $count,
            'total_duration' => $totalDuration,
            'generated_at' => $this->results['meta']['generated_at'] ?? now()->toDateTimeString(),
        ];
    }

    public function exportCSV(DataExportService $exportService): void
    {
        $raw = $this->results['data'] ?? [];
        $records = $raw['records'] ?? $raw;
        $data = collect($records);
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

    public function toggleFormalTemplate(string $template): void
    {
        $this->selectedTemplate = $template;
        $this->showFormalTemplate = true;
    }

    public function closeFormalTemplate(): void
    {
        $this->showFormalTemplate = false;
    }

    public function clearFilters(): void
    {
        $this->reset(['dateFrom', 'dateTo', 'status', 'sector', 'personnel', 'results', 'reportStats']);
    }

    public function render()
    {
        return view('livewire.reports', [
            'sectors' => DutySession::query()->whereNotNull('sector')->distinct()->orderBy('sector')->pluck('sector'),
        ]);
    }
}
