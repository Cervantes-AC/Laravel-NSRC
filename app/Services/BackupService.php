<?php

namespace App\Services;

use App\Mail\BackupEmailNotification;
use App\Models\Attendance;
use App\Models\BackupLog;
use App\Models\DutySession;
use App\Models\User;
use App\Models\VolunteerMetrics;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    protected string $backupEmail = 'aaronclydeccervantes@gmail.com';

    protected bool $sendEmailOnBackup = true;

    protected NotificationService $notifications;

    public function __construct()
    {
        $this->backupEmail = config('mail.backup_email', $this->backupEmail);
        $this->sendEmailOnBackup = config('app.send_backup_email', true);
        $this->notifications = app(NotificationService::class);
    }

    public function backupDatabase(bool $sendEmail = true): bool
    {
        $filename = 'db-backup-'.now()->format('Y-m-d_H-i-s').'.sql';
        $path = 'backups/'.$filename;
        $summary = $this->generateDatabaseSummary();

        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::connection()->getDatabaseName();
            $key = 'Tables_in_'.$dbName;
            $sql = '';

            foreach ($tables as $table) {
                $tableName = $table->$key;
                $stmt = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "-- Table: {$tableName}\n";
                $sql .= $stmt[0]->{'Create Table'}.";\n\n";

                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $columns = array_keys($rowArray);
                    $values = array_map(fn ($col) => "'".addslashes($rowArray[$col])."'", $columns);
                    $sql .= "INSERT INTO `{$tableName}` (`".implode('`, `', $columns).'`) VALUES ('.implode(', ', $values).");\n";
                }
                $sql .= "\n";
            }

            Storage::disk('local')->put($path, $sql);

            $size = strlen($sql);

            BackupLog::create([
                'type' => 'database',
                'filename' => $filename,
                'size' => $size,
                'status' => 'success',
                'details' => 'Database backup completed successfully.',
            ]);

            Log::info("Database backup created: {$filename}");

            $this->notifications->backupSuccess('database', $filename, $this->formatBytes($size));

            if ($sendEmail && $this->sendEmailOnBackup) {
                $this->sendBackupEmail('database', true, $filename, $this->formatBytes($size), 'Database backup completed successfully.', $summary);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Database backup failed: '.$e->getMessage());

            BackupLog::create([
                'type' => 'database',
                'filename' => $filename,
                'size' => 0,
                'status' => 'failed',
                'details' => $e->getMessage(),
            ]);

            $this->notifications->backupFailed('database', $e->getMessage());

            if ($sendEmail && $this->sendEmailOnBackup) {
                $this->sendBackupEmail('database', false, $filename, '0 B', $e->getMessage(), []);
            }

            return false;
        }
    }

    public function backupFileUploads(bool $sendEmail = true): bool
    {
        $filename = 'uploads-backup-'.now()->format('Y-m-d_H-i-s').'.zip';
        $path = 'backups/'.$filename;

        try {
            if (! Storage::disk('local')->exists('uploads')) {
                Log::info('No uploads directory found for backup - skipping file backup.');

                BackupLog::create([
                    'type' => 'uploads',
                    'filename' => $filename,
                    'size' => 0,
                    'status' => 'success',
                    'details' => 'No uploads directory found - skipped.',
                ]);

                return true;
            }

            $files = Storage::disk('local')->allFiles('uploads');
            $zip = new \ZipArchive;
            $zipPath = Storage::disk('local')->path($path);

            if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
                throw new \Exception('Could not create zip archive.');
            }

            foreach ($files as $file) {
                $fullPath = Storage::disk('local')->path($file);
                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, $file);
                }
            }

            $zip->close();

            $size = filesize($zipPath);

            BackupLog::create([
                'type' => 'uploads',
                'filename' => $filename,
                'size' => $size,
                'status' => 'success',
                'details' => 'File uploads backup completed.',
            ]);

            Log::info("Uploads backup created: {$filename}");

            $this->notifications->backupSuccess('files', $filename, $this->formatBytes($size));

            if ($sendEmail && $this->sendEmailOnBackup) {
                $this->sendBackupEmail('files', true, $filename, $this->formatBytes($size), 'File uploads backup completed.', []);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Uploads backup failed: '.$e->getMessage());

            BackupLog::create([
                'type' => 'uploads',
                'filename' => $filename,
                'size' => 0,
                'status' => 'failed',
                'details' => $e->getMessage(),
            ]);

            $this->notifications->backupFailed('files', $e->getMessage());

            if ($sendEmail && $this->sendEmailOnBackup) {
                $this->sendBackupEmail('files', false, $filename, '0 B', $e->getMessage(), []);
            }

            return false;
        }
    }

    public function backupFullSystem(bool $sendEmail = true): bool
    {
        $dbResult = $this->backupDatabase(false);
        $uploadsResult = $this->backupFileUploads(false);

        $success = $dbResult && $uploadsResult;

        if ($sendEmail && $this->sendEmailOnBackup) {
            $dbLog = BackupLog::where('type', 'database')->latest()->first();
            $fileLog = BackupLog::where('type', 'uploads')->latest()->first();

            $summary = [
                ['name' => 'Database', 'count' => '-', 'status' => $dbResult ? 'Success' : 'Failed'],
                ['name' => 'File Uploads', 'count' => '-', 'status' => $uploadsResult ? 'Success' : 'Failed'],
            ];

            $this->sendBackupEmail(
                'full',
                $success,
                $dbLog?->filename.', '.$fileLog?->filename,
                $this->formatBytes(($dbLog?->size ?? 0) + ($fileLog?->size ?? 0)),
                $success ? 'Full system backup completed successfully.' : 'Full system backup completed with errors.',
                $summary
            );
        }

        return $success;
    }

    public function verifyBackupIntegrity(string $path): bool
    {
        try {
            if (! Storage::disk('local')->exists($path)) {
                Log::warning("Backup file not found: {$path}");

                return false;
            }

            $fullPath = Storage::disk('local')->path($path);
            $extension = pathinfo($path, PATHINFO_EXTENSION);

            if ($extension === 'sql') {
                $content = Storage::disk('local')->get($path);

                return str_contains($content, 'CREATE TABLE');
            }

            if ($extension === 'zip') {
                $zip = new \ZipArchive;
                $result = $zip->open($fullPath);
                if ($result === true) {
                    $zip->close();

                    return true;
                }

                return false;
            }

            return file_exists($fullPath) && filesize($fullPath) > 0;
        } catch (\Exception $e) {
            Log::error('Backup integrity check failed: '.$e->getMessage());

            return false;
        }
    }

    public function scheduleBackups(): void
    {
        $lastBackup = BackupLog::where('type', 'database')
            ->where('status', 'success')
            ->latest()
            ->first();

        $shouldRun = ! $lastBackup || $lastBackup->created_at->diffInHours(now()) >= 24;

        if ($shouldRun) {
            $this->backupFullSystem();
            Log::info('Scheduled backup executed.');
        }
    }

    public function sendBackupEmailManually(int $backupLogId): bool
    {
        $backupLog = BackupLog::findOrFail($backupLogId);

        $path = 'backups/'.$backupLog->filename;
        $exists = Storage::disk('local')->exists($path);

        return $this->sendBackupEmail(
            $backupLog->type,
            $backupLog->status === 'success',
            $backupLog->filename,
            $this->formatBytes($backupLog->size),
            $backupLog->details,
            [],
            $exists ? $path : null
        );
    }

    public function cleanupOldBackups(int $maxBackups = 10): int
    {
        $deleted = 0;
        $backups = BackupLog::orderBy('created_at', 'asc')->get();

        if ($backups->count() > $maxBackups) {
            $toDelete = $backups->slice(0, $backups->count() - $maxBackups);

            foreach ($toDelete as $backup) {
                $path = 'backups/'.$backup->filename;
                if (Storage::disk('local')->exists($path)) {
                    Storage::disk('local')->delete($path);
                }
                $backup->delete();
                $deleted++;
            }
        }

        return $deleted;
    }

    protected function sendBackupEmail(string $type, bool $success, string $filename, string $size, string $details, array $summary = [], ?string $attachmentPath = null): bool
    {
        $maxRetries = 3;
        $retryDelay = 2; // seconds
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $mailable = new BackupEmailNotification($type, $success, $filename, $size, $details, $summary);

                if ($attachmentPath && Storage::disk('local')->exists($attachmentPath)) {
                    $fullPath = Storage::disk('local')->path($attachmentPath);
                    $mailable->attach($fullPath, ['as' => basename($attachmentPath)]);
                }

                Mail::to($this->backupEmail)->send($mailable);
                Log::info("Backup email sent to {$this->backupEmail}");

                return true;
            } catch (\Exception $e) {
                $lastException = $e;
                Log::warning("Failed to send backup notification email (Attempt {$attempt}/{$maxRetries}): ".$e->getMessage());

                // If this is not the last attempt, wait and retry
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                }
            }
        }

        // All retries exhausted - mark as failed
        Log::error("Failed to resend backup notification email after {$maxRetries} attempts: ".($lastException?->getMessage() ?? 'Unknown error'));
        
        // Create a notification about the failed email
        $this->notifications->backupEmailFailed($type, $filename, $lastException?->getMessage() ?? 'Failed to resend backup notification email');

        return false;
    }

    protected function generateDatabaseSummary(): array
    {
        return [
            ['name' => 'Users', 'count' => User::count(), 'status' => 'Included'],
            ['name' => 'Attendance Records', 'count' => Attendance::count(), 'status' => 'Included'],
            ['name' => 'Duty Sessions', 'count' => DutySession::count(), 'status' => 'Included'],
            ['name' => 'Volunteer Metrics', 'count' => VolunteerMetrics::count(), 'status' => 'Included'],
        ];
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }
}
