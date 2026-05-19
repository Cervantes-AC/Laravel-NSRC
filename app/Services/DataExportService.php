<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataExportService
{
    public function exportToCSV(Collection $data, string $filename): StreamedResponse
    {
        $sanitized = $this->sanitizeData($data);
        $headers = $sanitized->isNotEmpty() ? array_keys($sanitized->first()->toArray()) : [];

        $response = new StreamedResponse(function () use ($sanitized, $headers) {
            $handle = fopen('php://output', 'w+');

            fputcsv($handle, $headers);

            foreach ($sanitized as $row) {
                fputcsv($handle, $row->toArray());
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
        ]);

        return $response;
    }

    public function exportToPDF(string $view, array $data, string $filename): StreamedResponse
    {
        $pdf = Pdf::loadView($view, $data);

        $response = new StreamedResponse(function () use ($pdf) {
            echo $pdf->output();
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.pdf"',
        ]);

        return $response;
    }

    public function sanitizeData(Collection $data): Collection
    {
        return $data->map(function ($item) {
            $itemArray = $item instanceof Model
                ? $item->toArray()
                : (array) $item;

            unset($itemArray['password'], $itemArray['remember_token'], $itemArray['deleted_at']);

            return collect($itemArray);
        });
    }
}
