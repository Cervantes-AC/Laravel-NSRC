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
}
