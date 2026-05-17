<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Services\GoogleSheetsSyncService;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportController extends Controller
{
    public function __construct(
        protected ImportService $importService,
        protected GoogleSheetsSyncService $googleSheetsSyncService,
    ) {}

    public function index()
    {
        return view('admin.import.index');
    }

    public function preview(ImportRequest $request)
    {
        $result = $this->importService->previewImportData($request->file('file'));

        if (!empty($result['errors'])) {
            return back()->withErrors($result['errors'])->withInput();
        }

        return view('admin.import.preview', [
            'preview' => $result['preview'],
            'total_rows' => $result['total_rows'],
            'filename' => $request->file('file')->getClientOriginalName(),
        ]);
    }

    public function process(ImportRequest $request)
    {
        $result = $this->importService->processImport($request->file('file'));

        if (!empty($result['errors'])) {
            return redirect()->route('admin.import.index')
                ->with('warning', "Imported {$result['success']} records with {$result['failed']} failures.");
        }

        return redirect()->route('admin.import.index')
            ->with('success', "Successfully imported {$result['success']} records.");
    }

    public function syncGoogleSheets(Request $request)
    {
        $options = array_filter([
            'date' => $request->input('date'),
            'name' => $request->input('name'),
        ]);

        $result = $this->googleSheetsSyncService->sync($options);

        $message = sprintf(
            'Google Sheets sync complete: %d new logs, %d sessions created, %d sessions updated, %d skipped.',
            $result['imported'],
            $result['sessions_created'],
            $result['sessions_updated'],
            $result['skipped']
        );

        if (! empty($result['errors']) && $result['imported'] === 0 && $result['sessions_created'] === 0) {
            return redirect()->route('admin.import.index')
                ->with('warning', $message . ' ' . implode(' ', $result['errors']));
        }

        return redirect()->route('admin.import.index')
            ->with('success', $message);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance-import-template.csv"',
        ];

        $columns = ['timestamp', 'full_name', 'attendance'];

        $callback = function () use ($columns) {
            $handle = fopen('php://output', 'w+');
            fputcsv($handle, $columns);
            fputcsv($handle, [now()->format('n/j/Y H:i:s'), 'Juan Dela Cruz', 'Time in']);
            fputcsv($handle, [now()->addHours(4)->format('n/j/Y H:i:s'), 'Juan Dela Cruz', 'Time out']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
