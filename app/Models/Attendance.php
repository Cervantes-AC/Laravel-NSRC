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
    ];

    protected $casts = [
        'date_time' => 'datetime',
    ];
}
