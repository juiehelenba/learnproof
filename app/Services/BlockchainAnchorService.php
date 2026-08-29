<?php

namespace App\Services;

use App\Jobs\AnchorCertificateJob;
use App\Models\Certificate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use RuntimeException;

class BlockchainAnchorService
{
    public function queueAnchor(Certificate $certificate): void
    {
        if (! $this->isRealMode()) {
            $this->mockAnchor($certificate);

            return;
        }

        AnchorCertificateJob::dispatch($certificate);
    }

    public function anchorOnChain(Certificate $certificate): string
    {
        if (! $this->isRealMode()) {
            return $this->mockAnchor($certificate);
        }

        $txHash = $this->anchorHash($certificate->content_hash);

        if ($txHash !== '') {
            $certificate->update([
                'blockchain_tx_hash' => $txHash,
                'blockchain_network' => config('learnproof.blockchain.network'),
            ]);

            Log::info('learnproof.blockchain.anchored', [
                'uuid' => $certificate->uuid,
                'tx_hash' => $txHash,
                'network' => $certificate->blockchain_network,
            ]);
        }

        return $txHash;
    }

    public function anchorHash(string $contentHash): string
    {
        $this->ensureConfigured();

        $result = $this->runCli('anchor', $contentHash);

        if (($result['status'] ?? null) === 'already_anchored') {
            return '';
        }

        $txHash = $result['txHash'] ?? null;

        if (! is_string($txHash) || ! str_starts_with($txHash, '0x')) {
            throw new RuntimeException('Resposta inválida ao ancorar certificado na blockchain.');
        }

        return $txHash;
    }

    public function verifyHashOnChain(string $contentHash): bool
    {
        if (! $this->isRealMode()) {
            return false;
        }

        $ttl = (int) config('learnproof.blockchain.verify_cache_ttl', 300);

        if ($ttl <= 0) {
            return $this->readAnchorFromChain($contentHash);
        }

        return Cache::remember(
            "learnproof:anchor-verified:{$contentHash}",
            $ttl,
            fn (): bool => $this->readAnchorFromChain($contentHash),
        );
    }

    private function readAnchorFromChain(string $contentHash): bool
    {
        $result = $this->runCli('verify', $contentHash);

        return ($result['anchored'] ?? false) === true;
    }

    public function verifyOnChain(Certificate $certificate): bool
    {
        if (! $certificate->hasIntactContentHash()) {
            return false;
        }

        if (! $certificate->isAnchoredOnChain()) {
            return false;
        }

        if ($certificate->isSimulatedAnchor() || ! $this->isRealMode()) {
            return true;
        }

        try {
            return $this->verifyHashOnChain($certificate->content_hash);
        } catch (\Throwable $e) {
            Log::warning('learnproof.blockchain.verify_failed', [
                'uuid' => $certificate->uuid,
                'error' => $e->getMessage(),
            ]);

            return true;
        }
    }

    public function isRealMode(): bool
    {
        return config('learnproof.blockchain.enabled')
            && config('learnproof.blockchain.mode') === 'evm';
    }

    public function explorerTxUrl(?string $txHash): ?string
    {
        if (blank($txHash) || ! str_starts_with($txHash, '0x') || strlen($txHash) !== 66) {
            return null;
        }

        $template = config('learnproof.blockchain.explorer_tx_url');

        return $template ? sprintf($template, $txHash) : null;
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

    private function ensureConfigured(): void
    {
        foreach (['rpc_url', 'contract_address', 'wallet_private_key'] as $key) {
            if (blank(config("learnproof.blockchain.{$key}"))) {
                throw new RuntimeException("Blockchain EVM não configurada: learnproof.blockchain.{$key}");
            }
        }

        if (! is_dir(base_path('blockchain/node_modules'))) {
            throw new RuntimeException(
                'Dependências blockchain não instaladas. Execute: cd blockchain && npm install'
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function runCli(string $command, string $contentHash): array
    {
        $cliPath = base_path('blockchain/cli.mjs');

        $result = Process::path(base_path('blockchain'))
            ->timeout((int) config('learnproof.blockchain.timeout', 120))
            ->env([
                'BLOCKCHAIN_RPC_URL' => config('learnproof.blockchain.rpc_url'),
                'BLOCKCHAIN_WALLET_PRIVATE_KEY' => config('learnproof.blockchain.wallet_private_key'),
                'BLOCKCHAIN_CONTRACT_ADDRESS' => config('learnproof.blockchain.contract_address'),
            ])
            ->run(['node', $cliPath, $command, $contentHash]);

        $output = trim($result->output());
        $errorOutput = trim($result->errorOutput());

        $decoded = json_decode($output, true);

        if (! is_array($decoded)) {
            $message = $errorOutput ?: $output ?: 'Erro desconhecido ao executar CLI blockchain.';

            throw new RuntimeException($message);
        }

        if (isset($decoded['error'])) {
            throw new RuntimeException($decoded['error']);
        }

        if (! $result->successful()) {
            throw new RuntimeException($errorOutput ?: 'Comando blockchain falhou.');
        }

        return $decoded;
    }
}
