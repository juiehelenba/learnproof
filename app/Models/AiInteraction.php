<?php

namespace App\Models;

use Database\Factories\AiInteractionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiInteraction extends Model
{
    /** @use HasFactory<AiInteractionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'user_message_id',
        'assistant_message_id',
        'question',
        'answer',
        'status',
        'provider',
        'model',
        'used_fallback',
        'latency_ms',
        'context_chars',
        'context_snapshot',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'used_fallback' => 'boolean',
            'context_snapshot' => 'array',
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function userMessage(): BelongsTo
    {
        return $this->belongsTo(AiChatMessage::class, 'user_message_id');
    }

    public function assistantMessage(): BelongsTo
    {
        return $this->belongsTo(AiChatMessage::class, 'assistant_message_id');
    }
}
