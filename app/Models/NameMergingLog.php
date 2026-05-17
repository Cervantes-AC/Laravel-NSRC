<?php

namespace App\Models;

use Database\Factories\NameMergingLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NameMergingLog extends Model
{
    /** @use HasFactory<NameMergingLogFactory> */
    use HasFactory;

    protected $fillable = [
        'original_name',
        'merged_name',
        'similarity_score',
        'session_id',
    ];
}
