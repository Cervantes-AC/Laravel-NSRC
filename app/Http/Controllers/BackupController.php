<?php

namespace App\Http\Controllers;

use App\Models\BackupLog;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function __construct(
        protected BackupService $backupService
    ) {}

    public function index()
    {
        $backupLogs = BackupLog::latest()->paginate(15);
        return view('admin.backup.index', compact('backupLogs'));
    }

    public function runBackup(Request $request)
    {
        $request->validate([
            'type' => 'required|in:database,files,full',
        ]);

        $success = match ($request->type) {
            'database' => $this->backupService->backupDatabase(),
            'files' => $this->backupService->backupFileUploads(),
            'full' => $this->backupService->backupFullSystem(),
        };

        if ($success) {
            return redirect()->route('admin.backup.index')->with('success', 'Backup completed successfully.');
        }

        return redirect()->route('admin.backup.index')->with('error', 'Backup failed. Please check the logs.');
    }

    public function download($id)
    {
        $backupLog = BackupLog::findOrFail($id);

        $path = 'backups/' . $backupLog->filename;

        if (!Storage::disk('local')->exists($path)) {
            return redirect()->route('admin.backup.index')->with('error', 'Backup file not found.');
        }

        return Storage::disk('local')->download($path, $backupLog->filename);
    }
}
