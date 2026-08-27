<?php

namespace App\Console\Commands;

use App\Services\BlockchainAnchorService;
use Illuminate\Console\Command;

class BlockchainTestAnchorCommand extends Command
{
    protected $signature = 'blockchain:test-anchor {hash : Hash SHA-256 (64 caracteres hex)}';

    protected $description = 'Testa ancoragem e verificação de um hash na blockchain EVM';

    public function handle(BlockchainAnchorService $blockchain): int
    {
        if (! $blockchain->isRealMode()) {
            $this->error('Configure BLOCKCHAIN_MODE=evm e as variáveis de RPC/contrato/carteira.');

            return self::FAILURE;
        }

        $hash = strtolower(str_replace('0x', '', $this->argument('hash')));

        if (! preg_match('/^[a-f0-9]{64}$/', $hash)) {
            $this->error('Hash inválido. Use SHA-256 com 64 caracteres hexadecimais.');

            return self::FAILURE;
        }

        $this->info('Ancorando hash na blockchain...');

        try {
            $txHash = $blockchain->anchorHash($hash);

            if ($txHash === '') {
                $this->warn('Hash já estava ancorado.');
            } else {
                $this->info('Transação: '.$txHash);

                if ($url = $blockchain->explorerTxUrl($txHash)) {
                    $this->line('Explorer: '.$url);
                }
            }

            $this->info('Verificando on-chain...');
            $anchored = $blockchain->verifyHashOnChain($hash);
            $this->line('Ancorado: '.($anchored ? 'sim ✓' : 'não ✗'));

            return $anchored ? self::SUCCESS : self::FAILURE;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
