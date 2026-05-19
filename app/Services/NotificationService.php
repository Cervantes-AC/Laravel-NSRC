<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotificationService
{
    public function sendSystemNotification(User $user, string $message): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'system_notification',
            'data' => ['message' => $message],
        ]);
    }

    public function sendWarningAlert(User $user, string $alert): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'warning_alert',
            'data' => ['message' => $alert, 'severity' => 'warning'],
        ]);
    }

    public function sendCriticalAlert(User $user, string $alert): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'critical_alert',
            'data' => ['message' => $alert, 'severity' => 'critical'],
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'full_name' => $user->full_name,
            'type' => 'SYSTEM',
            'action' => 'CRITICAL_ALERT_SENT',
            'details' => $alert,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        Log::critical("Critical alert sent to {$user->email}: {$alert}");
    }

    public function sendReminderNotification(User $user, string $reminder): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'reminder',
            'data' => ['message' => $reminder, 'type' => 'reminder'],
        ]);
    }

    public function sendSecurityAlert(User $user, string $alert): void
    {
        $this->sendCriticalAlert($user, $alert);
    }

    public function sendBackupNotification(User $user, string $type, string $status, string $details = ''): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'backup_'.$status,
            'data' => [
                'title' => 'Backup '.ucfirst($status),
                'message' => "{$type} backup ".($status === 'completed' ? 'completed successfully' : 'failed').($details ? ": {$details}" : ''),
                'action_type' => 'backup',
                'backup_type' => $type,
                'status' => $status,
                'level' => $status === 'completed' ? 'success' : 'error',
            ],
        ]);
    }

    public function sendExportNotification(User $user, string $type, string $status, string $details = ''): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'export_'.$status,
            'data' => [
                'title' => 'Export '.ucfirst($status),
                'message' => "{$type} export ".($status === 'completed' ? 'completed successfully' : 'failed').($details ? ": {$details}" : ''),
                'action_type' => 'export',
                'export_type' => $type,
                'status' => $status,
                'level' => $status === 'completed' ? 'success' : 'error',
            ],
        ]);
    }

    public function sendImportNotification(User $user, string $status, int $successCount = 0, int $failedCount = 0, string $details = ''): void
    {
        $level = $failedCount > 0 ? 'warning' : 'success';
        $message = 'Import '.($status === 'completed' ? 'completed' : 'failed');
        if ($status === 'completed') {
            $message .= ": {$successCount} records imported";
            if ($failedCount > 0) {
                $message .= ", {$failedCount} failed";
            }
        }
        if ($details) {
            $message .= ". {$details}";
        }

        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'import_'.$status,
            'data' => [
                'title' => 'Import '.ucfirst($status),
                'message' => $message,
                'action_type' => 'import',
                'status' => $status,
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'level' => $level,
            ],
        ]);
    }

    public function sendValidationNotification(User $user, string $action, string $status, string $message): void
    {
        $level = match ($status) {
            'error' => 'error',
            'warning' => 'warning',
            default => 'info',
        };

        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'validation_'.$status,
            'data' => [
                'title' => ucfirst($action).' Validation',
                'message' => $message,
                'action_type' => $action,
                'validation_status' => $status,
                'level' => $level,
            ],
        ]);
    }

    public function sendActionNotification(User $user, string $action, string $status, string $message, string $level = 'info'): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'action_'.$status,
            'data' => [
                'title' => ucfirst($action).' '.ucfirst($status),
                'message' => $message,
                'action_type' => $action,
                'status' => $status,
                'level' => $level,
            ],
        ]);
    }

    public function notifyAllAdmins(string $type, array $data): void
    {
        User::where('role', 'admin')->each(function ($admin) use ($type, $data) {
            $admin->notifications()->create([
                'id' => (string) Str::uuid(),
                'type' => $type,
                'data' => $data,
            ]);
        });
    }

    public function notifyAdmins(string $action, string $title, string $message, array $data = []): void
    {
        $notificationData = array_merge([
            'title' => $title,
            'message' => $message,
            'action_type' => $action,
            'level' => $data['level'] ?? 'info',
        ], $data);

        User::where('role', 'admin')
            ->chunkById(100, function ($admins) use ($notificationData): void {
                foreach ($admins as $admin) {
                    $admin->notifications()->create([
                        'id' => (string) Str::uuid(),
                        'type' => 'action_notification',
                        'data' => $notificationData,
                    ]);
                }
            });
    }

    public function notifyAll(string $type, string $title, string $message, array $data = []): void
    {
        $notificationData = array_merge([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'level' => $data['level'] ?? 'info',
        ], $data);

        User::where('status', 'active')
            ->chunkById(100, function ($users) use ($notificationData): void {
                foreach ($users as $user) {
                    $user->notifications()->create([
                        'id' => (string) Str::uuid(),
                        'type' => 'action_notification',
                        'data' => $notificationData,
                    ]);
                }
            });
    }

    public function backupSuccess(string $backupType, string $filename, string $size): void
    {
        $this->notifyAdmins(
            'backup',
            'Backup Completed',
            ucfirst($backupType)." backup completed successfully: {$filename} ({$size})",
            [
                'level' => 'success',
                'action' => 'backup',
                'backup_type' => $backupType,
                'filename' => $filename,
                'size' => $size,
            ]
        );
    }

    public function backupFailed(string $backupType, string $error): void
    {
        $this->notifyAdmins(
            'backup',
            'Backup Failed',
            ucfirst($backupType)." backup failed: {$error}",
            [
                'level' => 'error',
                'action' => 'backup',
                'backup_type' => $backupType,
                'error' => $error,
            ]
        );
    }

    public function backupEmailFailed(string $backupType, string $filename, string $error): void
    {
        $this->notifyAdmins(
            'backup',
            'Backup Email Notification Failed',
            "Failed to send backup notification email for {$backupType} backup ({$filename}): {$error}",
            [
                'level' => 'critical',
                'action' => 'backup_email',
                'backup_type' => $backupType,
                'filename' => $filename,
                'error' => $error,
            ]
        );
    }

    public function importSuccess(string $filename, int $success, int $failed, int $skipped): void
    {
        $message = "Import completed: {$filename} - {$success} success";
        if ($failed > 0) {
            $message .= ", {$failed} failed";
        }
        if ($skipped > 0) {
            $message .= ", {$skipped} skipped";
        }

        $this->notifyAdmins(
            'import',
            'Import Completed',
            $message,
            [
                'level' => $failed > 0 ? 'warning' : 'success',
                'action' => 'import',
                'filename' => $filename,
                'success_count' => $success,
                'failed_count' => $failed,
                'skipped_count' => $skipped,
            ]
        );
    }

    public function importValidationFailed(string $filename, array $errors): void
    {
        $errorSummary = implode(', ', array_slice($errors, 0, 3));
        if (count($errors) > 3) {
            $errorSummary .= ' and '.(count($errors) - 3).' more';
        }

        $this->notifyAdmins(
            'import',
            'Import Validation Failed',
            "File '{$filename}' failed validation: {$errorSummary}",
            [
                'level' => 'error',
                'action' => 'import',
                'filename' => $filename,
                'errors' => $errors,
            ]
        );
    }

    public function exportSuccess(string $exportType, string $filename, string $format): void
    {
        $this->notifyAdmins(
            'export',
            'Export Completed',
            ucfirst($exportType)." export ready: {$filename}.{$format}",
            [
                'level' => 'success',
                'action' => 'export',
                'export_type' => $exportType,
                'filename' => $filename,
                'format' => $format,
            ]
        );
    }

    public function exportScheduled(string $exportType, string $filename): void
    {
        $this->notifyAdmins(
            'export',
            'Export Scheduled',
            ucfirst($exportType).' export has been scheduled and will be emailed when ready.',
            [
                'level' => 'info',
                'action' => 'export',
                'export_type' => $exportType,
                'filename' => $filename,
            ]
        );
    }

    public function exportFailed(string $exportType, string $error): void
    {
        $this->notifyAdmins(
            'export',
            'Export Failed',
            ucfirst($exportType)." export failed: {$error}",
            [
                'level' => 'error',
                'action' => 'export',
                'export_type' => $exportType,
                'error' => $error,
            ]
        );
    }

    public function validationWarning(string $action, string $message): void
    {
        $this->notifyAdmins(
            'validation',
            'Validation Warning',
            $message,
            [
                'level' => 'warning',
                'action' => $action,
            ]
        );
    }

    public function validationError(string $action, string $message): void
    {
        $this->notifyAdmins(
            'validation',
            'Validation Error',
            $message,
            [
                'level' => 'error',
                'action' => $action,
            ]
        );
    }

    /**
     * Send a failure notification with detailed context
     */
    public function sendFailureNotification(
        User $user,
        string $action,
        string $reason,
        array $details = [],
        string $severity = 'warning'
    ): void {
        $message = "Action Failed: {$action}. Reason: {$reason}";

        $data = [
            'message' => $message,
            'action' => $action,
            'reason' => $reason,
            'severity' => $severity,
            'details' => $details,
            'timestamp' => now()->toIso8601String(),
        ];

        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'failure_notification',
            'data' => $data,
        ]);

        if ($severity === 'critical') {
            AuditLog::create([
                'user_id' => $user->id,
                'full_name' => $user->full_name,
                'type' => 'SYSTEM',
                'action' => 'ACTION_FAILURE',
                'details' => $message,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
            ]);
        }
    }

    /**
     * Send a batch failure notification
     */
    public function sendBatchFailureNotification(
        User $user,
        string $operationName,
        int $totalItems,
        int $failedItems,
        array $failedIds = []
    ): void {
        $failurePercentage = ($failedItems / $totalItems) * 100;
        $severity = $failurePercentage > 50 ? 'critical' : 'warning';

        $message = "Batch operation '{$operationName}' completed with failures: {$failedItems}/{$totalItems} items failed ({$failurePercentage}%)";

        $data = [
            'message' => $message,
            'operation' => $operationName,
            'total_items' => $totalItems,
            'failed_items' => $failedItems,
            'failure_percentage' => $failurePercentage,
            'failed_ids' => $failedIds,
            'severity' => $severity,
            'timestamp' => now()->toIso8601String(),
        ];

        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'batch_failure_notification',
            'data' => $data,
        ]);

        if ($severity === 'critical') {
            $this->sendCriticalAlert($user, $message);
        }
    }

    /**
     * Get unread failure notifications for a user
     */
    public function getUnreadFailureNotifications(User $user)
    {
        return $user->notifications()
            ->whereIn('type', ['failure_notification', 'batch_failure_notification', 'critical_alert', 'warning_alert'])
            ->whereNull('read_at')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get all failure notifications for a user (paginated)
     */
    public function getFailureNotifications(User $user, int $perPage = 15)
    {
        return $user->notifications()
            ->whereIn('type', ['failure_notification', 'batch_failure_notification', 'critical_alert', 'warning_alert'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
