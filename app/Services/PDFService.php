<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PDFService
{
    public function __construct(
        private readonly DataExportService $exporter,
    ) {}

    public function generatePDF(string $view, array $data, string $filename = 'report'): StreamedResponse
    {
        $pdf = $this->optimizeForPrinting(
            Pdf::loadView($view, array_merge($data, [
                'generatedAt' => now()->format('F j, Y g:i A'),
                'appName' => config('app.name', 'NSRC AMS'),
            ]))
        );

        return new StreamedResponse(function () use ($pdf) {
            echo $pdf->output();
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.pdf"',
        ]);
    }

    public function optimizeForPrinting($pdf)
    {
        return $pdf->setPaper('a4', 'portrait');
    }
}
