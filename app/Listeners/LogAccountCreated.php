<?php

namespace App\Listeners;

use App\Models\AuditLog;

class LogAccountCreated
{
    public function handle($event): void
    {
        $user = $event->user;

        AuditLog::create([
            'user_id'    => $user->id,
            'full_name'  => $user->full_name ?? $user->name,
            'type'       => 'REGISTRY',
            'action'     => 'Account Created',
            'details'    => "Account created for {$user->name} ({$user->email}) with role: {$user->role}.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp'  => now(),
        ]);
    }
}
