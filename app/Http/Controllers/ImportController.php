<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Services\ImportService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportController extends Controller
{
    public function __construct(
        protected ImportService $importService
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

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="import-template.csv"',
        ];

        $columns = ['full_name', 'attendance', 'date_time', 'location', 'shift_type'];

        $callback = function () use ($columns) {
            $handle = fopen('php://output', 'w+');
            fputcsv($handle, $columns);
            fputcsv($handle, ['John Doe', 'Time In', now()->format('Y-m-d H:i:s'), 'Main Campus', 'Morning']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
