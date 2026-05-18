<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'severity',
        'category',
        'failure_reason',
        'failure_context',
        'read_at',
        'acknowledged_at',
        'acknowledged_by',
    ];

    protected $casts = [
        'id' => 'string',
        'data' => 'array',
        'failure_context' => 'array',
        'read_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $incrementing = false;

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to get unacknowledged notifications
     */
    public function scopeUnacknowledged($query)
    {
        return $query->whereNull('acknowledged_at');
    }

    /**
     * Scope to get failure notifications
     */
    public function scopeFailures($query)
    {
        return $query->whereIn('type', [
            'failure_notification',
            'batch_failure_notification',
            'critical_alert',
            'warning_alert',
        ]);
    }

    /**
     * Scope to get critical notifications
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope to get warnings
     */
    public function scopeWarnings($query)
    {
        return $query->where('severity', 'warning');
    }

    /**
     * Scope to get by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Mark notification as acknowledged
     */
    public function acknowledge(?string $acknowledgedBy = null): void
    {
        $this->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => $acknowledgedBy,
        ]);
    }

    /**
     * Check if notification is a failure
     */
    public function isFailure(): bool
    {
        return in_array($this->type, [
            'failure_notification',
            'batch_failure_notification',
            'critical_alert',
            'warning_alert',
        ]);
    }

    /**
     * Check if notification is critical
     */
    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    /**
     * Get human-readable severity label
     */
    public function getSeverityLabel(): string
    {
        return match ($this->severity) {
            'critical' => 'Critical',
            'error' => 'Error',
            'warning' => 'Warning',
            default => 'Info',
        };
    }

    /**
     * Get human-readable category label
     */
    public function getCategoryLabel(): string
    {
        return match ($this->category) {
            'system' => 'System',
            'security' => 'Security',
            'validation' => 'Validation',
            'external_service' => 'External Service',
            'database' => 'Database',
            'file' => 'File Operation',
            'import_export' => 'Import/Export',
            'backup' => 'Backup',
            'scheduled_task' => 'Scheduled Task',
            'authorization' => 'Authorization',
            default => 'General',
        };
    }
}
