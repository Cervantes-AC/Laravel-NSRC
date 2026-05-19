<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PDFService
{
    public function __construct(
        private readonly DataExportService $exporter,
    ) {}

    public function generatePDF(
        string $view,
        array $data,
        string $filename = 'report',
        string $orientation = 'portrait',
        bool $pageNumbers = true,
        bool $headerFooter = true,
    ): StreamedResponse {
        $pdf = Pdf::loadView($view, array_merge($data, [
            'generatedAt' => now()->format('F j, Y g:i A'),
            'appName' => config('app.name', 'NSRC AMS'),
            'appLogo' => public_path('images/nsrc-logo.png'),
            'showPageNumbers' => $pageNumbers,
            'showHeaderFooter' => $headerFooter,
        ]));

        $this->configurePdf($pdf, $orientation, $pageNumbers, $headerFooter);

        return new StreamedResponse(function () use ($pdf) {
            echo $pdf->output();
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.pdf"',
        ]);
    }

    public function streamPDF(
        string $view,
        array $data,
        string $filename = 'report',
        string $orientation = 'portrait',
    ): StreamedResponse {
        $pdf = Pdf::loadView($view, array_merge($data, [
            'generatedAt' => now()->format('F j, Y g:i A'),
            'appName' => config('app.name', 'NSRC AMS'),
            'appLogo' => public_path('images/nsrc-logo.png'),
            'showPageNumbers' => true,
            'showHeaderFooter' => true,
        ]));

        $this->configurePdf($pdf, $orientation, true, true);

        return new StreamedResponse(function () use ($pdf) {
            echo $pdf->output();
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'.pdf"',
        ]);
    }

    public function downloadPDF(string $view, array $data, string $filename = 'report'): StreamedResponse
    {
        return $this->generatePDF($view, $data, $filename);
    }

    public function emailPDF(string $view, array $data, string $filename = 'report'): string
    {
        $pdf = Pdf::loadView($view, array_merge($data, [
            'generatedAt' => now()->format('F j, Y g:i A'),
            'appName' => config('app.name', 'NSRC AMS'),
            'appLogo' => public_path('images/nsrc-logo.png'),
            'showPageNumbers' => true,
            'showHeaderFooter' => true,
        ]));

        $this->configurePdf($pdf, 'portrait', true, true);

        return $pdf->output();
    }

    public function optimizeForPrinting($pdf, string $orientation = 'portrait')
    {
        return $this->configurePdf($pdf, $orientation, true, true);
    }

    private function configurePdf($pdf, string $orientation = 'portrait', bool $pageNumbers = true, bool $headerFooter = true): void
    {
        $paperSize = config('app.pdf_paper_size', 'a4');
        $orientation = in_array($orientation, ['portrait', 'landscape']) ? $orientation : 'portrait';

        $pdf->setPaper($paperSize, $orientation);

        if ($pageNumbers || $headerFooter) {
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);
        }
    }
}
