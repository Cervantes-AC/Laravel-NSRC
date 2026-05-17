<?php

namespace App\Models;

use Database\Factories\ConversationHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationHistory extends Model
{
    /** @use HasFactory<ConversationHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'response',
        'mode',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
