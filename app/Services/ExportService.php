<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

    public function exportToExcel(Collection $data, string $filename): StreamedResponse
    {
        if (! class_exists(Spreadsheet::class)) {
            throw new \RuntimeException('Excel export requires the phpoffice/phpspreadsheet package. Run: composer require phpoffice/phpspreadsheet');
        }

        $sanitized = $this->dataExport->sanitizeData($data);
        $headers = $sanitized->isNotEmpty() ? array_keys($sanitized->first()->toArray()) : [];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $col = 0;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($col + 1, 1, ucwords(str_replace('_', ' ', $header)));
            $col++;
        }

        $row = 2;
        foreach ($sanitized as $item) {
            $col = 0;
            foreach ($headers as $header) {
                $value = $item[$header] ?? '';
                if ($value instanceof \DateTimeInterface) {
                    $value = $value->format('Y-m-d H:i:s');
                }
                $sheet->setCellValueByColumnAndRow($col + 1, $row, $value);
                $col++;
            }
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.xlsx"',
            'Cache-Control' => 'max-age=0',
        ]);

        return $response;
    }

    public function exportToPDF(Collection $data, string $filename, string $view = 'reports.export-pdf'): StreamedResponse
    {
        return $this->pdf->generatePDF($view, ['rows' => $data, 'title' => $filename], $filename);
    }

    public function exportWithData(Collection $data, string $filename, string $format = 'csv'): StreamedResponse
    {
        return match ($format) {
            'csv' => $this->exportToCSV($data, $filename),
            'xlsx' => $this->exportToExcel($data, $filename),
            'pdf' => $this->exportToPDF($data, $filename),
            default => $this->exportToCSV($data, $filename),
        };
    }

    public function scheduleExport(Collection $data, string $filename, string $format = 'csv', string $email): bool
    {
        try {
            $response = $this->exportWithData($data, $filename, $format);

            ob_start();
            $response->sendContent();
            $content = ob_get_clean();

            $extension = match ($format) {
                'xlsx' => 'xlsx',
                'pdf' => 'pdf',
                default => 'csv',
            };

            Storage::disk('local')->put(
                "exports/{$filename}.{$extension}",
                $content
            );

            Mail::raw("Your export '{$filename}' is ready. Please download it from the admin panel.", function ($message) use ($email, $filename, $format) {
                $extension = match ($format) {
                    'xlsx' => 'xlsx',
                    'pdf' => 'pdf',
                    default => 'csv',
                };
                $message->to($email)
                    ->subject("Export Ready: {$filename}")
                    ->attach(Storage::disk('local')->path("exports/{$filename}.{$extension}"), [
                        'as' => "{$filename}.{$extension}",
                        'mime' => match ($format) {
                            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'pdf' => 'application/pdf',
                            default => 'text/csv',
                        },
                    ]);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Scheduled export failed: '.$e->getMessage());

            return false;
        }
    }
}
