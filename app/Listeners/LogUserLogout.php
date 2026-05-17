<?php

namespace App\Listeners;

use App\Models\AuditLog;

class LogUserLogout
{
    public function handle($event): void
    {
        $user = $event->user ?? auth()->user();
        if (! $user) {
            return;
        }

        AuditLog::create([
            'user_id'    => $user->id,
            'full_name'  => $user->full_name ?? $user->name,
            'type'       => 'SECURITY',
            'action'     => 'User Logged Out',
            'details'    => "User {$user->name} ({$user->email}) logged out.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp'  => now(),
        ]);
    }
}
