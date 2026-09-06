<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;

class DemoReadinessService
{
    /**
     * Checklist operacional para uma demo completa (sem expor segredos).
     *
     * @return array{
     *     ready: bool,
     *     checks: list<array{key: string, ok: bool, label: string, hint: ?string}>,
     *     accounts: list<array{email: string, role: string, exists: bool}>,
     *     course: array{slug: string, exists: bool, published: bool}|null,
     *     script: list<string>
     * }
     */
    public function assess(): array
    {
        $aiEnabled = (bool) config('learnproof.ai.enabled');
        $aiKey = filled(config('learnproof.ai.api_key'));
        $chainEnabled = (bool) config('learnproof.blockchain.enabled');
        $chainMode = (string) config('learnproof.blockchain.mode');
        $chainConfigured = filled(config('learnproof.blockchain.rpc_url'))
            && filled(config('learnproof.blockchain.contract_address'))
            && filled(config('learnproof.blockchain.wallet_private_key'));

        $accounts = collect([
            ['email' => 'aluno@learnproof.test', 'role' => 'student'],
            ['email' => 'instrutor@learnproof.test', 'role' => 'instructor'],
            ['email' => 'admin@learnproof.test', 'role' => 'admin'],
        ])->map(fn (array $row) => [
            'email' => $row['email'],
            'role' => $row['role'],
            'exists' => User::query()->where('email', $row['email'])->exists(),
        ])->all();

        $course = Course::query()->where('slug', 'fundamentos-ia-generativa')->first();

        $checks = [
            [
                'key' => 'seed_users',
                'ok' => collect($accounts)->every(fn ($a) => $a['exists']),
                'label' => 'Usuários demo no banco',
                'hint' => 'php artisan db:seed',
            ],
            [
                'key' => 'demo_course',
                'ok' => $course !== null,
                'label' => 'Curso demo "Fundamentos de IA Generativa"',
                'hint' => 'php artisan db:seed',
            ],
            [
                'key' => 'ai',
                'ok' => ! $aiEnabled || $aiKey,
                'label' => 'Tutor de IA configurado',
                'hint' => $aiEnabled && ! $aiKey ? 'Defina OPENAI_API_KEY no .env' : null,
            ],
            [
                'key' => 'blockchain',
                'ok' => ! $chainEnabled
                    || $chainMode === 'mock'
                    || ($chainMode === 'evm' && $chainConfigured),
                'label' => 'Blockchain (mock ou EVM configurado)',
                'hint' => match (true) {
                    $chainMode === 'evm' && ! $chainConfigured => 'Preencha RPC, contract e wallet; rode php artisan blockchain:setup --deploy',
                    $chainMode === 'mock' => 'Modo mock: ensaio OK. Para demo final, use BLOCKCHAIN_MODE=evm',
                    default => null,
                },
            ],
            [
                'key' => 'queue',
                'ok' => true,
                'label' => 'Fila para ancoragem EVM',
                'hint' => $chainMode === 'evm'
                    ? 'Em outro terminal: php artisan queue:listen'
                    : null,
            ],
        ];

        return [
            'ready' => collect($checks)->every(fn ($c) => $c['ok']),
            'checks' => $checks,
            'accounts' => $accounts,
            'course' => $course ? [
                'slug' => $course->slug,
                'exists' => true,
                'published' => (bool) $course->is_published,
            ] : [
                'slug' => 'fundamentos-ia-generativa',
                'exists' => false,
                'published' => false,
            ],
            'script' => $this->script(),
        ];
    }

    /**
     * @return list<string>
     */
    public function script(): array
    {
        return [
            '1. Login aluno: aluno@learnproof.test / password',
            '2. Abrir o curso Fundamentos de IA Generativa e concluir as aulas',
            '3. Fazer o quiz (nota ≥ mínima) e mostrar o certificado emitido',
            '4. Abrir o link público de verificação do certificado',
            '5. No mesmo curso, enviar 1 pergunta ao tutor de IA',
            '6. Login instrutor: instrutor@learnproof.test / password → Painel + Métricas',
            '7. Abrir /api/v1/docs e listar GET /api/v1/courses',
            '8. (Opcional) Mostrar /health e php artisan learnproof:metrics',
        ];
    }
}
