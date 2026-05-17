<?php

namespace App\Listeners;

use App\Models\AuditLog;

class LogRoleChanged
{
    public function handle($event): void
    {
        $user = $event->user;

        AuditLog::create([
            'user_id'    => $user->id,
            'full_name'  => $user->full_name ?? $user->name,
            'type'       => 'REGISTRY',
            'action'     => 'Role Changed',
            'details'    => "Role changed for {$user->name}: {$event->oldRole} => {$event->newRole}.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp'  => now(),
        ]);
    }
}
