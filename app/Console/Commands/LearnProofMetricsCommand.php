<?php

namespace App\Console\Commands;

use App\Services\HealthCheckService;
use App\Services\MetricsService;
use Illuminate\Console\Command;

class LearnProofMetricsCommand extends Command
{
    protected $signature = 'learnproof:metrics {--days=7 : Janela em dias (1, 7 ou 30)}';

    protected $description = 'Exibe métricas operacionais do LearnProof (IA, certificados, fila)';

    public function handle(MetricsService $metrics, HealthCheckService $health): int
    {
        $days = (int) $this->option('days');
        $days = in_array($days, [1, 7, 30], true) ? $days : 7;

        $data = $metrics->dashboard($days);
        $status = $health->check();

        $this->info("LearnProof — métricas ({$days} dias) · health: {$status['status']}");
        $this->newLine();

        $this->table(
            ['Indicador', 'Valor'],
            [
                ['Interações IA', $data['ai']['interactions']],
                ['Taxa de fallback', $data['ai']['fallback_rate'] !== null ? $data['ai']['fallback_rate'].'%' : '—'],
                ['Latência média (ms)', $data['ai']['avg_latency_ms'] ?? '—'],
                ['Custo estimado (USD)', $data['ai']['estimated_cost_usd']],
                ['Certificados', $data['certificates']['total']],
                ['Âncoras pendentes', $data['certificates']['pending']],
                ['Simulados (mock)', $data['certificates']['simulated']],
                ['Jobs falhos', $data['queue']['failed_jobs'] ?? '—'],
                ['Taxa de aprovação quiz', $data['learning']['quiz_pass_rate_period'] !== null ? $data['learning']['quiz_pass_rate_period'].'%' : '—'],
            ]
        );

        return self::SUCCESS;
    }
}
