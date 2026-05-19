<?php

namespace App\Listeners;

use App\Models\AuditLog;

class LogSystemError
{
    public function handle($event): void
    {
        AuditLog::create([
            'user_id' => $event->user?->id ?? optional(auth()->user())->id,
            'full_name' => $event->user?->full_name ?? $event->user?->name ?? optional(auth()->user())->name ?? 'System',
            'type' => 'SYSTEM',
            'action' => 'System Error',
            'details' => json_encode([
                'message' => $event->message ?? $event->exception?->getMessage() ?? 'Unknown error',
                'file' => $event->exception?->getFile(),
                'line' => $event->exception?->getLine(),
                'trace' => $event->exception?->getTraceAsString(),
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }
}
