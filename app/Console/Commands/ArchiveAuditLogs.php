<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;

class ArchiveAuditLogs extends Command
{
    protected $signature = 'audit:archive {--days= : Archive logs older than this many days}';

    protected $description = 'Marks old audit logs as archived so active log views stay focused.';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: 90);
        $cutoff = now()->subDays(max(1, $days));

        $count = AuditLog::whereNull('archived_at')
            ->where('timestamp', '<', $cutoff)
            ->update(['archived_at' => now()]);

        $this->info("Archived {$count} audit log(s) older than {$days} days.");

        return self::SUCCESS;
    }
}
