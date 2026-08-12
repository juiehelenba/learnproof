<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'uuid',
        'content_hash',
        'blockchain_tx_hash',
        'blockchain_network',
        'issued_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function verificationUrl(): string
    {
        return route('certificates.verify', $this);
    }

    public function isAnchoredOnChain(): bool
    {
        return filled($this->blockchain_tx_hash);
    }
}
