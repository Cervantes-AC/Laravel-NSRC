<?php

namespace App\Listeners;

use App\Models\AuditLog;

class LogDataModified
{
    public function handle($event): void
    {
        $user = $event->user ?? auth()->user();
        $changes = method_exists($event, 'changes') ? $event->changes() : ($event->changes ?? []);
        $performer = $user?->name ?? 'System';

        AuditLog::create([
            'user_id'    => $user?->id,
            'full_name'  => $user?->full_name ?? $user?->name ?? 'System',
            'type'       => 'OPERATIONS',
            'action'     => 'Data Modified',
            'details'    => json_encode([
                'model'    => $event->model ?? get_class($event),
                'changes'  => $changes,
                'performed_by' => $performer,
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp'  => now(),
        ]);
    }
}
