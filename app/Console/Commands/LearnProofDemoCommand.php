<?php

namespace App\Console\Commands;

use App\Services\DemoReadinessService;
use Illuminate\Console\Command;

class LearnProofDemoCommand extends Command
{
    protected $signature = 'learnproof:demo';

    protected $description = 'Checklist e roteiro da demo LearnProof (pronto para apresentar)';

    public function handle(DemoReadinessService $demo): int
    {
        $report = $demo->assess();

        $this->info('LearnProof — prontidão da demo');
        $this->newLine();

        foreach ($report['checks'] as $check) {
            $mark = $check['ok'] ? '<fg=green>✓</>' : '<fg=red>✗</>';
            $this->line("  {$mark} {$check['label']}");
            if (! $check['ok'] && filled($check['hint'])) {
                $this->line("      → {$check['hint']}");
            } elseif ($check['ok'] && filled($check['hint'])) {
                $this->line("      · {$check['hint']}");
            }
        }

        $this->newLine();
        $this->info('Contas demo (senha: password)');
        foreach ($report['accounts'] as $account) {
            $mark = $account['exists'] ? '✓' : '✗';
            $this->line("  [{$mark}] {$account['email']} ({$account['role']})");
        }

        $this->newLine();
        $this->info('Roteiro sugerido (≈8–12 min)');
        foreach ($report['script'] as $step) {
            $this->line("  {$step}");
        }

        $this->newLine();
        if ($report['ready']) {
            $this->info('Status: pronto para ensaiar a demo.');
        } else {
            $this->warn('Status: ainda há itens pendentes acima.');
        }

        return $report['ready'] ? self::SUCCESS : self::FAILURE;
    }
}
