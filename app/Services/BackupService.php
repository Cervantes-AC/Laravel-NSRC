<?php

namespace App\Services;

use App\Models\BackupLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupService
{
    public function backupDatabase(): bool
    {
        $filename = 'db-backup-' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path = 'backups/' . $filename;

        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::connection()->getDatabaseName();
            $key = 'Tables_in_' . $dbName;
            $sql = '';

            foreach ($tables as $table) {
                $tableName = $table->$key;
                $stmt = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "-- Table: {$tableName}\n";
                $sql .= $stmt[0]->{'Create Table'} . ";\n\n";

                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $columns = array_keys($rowArray);
                    $values = array_map(fn($col) => "'" . addslashes($rowArray[$col]) . "'", $columns);
                    $sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }

            Storage::disk('local')->put($path, $sql);

            BackupLog::create([
                'type' => 'database',
                'filename' => $filename,
                'size' => strlen($sql),
                'status' => 'success',
                'details' => 'Database backup completed successfully.',
            ]);

            Log::info("Database backup created: {$filename}");
            return true;
        } catch (\Exception $e) {
            Log::error("Database backup failed: " . $e->getMessage());

            BackupLog::create([
                'type' => 'database',
                'filename' => $filename,
                'size' => 0,
                'status' => 'failed',
                'details' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function backupFileUploads(): bool
    {
        $filename = 'uploads-backup-' . now()->format('Y-m-d_H-i-s') . '.zip';
        $path = 'backups/' . $filename;

        try {
            if (!Storage::disk('local')->exists('uploads')) {
                Log::warning('No uploads directory found for backup.');
                return false;
            }

            $files = Storage::disk('local')->allFiles('uploads');
            $zip = new \ZipArchive();
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

            BackupLog::create([
                'type' => 'uploads',
                'filename' => $filename,
                'size' => filesize($zipPath),
                'status' => 'success',
                'details' => 'File uploads backup completed.',
            ]);

            Log::info("Uploads backup created: {$filename}");
            return true;
        } catch (\Exception $e) {
            Log::error("Uploads backup failed: " . $e->getMessage());

            BackupLog::create([
                'type' => 'uploads',
                'filename' => $filename,
                'size' => 0,
                'status' => 'failed',
                'details' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function backupFullSystem(): bool
    {
        $dbResult = $this->backupDatabase();
        $uploadsResult = $this->backupFileUploads();

        return $dbResult && $uploadsResult;
    }

    public function verifyBackupIntegrity(string $path): bool
    {
        try {
            if (!Storage::disk('local')->exists($path)) {
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
                $zip = new \ZipArchive();
                $result = $zip->open($fullPath);
                if ($result === true) {
                    $zip->close();
                    return true;
                }
                return false;
            }

            return file_exists($fullPath) && filesize($fullPath) > 0;
        } catch (\Exception $e) {
            Log::error("Backup integrity check failed: " . $e->getMessage());
            return false;
        }
    }

    public function scheduleBackups(): void
    {
        $lastBackup = BackupLog::where('type', 'database')
            ->where('status', 'success')
            ->latest()
            ->first();

        $shouldRun = !$lastBackup || $lastBackup->created_at->diffInHours(now()) >= 24;

        if ($shouldRun) {
            $this->backupFullSystem();
            Log::info('Scheduled backup executed.');
        }
    }
}
