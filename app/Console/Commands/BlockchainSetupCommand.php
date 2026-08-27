<?php

namespace App\Console\Commands;

use App\Services\BlockchainAnchorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class BlockchainSetupCommand extends Command
{
    protected $signature = 'blockchain:setup {--deploy : Compila e publica o contrato na testnet Amoy}';

    protected $description = 'Verifica configuração blockchain EVM e opcionalmente faz deploy do contrato';

    public function handle(BlockchainAnchorService $blockchain): int
    {
        $this->info('LearnProof — Blockchain EVM (Polygon Amoy)');
        $this->newLine();

        $mode = config('learnproof.blockchain.mode');
        $this->line("Modo atual: <fg=cyan>{$mode}</>");

        if ($mode !== 'evm') {
            $this->warn('Para blockchain real, defina BLOCKCHAIN_MODE=evm no .env');
        }

        $rpcOk = $this->isValidRpcUrl(config('learnproof.blockchain.rpc_url'));
        $keyOk = $this->isValidPrivateKey(config('learnproof.blockchain.wallet_private_key'));
        $contract = config('learnproof.blockchain.contract_address');

        $this->line(sprintf('  [%s] RPC URL: %s', $rpcOk ? '✓' : '✗', $this->maskRpc(config('learnproof.blockchain.rpc_url'))));
        $this->line(sprintf('  [%s] Contrato: %s', filled($contract) ? '✓' : '✗', filled($contract) ? $contract : 'não configurado'));
        $this->line(sprintf('  [%s] Carteira: %s', $keyOk ? '✓' : '✗', $keyOk ? '*** chave válida (64 hex) ***' : 'inválida ou placeholder'));

        if (! $rpcOk || ! $keyOk) {
            $this->newLine();
            $this->error('Configuração incompleta. Corrija o .env antes do deploy:');
            $this->line('  BLOCKCHAIN_RPC_URL=https://polygon-amoy.g.alchemy.com/v2/SUA_API_KEY_REAL');
            $this->line('  BLOCKCHAIN_WALLET_PRIVATE_KEY=0x + 64 caracteres hexadecimais');
            $this->newLine();
            $this->comment('Dicas:');
            $this->line('  • Alchemy: https://dashboard.alchemy.com → app Polygon Amoy → API Key');
            $this->line('  • MetaMask: Conta → ⋮ → Detalhes da conta → Exportar chave privada');
            $this->line('  • Faucet MATIC: https://faucet.polygon.technology/');

            return self::FAILURE;
        }

        if (! is_dir(base_path('blockchain/node_modules'))) {
            $this->newLine();
            $this->warn('Dependências Node não instaladas.');
            $this->line('Execute: cd blockchain && npm install');

            return self::FAILURE;
        }

        $this->info('Dependências Node: OK');

        if ($this->option('deploy')) {
            return $this->deployContract();
        }

        $this->newLine();
        $this->line('Deploy do contrato: php artisan blockchain:setup --deploy');
        $this->line('Teste de ancoragem: php artisan blockchain:test-anchor {hash-sha256}');

        return self::SUCCESS;
    }

    private function deployContract(): int
    {
        $this->info('Compilando contrato...');

        $compile = Process::path(base_path('blockchain'))
            ->run('npm run compile');

        if (! $compile->successful()) {
            $this->error($compile->errorOutput());

            return self::FAILURE;
        }

        $this->info('Publicando na rede Amoy (pode levar ~30s)...');

        $deploy = Process::path(base_path('blockchain'))
            ->timeout(180)
            ->env([
                'BLOCKCHAIN_RPC_URL' => config('learnproof.blockchain.rpc_url'),
                'BLOCKCHAIN_WALLET_PRIVATE_KEY' => config('learnproof.blockchain.wallet_private_key'),
            ])
            ->run('npm run deploy:amoy');

        $output = trim($deploy->output());
        $decoded = json_decode($output, true);

        if (! $deploy->successful() || ! is_array($decoded) || ! isset($decoded['contractAddress'])) {
            $this->error($deploy->errorOutput() ?: $output);

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Contrato publicado com sucesso!');
        $this->line('Endereço: '.$decoded['contractAddress']);
        $this->newLine();
        $this->warn('Adicione ao .env:');
        $this->line('BLOCKCHAIN_CONTRACT_ADDRESS='.$decoded['contractAddress']);

        return self::SUCCESS;
    }

    private function isValidRpcUrl(?string $url): bool
    {
        if (blank($url) || ! Str::startsWith($url, 'http')) {
            return false;
        }

        return ! Str::contains(Str::lower($url), ['sua_chave', 'your_key', 'placeholder']);
    }

    private function isValidPrivateKey(?string $key): bool
    {
        if (blank($key)) {
            return false;
        }

        $normalized = Str::lower(Str::replaceFirst('0x', '', $key));

        return (bool) preg_match('/^[a-f0-9]{64}$/', $normalized);
    }

    private function maskRpc(?string $url): string
    {
        if (blank($url)) {
            return 'não configurado';
        }

        return preg_replace('#(/v2/)[^/]+#', '$1***', $url) ?? $url;
    }
}
