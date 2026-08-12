<?php

namespace App\Services;

use App\Models\Certificate;
use Illuminate\Support\Str;

class BlockchainAnchorService
{
    public function anchor(Certificate $certificate): string
    {
        $mode = config('learnproof.blockchain.mode', 'mock');

        if ($mode === 'mock' || ! config('learnproof.blockchain.enabled')) {
            return $this->mockAnchor($certificate);
        }

        // Fase 2: integrar contrato inteligente via RPC (Polygon, Base, etc.)
        return $this->mockAnchor($certificate);
    }

    private function mockAnchor(Certificate $certificate): string
    {
        $txHash = '0x'.Str::lower(Str::random(64));

        $certificate->update([
            'blockchain_tx_hash' => $txHash,
            'blockchain_network' => 'mock-'.config('learnproof.blockchain.network'),
        ]);

        return $txHash;
    }

    public function verifyOnChain(Certificate $certificate): bool
    {
        if (! $certificate->isAnchoredOnChain()) {
            return false;
        }

        if (str_starts_with($certificate->blockchain_network ?? '', 'mock')) {
            return hash_equals(
                $certificate->content_hash,
                $this->expectedHash($certificate)
            );
        }

        return true;
    }

    public function expectedHash(Certificate $certificate): string
    {
        $payload = implode('|', [
            $certificate->uuid,
            $certificate->user_id,
            $certificate->course_id,
            $certificate->issued_at->toIso8601String(),
        ]);

        return hash('sha256', $payload);
    }
}
