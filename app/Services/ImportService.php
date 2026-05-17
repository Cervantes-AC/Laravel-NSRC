<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImportService
{
    public function validateImportFile(UploadedFile $file): array
    {
        $errors = [];
        $warnings = [];

        $allowedMimes = ['csv', 'xlsx', 'xls', 'txt'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedMimes)) {
            $errors[] = "Invalid file type: {$extension}. Allowed types: " . implode(', ', $allowedMimes);
        }

        $maxSize = 5 * 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size of 5MB.';
        }

        if ($file->getSize() === 0) {
            $errors[] = 'File is empty.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'filename' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'extension' => $extension,
        ];
    }

    public function previewImportData(UploadedFile $file): array
    {
        $validation = $this->validateImportFile($file);

        if (!$validation['valid']) {
            return [
                'preview' => [],
                'errors' => $validation['errors'],
                'total_rows' => 0,
            ];
        }

        try {
            $rows = $this->parseFile($file);
            $errors = [];
            $preview = [];

            foreach ($rows as $index => $row) {
                $rowErrors = $this->validateRow($row, $index + 1);
                if (!empty($rowErrors)) {
                    $errors = array_merge($errors, $rowErrors);
                }
                $preview[] = $row;
            }

            return [
                'preview' => array_slice($preview, 0, 10),
                'errors' => $errors,
                'total_rows' => count($rows),
            ];
        } catch (\Exception $e) {
            Log::error('Import preview failed: ' . $e->getMessage());
            return [
                'preview' => [],
                'errors' => [$e->getMessage()],
                'total_rows' => 0,
            ];
        }
    }

    public function processImport(UploadedFile $file): array
    {
        $validation = $this->validateImportFile($file);

        if (!$validation['valid']) {
            return [
                'success' => 0,
                'failed' => 0,
                'errors' => $validation['errors'],
            ];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        try {
            $rows = $this->parseFile($file);

            foreach ($rows as $index => $row) {
                try {
                    Attendance::create([
                        'full_name' => $row['full_name'] ?? '',
                        'attendance' => $row['attendance'] ?? '',
                        'date_time' => $row['date_time'] ?? now(),
                        'location' => $row['location'] ?? '',
                        'shift_type' => $row['shift_type'] ?? '',
                    ]);
                    $success++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            $errors[] = $e->getMessage();
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    public function detectDuplicates(array $data): array
    {
        $seen = [];
        $duplicates = [];

        foreach ($data as $index => $row) {
            $signature = ($row['full_name'] ?? '') . '|' . ($row['date_time'] ?? '');

            if (isset($seen[$signature])) {
                $duplicates[] = [
                    'original_index' => $seen[$signature],
                    'duplicate_index' => $index,
                    'row' => $row,
                ];
            } else {
                $seen[$signature] = $index;
            }
        }

        return $duplicates;
    }

    private function parseFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'csv' || $extension === 'txt') {
            return $this->parseCSV($file);
        }

        throw new \InvalidArgumentException("Unsupported file format: {$extension}");
    }

    private function parseCSV(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);
        $rows = [];

        if (!$headers) {
            fclose($handle);
            return [];
        }

        $headers = array_map('trim', $headers);

        while (($line = fgetcsv($handle)) !== false) {
            $row = [];
            foreach ($headers as $i => $header) {
                $row[$header] = $line[$i] ?? '';
            }
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function validateRow(array $row, int $rowNumber): array
    {
        $errors = [];

        $validator = Validator::make($row, [
            'full_name' => 'required|string|max:255',
            'attendance' => 'required|string|in:Time In,Time Out,time in,time out',
            'date_time' => 'required|date',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = "Row {$rowNumber}: {$error}";
            }
        }

        return $errors;
    }
}
