<?php

namespace App\Services;

use App\Mail\ImportNotification;
use App\Models\Attendance;
use App\Services\NotificationService;
use App\Types\AttendanceRecord;
use App\Types\DuplicateDetectionResult;
use App\Types\ExportFormat;
use App\Types\FieldMapping;
use App\Types\ImportFileFormat;
use App\Types\ImportLogEntry;
use App\Types\ImportPreviewResult;
use App\Types\ImportProcessResult;
use App\Types\ImportValidationResult;
use App\Types\NormalizedImportRow;
use App\Types\ValidationRules;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ImportService
{
    protected array $importLog = [];
    protected NotificationService $notifications;

    public function __construct()
    {
        $this->notifications = app(NotificationService::class);
    }

    public function validateImportFile(UploadedFile $file): array
    {
        $errors = [];
        $warnings = [];

        $allowedMimes = ['csv', 'xlsx', 'xls', 'txt'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedMimes)) {
            $errors[] = "Invalid file type: {$extension}. Allowed types: " . implode(', ', $allowedMimes);
        }

        $maxSize = 10 * 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size of 10MB.';
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
        $skipped = 0;
        $errors = [];
        $this->importLog = [];

        try {
            $rows = $this->parseFile($file);
            $totalRows = count($rows);

            foreach ($rows as $index => $row) {
                try {
                    $normalized = $this->normalizeSheetRow($row);

                    if ($this->isDuplicate($normalized)) {
                        $skipped++;
                        $this->importLog[] = [
                            'row' => $index + 1,
                            'status' => 'skipped',
                            'message' => 'Duplicate record',
                        ];
                        continue;
                    }

                    Attendance::create([
                        'full_name' => $normalized['full_name'],
                        'attendance' => $normalized['attendance'],
                        'date_time' => $normalized['date_time'],
                        'location' => $normalized['location'],
                        'shift_type' => $normalized['shift_type'],
                        'source_signature' => $normalized['source_signature'],
                        'source_payload' => $normalized['source_payload'] ? json_decode($normalized['source_payload'], true) : null,
                    ]);

                    $success++;
                    $this->importLog[] = [
                        'row' => $index + 1,
                        'status' => 'success',
                        'message' => "Imported: {$normalized['full_name']}",
                    ];
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                    $this->importLog[] = [
                        'row' => $index + 1,
                        'status' => 'failed',
                        'message' => $e->getMessage(),
                    ];
                }
            }

            $this->sendImportNotification($validation['filename'], $totalRows, $success, $failed, $skipped, empty($errors));
            $this->notifications->importSuccess($validation['filename'], $success, $failed, $skipped);
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            $errors[] = $e->getMessage();
            $this->notifications->importValidationFailed($file->getClientOriginalName(), [$e->getMessage()]);
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'skipped' => $skipped,
            'errors' => $errors,
            'log' => $this->importLog,
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

    public function getImportHistory(int $limit = 10): array
    {
        return array_slice(array_reverse($this->importLog), 0, $limit);
    }

    private function isDuplicate(array $data): bool
    {
        return Attendance::where('full_name', $data['full_name'])
            ->whereDate('date_time', $data['date_time'])
            ->exists();
    }

    private function sendImportNotification(string $filename, int $total, int $success, int $failed, int $skipped, bool $overallSuccess): void
    {
        try {
            Mail::to(config('mail.backup_email', 'aaronclydeccervantes@gmail.com'))
                ->send(new ImportNotification($filename, $total, $success, $failed, $skipped, $overallSuccess));
        } catch (\Exception $e) {
            Log::warning('Failed to send import notification email: ' . $e->getMessage());
        }
    }

    private function parseFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'csv' || $extension === 'txt') {
            return $this->parseCSV($file);
        }

        if (in_array($extension, ['xlsx', 'xls'])) {
            return $this->parseExcel($file);
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

    private function parseExcel(UploadedFile $file): array
    {
        if (!extension_loaded('zip') && !class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            throw new \RuntimeException('Excel support requires the phpoffice/phpspreadsheet package. Run: composer require phpoffice/phpspreadsheet');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = [];

        $headers = [];
        foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
            $rowData = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $values = [];
            foreach ($cellIterator as $cell) {
                $values[] = $cell->getValue();
            }

            if ($rowIndex === 1) {
                $headers = array_map('trim', $values);
                continue;
            }

            $rowData = [];
            foreach ($headers as $i => $header) {
                $rowData[$header] = $values[$i] ?? '';
            }
            $rows[] = $rowData;
        }

        return $rows;
    }

    /**
     * Normalize a CSV row to match the Google Sheets column layout.
     *
     * @return array{full_name: string, attendance: string, date_time: mixed, location: ?string, shift_type: ?string, source_signature: ?string, source_payload: ?string}
     */
    private function normalizeSheetRow(array $row): array
    {
        $timestamp = $row['timestamp'] ?? $row['date_time'] ?? now();
        $sourcePayload = $row['source_payload'] ?? null;

        // If source_payload is a JSON string, keep it as is for later parsing
        if (is_string($sourcePayload) && !empty($sourcePayload)) {
            try {
                // Validate it's valid JSON
                json_decode($sourcePayload, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $sourcePayload = null;
            }
        }

        return [
            'full_name' => trim((string) ($row['full_name'] ?? '')),
            'attendance' => trim((string) ($row['attendance'] ?? '')),
            'date_time' => $timestamp,
            'location' => isset($row['location']) ? trim((string) $row['location']) : null,
            'shift_type' => isset($row['shift_type']) ? trim((string) $row['shift_type']) : null,
            'source_signature' => isset($row['source_signature']) ? trim((string) $row['source_signature']) : null,
            'source_payload' => $sourcePayload,
        ];
    }

    private function validateRow(array $row, int $rowNumber): array
    {
        $errors = [];
        $normalized = $this->normalizeSheetRow($row);

        $validator = Validator::make($normalized, [
            'full_name' => 'required|string|max:255',
            'attendance' => 'required|string',
            'date_time' => 'required',
            'location' => 'nullable|string|max:255',
            'shift_type' => 'nullable|string|max:255',
            'source_signature' => 'nullable|string|max:255',
            'source_payload' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = "Row {$rowNumber}: {$error}";
            }
        }

        return $errors;
    }
}
