<?php

namespace App\Models;

use Database\Factories\BackupLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    /** @use HasFactory<BackupLogFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'filename',
        'size',
        'status',
        'details',
    ];
}
