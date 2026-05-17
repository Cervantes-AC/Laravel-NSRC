<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class RunBackup extends Command
{
    protected $signature = 'backup:run {--type=database : database, files, or full}';

    protected $description = 'Run a scheduled or manual system backup';

    public function handle(BackupService $backupService): int
    {
        $type = $this->option('type');

        $success = match ($type) {
            'files' => $backupService->backupFileUploads(),
            'full' => $backupService->backupFullSystem(),
            default => $backupService->backupDatabase(),
        };

        if ($success) {
            $this->info("Backup ({$type}) completed successfully.");

            return 0;
        }

        $this->error("Backup ({$type}) failed.");

        return 1;
    }
}
