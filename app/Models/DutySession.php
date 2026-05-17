<?php

namespace App\Models;

use Database\Factories\DutySessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DutySession extends Model
{
    /** @use HasFactory<DutySessionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'date',
        'time_in',
        'time_out',
        'duration_minutes',
        'status',
        'trace_id',
        'location',
        'sector',
        'integrity_score',
        'volunteer_id',
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'integrity_score' => 'float',
    ];

    protected $with = ['volunteer'];

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }
}
