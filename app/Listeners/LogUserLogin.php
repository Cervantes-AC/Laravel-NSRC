<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Models\AuditLog;

class LogUserLogin
{
    public function handle(UserLoggedIn $event): void
    {
        AuditLog::create([
            'user_id'    => $event->user->id,
            'full_name'  => $event->user->full_name ?? $event->user->name,
            'type'       => 'SECURITY',
            'action'     => 'User Logged In',
            'details'    => "User {$event->user->name} ({$event->user->email}) logged in.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp'  => now(),
        ]);

        $failedCount = optional($event->user)->failed_login_attempts ?? 0;
        if ($failedCount >= 3) {
            try {
                app(\App\Services\NotificationService::class)?->sendSecurityAlert(
                    $event->user,
                    "Multiple failed login attempts ($failedCount) before successful login."
                );
            } catch (\Throwable) {
            }
        }
    }
}
