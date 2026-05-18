<?php

namespace App\Models;

use Database\Factories\VolunteerMetricsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerMetrics extends Model
{
    /** @use HasFactory<VolunteerMetricsFactory> */
    use HasFactory;

    protected $fillable = [
        'volunteer_id',
        'full_name',
        'total_regular_minutes',
        'total_overtime_minutes',
        'total_undertime_minutes',
        'total_minutes',
        'invalid_record_count',
        'session_count',
    ];

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }
}
