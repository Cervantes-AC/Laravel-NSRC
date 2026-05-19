<?php

namespace App\Http\Controllers;

use App\Models\BackupLog;
use App\Services\BackupService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function __construct(
        protected BackupService $backupService,
        protected NotificationService $notificationService
    ) {}

    public function index()
    {
        $backupLogs = BackupLog::latest()->paginate(15);
        $totalSize = BackupLog::where('status', 'success')->sum('size');

        return view('admin.backup.index', compact('backupLogs', 'totalSize'));
    }

    public function runBackup(Request $request)
    {
        $request->validate([
            'type' => 'required|in:database,files,full',
            'send_email' => 'sometimes|boolean',
        ]);

        $sendEmail = $request->boolean('send_email', true);
        $user = Auth::user();

        $validationErrors = $this->validateBackupPreconditions($request->type);
        if (! empty($validationErrors)) {
            $this->notificationService->sendValidationNotification(
                $user,
                'backup',
                'error',
                implode('. ', $validationErrors)
            );

            return redirect()->route('admin.backup.index')->with('error', implode('. ', $validationErrors));
        }

        $this->notificationService->sendActionNotification($user, 'backup', 'started', "Starting {$request->type} backup...", 'info');

        $success = match ($request->type) {
            'database' => $this->backupService->backupDatabase($sendEmail),
            'files' => $this->backupService->backupFileUploads($sendEmail),
            'full' => $this->backupService->backupFullSystem($sendEmail),
        };

        $this->backupService->cleanupOldBackups(10);

        if ($success) {
            $this->notificationService->sendBackupNotification($user, $request->type, 'completed');

            return redirect()->route('admin.backup.index')->with('success', 'Backup completed successfully.'.($sendEmail ? ' Notification email sent.' : ''));
        }

        $this->notificationService->sendBackupNotification($user, $request->type, 'failed', 'Backup operation failed');

        return redirect()->route('admin.backup.index')->with('error', 'Backup failed. Please check the logs.');
    }

    protected function validateBackupPreconditions(string $type): array
    {
        $errors = [];

        $disk = Storage::disk('local');
        $backupsPath = $disk->path('backups');

        if (! is_dir($backupsPath)) {
            @mkdir($backupsPath, 0755, true);
        }

        $freeSpace = @disk_free_space($backupsPath);
        if ($freeSpace !== false) {
            $freeMB = $freeSpace / 1024 / 1024;
            if ($freeMB < 100) {
                $errors[] = 'Insufficient disk space. Only '.round($freeMB, 2).' MB available. At least 100 MB required.';
            }
        }

        $recentBackup = BackupLog::where('status', 'success')
            ->where('created_at', '>', now()->subMinutes(5))
            ->first();

        if ($recentBackup) {
            $errors[] = 'A backup was recently completed ('.$recentBackup->created_at->diffForHumans().'). Please wait before running another backup.';
        }

        return $errors;
    }

    public function download($id)
    {
        $backupLog = BackupLog::findOrFail($id);

        $path = 'backups/'.$backupLog->filename;

        if (! Storage::disk('local')->exists($path)) {
            $this->notificationService->sendValidationNotification(
                Auth::user(),
                'backup',
                'error',
                'Backup file not found: '.$backupLog->filename
            );

            return redirect()->route('admin.backup.index')->with('error', 'Backup file not found.');
        }

        return Storage::disk('local')->download($path, $backupLog->filename);
    }

    public function resendEmail($id)
    {
        $success = $this->backupService->sendBackupEmailManually($id);

        if ($success) {
            $this->notificationService->sendActionNotification(
                Auth::user(),
                'backup',
                'email_resent',
                'Backup notification email resent successfully',
                'success'
            );

            return redirect()->route('admin.backup.index')->with('success', 'Backup notification email resent.');
        }

        $this->notificationService->sendActionNotification(
            Auth::user(),
            'backup',
            'email_failed',
            'Failed to resend backup notification email',
            'error'
        );

        return redirect()->route('admin.backup.index')->with('error', 'Failed to resend backup notification email.');
    }

    public function toggleEmailNotifications(Request $request)
    {
        $request->validate([
            'enabled' => 'required|boolean',
        ]);

        $enabled = $request->boolean('enabled');

        if (file_exists(app()->environmentFilePath())) {
            $env = file_get_contents(app()->environmentFilePath());
            $key = 'SEND_BACKUP_EMAIL=';
            $newValue = $key.($enabled ? 'true' : 'false');

            if (preg_match('/^'.preg_quote($key, '/').'/m', $env)) {
                $env = preg_replace('/^'.preg_quote($key, '/').'.*$/m', $newValue, $env);
            } else {
                $env .= "\n".$newValue."\n";
            }

            file_put_contents(app()->environmentFilePath(), $env);
        }

        config(['app.send_backup_email' => $enabled]);

        $this->notificationService->sendActionNotification(
            Auth::user(),
            'backup',
            'email_notifications_'.($enabled ? 'enabled' : 'disabled'),
            'Backup email notifications '.($enabled ? 'enabled' : 'disabled'),
            'info'
        );

        return redirect()->route('admin.backup.index')->with('success', 'Email notifications '.($enabled ? 'enabled' : 'disabled').'.');
    }
}
