<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    public function __construct(
        private readonly DataExportService $dataExport,
        private readonly PDFService $pdf,
    ) {}

    public function exportToCSV(Collection $data, string $filename): StreamedResponse
    {
        return $this->dataExport->exportToCSV($data, $filename);
    }

    public function exportToPDF(Collection $data, string $filename, string $view = 'reports.export-pdf'): StreamedResponse
    {
        return $this->pdf->generatePDF($view, ['rows' => $data, 'title' => $filename], $filename);
    }
}
