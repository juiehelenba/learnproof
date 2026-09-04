<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-indigo-600 dark:text-indigo-400 uppercase tracking-wide font-medium">Painel do instrutor</p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Métricas operacionais
                </h2>
            </div>
            <x-instructor-subnav active="metrics" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Dados reais de uso — IA, certificados, fila e saúde dos serviços.
                    Atualizado em {{ \Illuminate\Support\Carbon::parse($metrics['generated_at'])->format('d/m/Y H:i') }}.
                </p>
                <div class="flex gap-2 text-sm">
                    @foreach ([1, 7, 30] as $option)
                        <a href="{{ route('instructor.metrics', ['days' => $option]) }}"
                           class="px-3 py-1 rounded-md {{ $days === $option ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200' }}">
                            {{ $option }}d
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Health --}}
            @php
                $healthTone = match ($health['status']) {
                    'ok' => 'ok',
                    'degraded' => 'warn',
                    default => 'bad',
                };
            @endphp
            <section class="space-y-3">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Saúde do sistema</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <x-metric-card
                        label="Status geral"
                        :value="strtoupper($health['status'])"
                        :tone="$healthTone"
                        hint="Endpoint público: /health"
                    />
                    @foreach ($health['checks'] as $name => $check)
                        <x-metric-card
                            :label="ucfirst($name)"
                            :value="strtoupper($check['status'])"
                            :tone="match ($check['status']) { 'ok' => 'ok', 'degraded' => 'warn', default => 'bad' }"
                            :hint="collect($check)->except('status')->map(fn ($v, $k) => is_bool($v) ? $k.': '.($v ? 'sim' : 'não') : (is_scalar($v) ? $k.': '.$v : null))->filter()->take(3)->implode(' · ')"
                        />
                    @endforeach
                </div>
            </section>

            {{-- AI --}}
            <section class="space-y-3">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Tutor de IA (últimos {{ $days }} dias)</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-metric-card label="Interações" :value="$metrics['ai']['interactions']" />
                    <x-metric-card
                        label="Taxa de fallback"
                        :value="($metrics['ai']['fallback_rate'] ?? '—').(isset($metrics['ai']['fallback_rate']) ? '%' : '')"
                        :tone="($metrics['ai']['fallback_rate'] ?? 0) > 20 ? 'warn' : 'default'"
                        :hint="$metrics['ai']['fallbacks'].' respostas sem provedor'"
                    />
                    <x-metric-card
                        label="Latência média"
                        :value="isset($metrics['ai']['avg_latency_ms']) ? $metrics['ai']['avg_latency_ms'].' ms' : '—'"
                    />
                    <x-metric-card
                        label="Custo estimado"
                        :value="'$'.number_format($metrics['ai']['estimated_cost_usd'], 4)"
                        :hint="$metrics['ai']['prompt_tokens'] + $metrics['ai']['completion_tokens'].' tokens'"
                    />
                </div>
            </section>

            {{-- Certificates + learning --}}
            <section class="space-y-3">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Certificados e aprendizagem</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-metric-card label="Certificados emitidos" :value="$metrics['certificates']['total']" />
                    <x-metric-card
                        label="Pendentes na blockchain"
                        :value="$metrics['certificates']['pending']"
                        :tone="$metrics['certificates']['pending'] > 0 ? 'warn' : 'ok'"
                    />
                    <x-metric-card label="Âncoras simuladas" :value="$metrics['certificates']['simulated']" hint="Modo mock" />
                    <x-metric-card
                        label="Aprovação no quiz"
                        :value="isset($metrics['learning']['quiz_pass_rate_period']) ? $metrics['learning']['quiz_pass_rate_period'].'%' : '—'"
                        :hint="$metrics['learning']['quiz_attempts_period'].' tentativas no período'"
                    />
                </div>
            </section>

            {{-- By course --}}
            <section class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">IA por curso</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Curso</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Interações</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Fallback</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Latência média</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($metrics['ai_by_course'] as $row)
                            <tr>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $row['course_title'] }}</td>
                                <td class="px-4 py-3">{{ $row['total'] }}</td>
                                <td class="px-4 py-3">{{ $row['fallback_rate'] !== null ? $row['fallback_rate'].'%' : '—' }}</td>
                                <td class="px-4 py-3">{{ $row['avg_latency_ms'] !== null ? $row['avg_latency_ms'].' ms' : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">Sem interações de IA no período.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            {{-- Recent --}}
            <section class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Interações recentes</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Quando</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Aluno</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Curso</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Pergunta</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">ms</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($metrics['recent_interactions'] as $row)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                                    {{ \Illuminate\Support\Carbon::parse($row['created_at'])->format('d/m H:i') }}
                                </td>
                                <td class="px-4 py-3">{{ $row['user'] ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $row['course'] ?? '—' }}</td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $row['question_preview'] }}">{{ $row['question_preview'] }}</td>
                                <td class="px-4 py-3">
                                    @if ($row['used_fallback'])
                                        <span class="text-amber-700 dark:text-amber-300">fallback</span>
                                    @else
                                        <span class="text-emerald-700 dark:text-emerald-300">ok</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $row['latency_ms'] ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">Nenhuma interação registrada ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <p class="text-xs text-gray-500">
                CLI: <code class="font-mono">php artisan learnproof:metrics --days={{ $days }}</code>
                · Fila: {{ $metrics['queue']['connection'] }}
                · Jobs pendentes: {{ $metrics['queue']['pending_jobs'] ?? '—' }}
                · Jobs falhos: {{ $metrics['queue']['failed_jobs'] ?? '—' }}
            </p>
        </div>
    </div>
</x-app-layout>
