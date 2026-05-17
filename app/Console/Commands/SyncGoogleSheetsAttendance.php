<?php

namespace App\Console\Commands;

use App\Services\GoogleSheetsSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncGoogleSheetsAttendance extends Command
{
    protected $signature = 'attendance:sync-google-sheets
                            {--date= : Optional date filter (M/d/yyyy, e.g. 5/3/2026)}
                            {--name= : Optional volunteer name filter}';

    protected $description = 'Import attendance from Google Sheets and rebuild duty sessions';

    public function handle(GoogleSheetsSyncService $syncService): int
    {
        $this->info('Starting Google Sheets attendance sync...');

        $options = array_filter([
            'date' => $this->option('date'),
            'name' => $this->option('name'),
        ]);

        if (! empty($options['date'])) {
            $this->info("Filtering by date: {$options['date']}");
        }

        if (! empty($options['name'])) {
            $this->info("Filtering by name: {$options['name']}");
        }

        try {
            $result = $syncService->sync($options);

            if (! empty($result['errors']) && $result['imported'] === 0 && $result['sessions_created'] === 0) {
                $this->warn($result['errors'][0] ?? 'Sync produced no results');

                return 1;
            }

            $this->newLine();
            $this->info('Sync completed!');
            $this->line("New attendance logs: <fg=green>{$result['imported']}</>");
            $this->line("Skipped (duplicates): <fg=yellow>{$result['skipped']}</>");
            $this->line("Duty sessions created: <fg=green>{$result['sessions_created']}</>");
            $this->line("Duty sessions updated: <fg=cyan>{$result['sessions_updated']}</>");

            if (! empty($result['errors'])) {
                $this->newLine();
                $this->error('Errors encountered:');
                foreach ($result['errors'] as $error) {
                    $this->line("  - {$error}");
                }
            }

            return empty($result['errors']) ? 0 : 1;
        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            Log::error('Google Sheets attendance sync failed', [
                'error' => $e->getMessage(),
            ]);

            return 1;
        }
    }
}
