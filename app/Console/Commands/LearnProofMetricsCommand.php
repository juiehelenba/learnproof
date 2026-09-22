<?php

namespace App\Console\Commands;

use App\Services\HealthCheckService;
use App\Services\MetricsService;
use Illuminate\Console\Command;

class LearnProofMetricsCommand extends Command
{
    protected $signature = 'learnproof:metrics
                            {--days=7 : Janela em dias (1, 7 ou 30)}
                            {--csv= : Caminho do arquivo CSV (ex.: storage/app/metricas.csv)}';

    protected $description = 'Exibe métricas operacionais do LearnProof (IA, certificados, fila)';

    public function handle(MetricsService $metrics, HealthCheckService $health): int
    {
        $days = $metrics->normalizeDays((int) $this->option('days'));
        $data = $metrics->dashboard($days);
        $status = $health->check();

        $csvPath = $this->option('csv');

        if (filled($csvPath)) {
            $absolute = $this->resolveCsvPath((string) $csvPath);
            file_put_contents($absolute, $metrics->toCsv($days));
            $this->info("CSV salvo em: {$absolute}");
        }

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

    private function resolveCsvPath(string $path): string
    {
        if (str_starts_with($path, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:\\\\/', $path) === 1) {
            return $path;
        }

        return base_path($path);
    }
}
