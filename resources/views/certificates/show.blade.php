<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Certificado de Conclusão
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg border border-green-200 dark:border-green-800">{{ session('status') }}</div>
            @endif

            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg text-sm text-emerald-800 dark:text-emerald-200">
                Parabéns! Você concluiu todas as aulas, foi aprovado na avaliação final e este certificado comprova
                sua participação e aprendizado no microcurso abaixo.
            </div>

            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 text-white shadow-xl sm:rounded-2xl p-8 sm:p-10 text-center border-4 border-amber-300/50">
                <p class="text-amber-200 text-xs uppercase tracking-[0.2em]">{{ config('learnproof.name') }}</p>
                <h3 class="mt-4 text-2xl sm:text-3xl font-bold">Certificado de Conclusão</h3>
                <p class="mt-8 text-base opacity-90">Certificamos que</p>
                <p class="text-3xl sm:text-4xl font-semibold mt-2">{{ $certificate->metadata['student_name'] ?? $certificate->user->name }}</p>
                <p class="mt-8 text-base opacity-90">concluiu com êxito o microcurso</p>
                <p class="text-xl sm:text-2xl font-medium mt-2">{{ $certificate->course->title }}</p>
                <div class="mt-8 pt-6 border-t border-white/20 flex flex-wrap justify-center gap-6 text-sm opacity-90">
                    <span>Nota na avaliação: <strong>{{ $certificate->metadata['quiz_score'] ?? '—' }}%</strong></span>
                    <span>Emitido em: <strong>{{ $certificate->issued_at->format('d/m/Y') }}</strong></span>
                </div>
                <p class="mt-6 font-mono text-xs break-all opacity-70">Código de verificação: {{ $certificate->uuid }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-4 text-sm">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Sobre a verificação</h4>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    Este certificado possui um registro digital verificável. Qualquer pessoa pode confirmar sua
                    autenticidade pelo link público abaixo — útil para compartilhar com empregadores, parceiros
                    ou em seu portfólio profissional.
                </p>
                <dl class="space-y-3 pt-2">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">Hash de integridade (SHA-256)</dt>
                        <dd class="mt-1 font-mono text-xs break-all text-gray-700 dark:text-gray-300">{{ $certificate->content_hash }}</dd>
                    </div>
                    @if ($certificate->blockchain_tx_hash)
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">Registro blockchain</dt>
                        <dd class="mt-1 font-mono text-xs break-all text-gray-700 dark:text-gray-300">{{ $certificate->blockchain_tx_hash }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">Status</dt>
                        <dd class="mt-1">
                            @if ($verified)
                                <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-medium">✓ Certificado autêntico e verificado</span>
                            @else
                                <span class="text-amber-600 dark:text-amber-400">Aguardando confirmação on-chain</span>
                            @endif
                        </dd>
                    </div>
                </dl>
                <a href="{{ $certificate->verificationUrl() }}" class="inline-flex items-center mt-2 text-indigo-600 dark:text-indigo-400 font-medium hover:underline" target="_blank">
                    Compartilhar link de verificação pública →
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
