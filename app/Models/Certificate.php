<?php

namespace App\Models;

use App\Services\BlockchainAnchorService;
use Database\Factories\CertificateFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    /** @use HasFactory<CertificateFactory> */
    use HasFactory;

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

    /**
     * Fonte única da fórmula do hash de integridade. Alterar esta função
     * invalida todos os certificados já emitidos e ancorados.
     */
    public static function contentHashFor(
        string $uuid,
        int $userId,
        int $courseId,
        DateTimeInterface $issuedAt,
    ): string {
        return hash('sha256', implode('|', [
            $uuid,
            $userId,
            $courseId,
            $issuedAt->format(DateTimeInterface::ATOM),
        ]));
    }

    public function expectedContentHash(): string
    {
        return static::contentHashFor(
            $this->uuid,
            $this->user_id,
            $this->course_id,
            $this->issued_at,
        );
    }

    public function hasIntactContentHash(): bool
    {
        return hash_equals($this->content_hash, $this->expectedContentHash());
    }

    public function isAnchoredOnChain(): bool
    {
        return filled($this->blockchain_tx_hash);
    }

    public function isSimulatedAnchor(): bool
    {
        return str_starts_with($this->blockchain_network ?? '', 'mock');
    }

    public function isBlockchainPending(): bool
    {
        return config('learnproof.blockchain.mode') === 'evm'
            && config('learnproof.blockchain.enabled')
            && blank($this->blockchain_tx_hash)
            && ! str_starts_with($this->blockchain_network ?? '', 'mock');
    }

    public function explorerTxUrl(): ?string
    {
        return app(BlockchainAnchorService::class)->explorerTxUrl($this->blockchain_tx_hash);
    }
}
