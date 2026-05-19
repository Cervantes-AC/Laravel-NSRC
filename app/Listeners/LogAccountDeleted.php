<?php

namespace App\Listeners;

use App\Models\AuditLog;

class LogAccountDeleted
{
    public function handle($event): void
    {
        $user = $event->user;

        AuditLog::create([
            'user_id' => $user->id,
            'full_name' => $user->full_name ?? $user->name,
            'type' => 'REGISTRY',
            'action' => 'Account Deleted',
            'details' => "Account deleted for {$user->name} ({$user->email}).",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }
}
