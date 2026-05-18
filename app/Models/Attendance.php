<?php

namespace App\Models;

use Database\Factories\AttendanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    /** @use HasFactory<AttendanceFactory> */
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'full_name',
        'attendance',
        'date_time',
        'location',
        'shift_type',
        'source_signature',
        'source_payload',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'source_payload' => 'array',
    ];
}
