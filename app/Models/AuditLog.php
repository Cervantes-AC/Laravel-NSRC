<?php

namespace App\Models;

use Database\Factories\AuditLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    /** @use HasFactory<AuditLogFactory> */
    use HasFactory;

    const TYPES = ['SECURITY', 'REGISTRY', 'OPERATIONS', 'SYSTEM', 'ACCESS'];

    protected $fillable = [
        'user_id',
        'full_name',
        'type',
        'action',
        'details',
        'ip_address',
        'user_agent',
        'timestamp',
        'archived_at',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
