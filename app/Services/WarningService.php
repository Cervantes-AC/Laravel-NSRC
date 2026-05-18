<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WarningService
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Log an action failure and notify relevant users
     */
    public function logActionFailure(
        string $action,
        string $reason,
        ?User $user = null,
        array $context = [],
        string $severity = 'warning'
    ): void {
        $failureId = (string) Str::uuid();
        $timestamp = now();

        // Log to application logs
        $logMessage = "Action Failure [{$failureId}]: {$action} - {$reason}";
        if ($user) {
            $logMessage .= " (User: {$user->email})";
        }

        match ($severity) {
            'critical' => Log::critical($logMessage, $context),
            'error' => Log::error($logMessage, $context),
            default => Log::warning($logMessage, $context),
        };

        // Notify the user if provided
        if ($user) {
            $this->notifyUserOfFailure($user, $action, $reason, $severity, $failureId);
        }

        // Notify admins of critical failures
        if ($severity === 'critical') {
            $this->notifyAdminsOfCriticalFailure($action, $reason, $user, $failureId, $context);
        }
    }

    /**
     * Notify a user about an action failure
     */
    private function notifyUserOfFailure(
        User $user,
        string $action,
        string $reason,
        string $severity,
        string $failureId
    ): void {
        $message = "Action Failed: {$action}. Reason: {$reason}";

        match ($severity) {
            'critical' => $this->notificationService->sendCriticalAlert($user, $message),
            'error' => $this->notificationService->sendWarningAlert($user, $message),
            default => $this->notificationService->sendWarningAlert($user, $message),
        };
    }

    /**
     * Notify all admins of a critical failure
     */
    private function notifyAdminsOfCriticalFailure(
        string $action,
        string $reason,
        ?User $user,
        string $failureId,
        array $context
    ): void {
        $admins = User::query()
            ->where('role', 'admin')
            ->where('status', 'active')
            ->get();

        $userInfo = $user ? " by {$user->email}" : '';
        $message = "Critical Failure [{$failureId}]: {$action}{$userInfo}. Reason: {$reason}";

        foreach ($admins as $admin) {
            $this->notificationService->sendCriticalAlert($admin, $message);
        }
    }

    /**
     * Log a batch operation failure
     */
    public function logBatchOperationFailure(
        string $operationName,
        int $totalItems,
        int $failedItems,
        array $failedIds = [],
        ?User $user = null
    ): void {
        $failurePercentage = ($failedItems / $totalItems) * 100;
        $severity = $failurePercentage > 50 ? 'critical' : 'warning';

        $reason = "Batch operation failed for {$failedItems}/{$totalItems} items ({$failurePercentage}%)";
        if (!empty($failedIds)) {
            $reason .= ". Failed IDs: " . implode(', ', array_slice($failedIds, 0, 5));
            if (count($failedIds) > 5) {
                $reason .= " and " . (count($failedIds) - 5) . " more";
            }
        }

        $this->logActionFailure(
            $operationName,
            $reason,
            $user,
            [
                'total_items' => $totalItems,
                'failed_items' => $failedItems,
                'failure_percentage' => $failurePercentage,
                'failed_ids' => $failedIds,
            ],
            $severity
        );
    }

    /**
     * Log a data validation failure
     */
    public function logValidationFailure(
        string $dataType,
        array $errors,
        ?User $user = null,
        array $context = []
    ): void {
        $errorCount = count($errors);
        $errorSummary = implode('; ', array_slice($errors, 0, 3));
        if ($errorCount > 3) {
            $errorSummary .= " and " . ($errorCount - 3) . " more errors";
        }

        $reason = "Validation failed for {$dataType}: {$errorSummary}";

        $this->logActionFailure(
            "Data Validation",
            $reason,
            $user,
            array_merge($context, [
                'data_type' => $dataType,
                'error_count' => $errorCount,
                'errors' => $errors,
            ]),
            'warning'
        );
    }

    /**
     * Log an external service failure
     */
    public function logExternalServiceFailure(
        string $serviceName,
        string $reason,
        ?User $user = null,
        array $context = []
    ): void {
        $this->logActionFailure(
            "External Service: {$serviceName}",
            $reason,
            $user,
            array_merge($context, ['service' => $serviceName]),
            'error'
        );
    }

    /**
     * Log a database operation failure
     */
    public function logDatabaseFailure(
        string $operation,
        string $reason,
        ?User $user = null,
        array $context = []
    ): void {
        $this->logActionFailure(
            "Database Operation: {$operation}",
            $reason,
            $user,
            array_merge($context, ['operation' => $operation]),
            'critical'
        );
    }

    /**
     * Log an authentication/authorization failure
     */
    public function logSecurityFailure(
        string $failureType,
        string $reason,
        ?User $user = null,
        array $context = []
    ): void {
        $this->logActionFailure(
            "Security: {$failureType}",
            $reason,
            $user,
            array_merge($context, ['failure_type' => $failureType]),
            'critical'
        );
    }

    /**
     * Log a file operation failure
     */
    public function logFileOperationFailure(
        string $operation,
        string $filename,
        string $reason,
        ?User $user = null,
        array $context = []
    ): void {
        $this->logActionFailure(
            "File Operation: {$operation}",
            "Failed to {$operation} file '{$filename}': {$reason}",
            $user,
            array_merge($context, [
                'operation' => $operation,
                'filename' => $filename,
            ]),
            'error'
        );
    }

    /**
     * Log an import/export failure
     */
    public function logImportExportFailure(
        string $type,
        string $reason,
        int $processedCount = 0,
        int $totalCount = 0,
        ?User $user = null,
        array $context = []
    ): void {
        $countInfo = $totalCount > 0 ? " ({$processedCount}/{$totalCount} processed)" : '';
        $this->logActionFailure(
            ucfirst($type) . " Operation",
            "Failed to {$type} data{$countInfo}: {$reason}",
            $user,
            array_merge($context, [
                'type' => $type,
                'processed_count' => $processedCount,
                'total_count' => $totalCount,
            ]),
            'warning'
        );
    }

    /**
     * Log a backup failure
     */
    public function logBackupFailure(
        string $backupType,
        string $reason,
        ?User $user = null,
        array $context = []
    ): void {
        $this->logActionFailure(
            "Backup: {$backupType}",
            $reason,
            $user,
            array_merge($context, ['backup_type' => $backupType]),
            'critical'
        );
    }

    /**
     * Log a scheduled task failure
     */
    public function logScheduledTaskFailure(
        string $taskName,
        string $reason,
        array $context = []
    ): void {
        $this->logActionFailure(
            "Scheduled Task: {$taskName}",
            $reason,
            null,
            array_merge($context, ['task_name' => $taskName]),
            'error'
        );

        // Notify all admins about scheduled task failures
        $admins = User::query()
            ->where('role', 'admin')
            ->where('status', 'active')
            ->get();

        foreach ($admins as $admin) {
            $this->notificationService->sendWarningAlert(
                $admin,
                "Scheduled task '{$taskName}' failed: {$reason}"
            );
        }
    }

    /**
     * Log a permission/authorization failure
     */
    public function logAuthorizationFailure(
        string $action,
        ?User $user = null,
        array $context = []
    ): void {
        $userInfo = $user ? " by {$user->email}" : '';
        $this->logActionFailure(
            "Authorization",
            "Unauthorized attempt to {$action}{$userInfo}",
            $user,
            array_merge($context, ['action' => $action]),
            'warning'
        );
    }

    /**
     * Create a warning notification without logging to application logs
     */
    public function createWarningNotification(
        User $user,
        string $title,
        string $message,
        string $severity = 'warning'
    ): void {
        match ($severity) {
            'critical' => $this->notificationService->sendCriticalAlert($user, "{$title}: {$message}"),
            default => $this->notificationService->sendWarningAlert($user, "{$title}: {$message}"),
        };
    }

    /**
     * Create a system-wide warning notification
     */
    public function createSystemWarning(
        string $title,
        string $message,
        string $severity = 'warning'
    ): void {
        $admins = User::query()
            ->where('role', 'admin')
            ->where('status', 'active')
            ->get();

        foreach ($admins as $admin) {
            $this->createWarningNotification($admin, $title, $message, $severity);
        }

        Log::warning("System Warning: {$title} - {$message}");
    }
}
