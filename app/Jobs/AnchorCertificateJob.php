<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Services\BlockchainAnchorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AnchorCertificateJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public Certificate $certificate,
    ) {}

    public function handle(BlockchainAnchorService $blockchain): void
    {
        if ($this->certificate->fresh()?->blockchain_tx_hash) {
            return;
        }

        $blockchain->anchorOnChain($this->certificate);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Falha ao ancorar certificado na blockchain', [
            'certificate_uuid' => $this->certificate->uuid,
            'error' => $exception->getMessage(),
        ]);
    }
}
