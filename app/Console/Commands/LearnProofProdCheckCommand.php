<?php

namespace App\Console\Commands;

use App\Services\HealthCheckService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class LearnProofProdCheckCommand extends Command
{
    protected $signature = 'learnproof:prod-check';

    protected $description = 'Checklist rápido de prontidão para produção (config + health)';

    public function handle(HealthCheckService $health): int
    {
        $this->info('LearnProof — verificação de produção');
        $this->newLine();

        $configOk = $this->checkConfig();
        $this->newLine();

        $migrationsOk = $this->checkMigrations();
        $this->newLine();

        $healthReport = $health->check();
        $this->info('Health (DB, cache, fila, IA, blockchain)');
        foreach ($healthReport['checks'] as $name => $check) {
            $status = (string) ($check['status'] ?? 'unknown');
            $mark = match ($status) {
                'ok' => '<fg=green>✓</>',
                'degraded' => '<fg=yellow>~</>',
                default => '<fg=red>✗</>',
            };
            $this->line("  {$mark} {$name}: {$status}");
        }
        $this->line("  · overall: {$healthReport['status']}");

        $healthOk = $healthReport['status'] !== 'down';
        $ready = $configOk && $migrationsOk && $healthOk;

        $this->newLine();
        if ($ready) {
            $this->info('Status: ambiente parece pronto (revise itens amarelos se houver).');
        } else {
            $this->warn('Status: há bloqueios — corrija os itens vermelhos acima.');
        }

        $this->line('Detalhe HTTP: GET /up e GET /health');

        return $ready ? self::SUCCESS : self::FAILURE;
    }

    private function checkConfig(): bool
    {
        $this->info('Configuração');

        $checks = [
            [
                'ok' => filled(config('app.key')),
                'label' => 'APP_KEY definida',
                'hint' => 'Rode: php artisan key:generate --force',
            ],
            [
                'ok' => ! (config('app.env') === 'production' && config('app.debug')),
                'label' => 'APP_DEBUG desligado em production',
                'hint' => 'Defina APP_DEBUG=false quando APP_ENV=production',
            ],
            [
                'ok' => filled(config('app.url')),
                'label' => 'APP_URL definida',
                'hint' => 'Ex.: https://seu-dominio.com',
            ],
            [
                'ok' => config('queue.default') !== 'sync',
                'label' => 'Fila não é sync (blockchain/IA precisam de worker)',
                'hint' => 'Use QUEUE_CONNECTION=database ou redis + queue:work',
            ],
        ];

        $allOk = true;
        foreach ($checks as $check) {
            $mark = $check['ok'] ? '<fg=green>✓</>' : '<fg=red>✗</>';
            $this->line("  {$mark} {$check['label']}");
            if (! $check['ok']) {
                $allOk = false;
                $this->line("      → {$check['hint']}");
            }
        }

        return $allOk;
    }

    private function checkMigrations(): bool
    {
        $this->info('Migrações');

        try {
            Artisan::call('migrate:status');
            $output = Artisan::output();
            $pending = substr_count($output, 'Pending');

            if ($pending > 0) {
                $this->line("  <fg=red>✗</> {$pending} migration(s) pendente(s)");
                $this->line('      → Rode: php artisan migrate --force');

                return false;
            }

            $this->line('  <fg=green>✓</> sem migrations pendentes');

            return true;
        } catch (\Throwable) {
            $this->line('  <fg=yellow>~</> não foi possível ler migrate:status (DB offline?)');

            return false;
        }
    }
}
