<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Certificado de Conclusão
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg">{{ session('status') }}</div>
            @endif

            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 text-white shadow-xl sm:rounded-2xl p-8 text-center border-4 border-amber-300/50">
                <p class="text-amber-200 text-sm uppercase tracking-widest">{{ config('learnproof.name') }}</p>
                <h3 class="mt-4 text-2xl font-bold">Certificado de Conclusão</h3>
                <p class="mt-6 text-lg">Concedido a</p>
                <p class="text-3xl font-semibold mt-1">{{ $certificate->metadata['student_name'] ?? $certificate->user->name }}</p>
                <p class="mt-6">por concluir com êxito o curso</p>
                <p class="text-xl font-medium mt-1">{{ $certificate->course->title }}</p>
                <p class="mt-4 text-sm opacity-90">Nota no quiz: {{ $certificate->metadata['quiz_score'] ?? '—' }}%</p>
                <p class="mt-2 text-xs opacity-75">Emitido em {{ $certificate->issued_at->format('d/m/Y H:i') }}</p>
                <p class="mt-4 font-mono text-xs break-all opacity-80">ID: {{ $certificate->uuid }}</p>
            </div>

            <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-3 text-sm">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Verificação blockchain</h4>
                <p><span class="text-gray-500">Hash do conteúdo:</span> <code class="text-xs break-all">{{ $certificate->content_hash }}</code></p>
                <p><span class="text-gray-500">Transação:</span> <code class="text-xs break-all">{{ $certificate->blockchain_tx_hash ?? 'Pendente' }}</code></p>
                <p><span class="text-gray-500">Rede:</span> {{ $certificate->blockchain_network }}</p>
                <p>
                    <span class="text-gray-500">Status:</span>
                    @if ($verified)
                        <span class="text-emerald-600 font-medium">✓ Verificado</span>
                    @else
                        <span class="text-amber-600">Aguardando verificação</span>
                    @endif
                </p>
                <p class="pt-2">
                    <a href="{{ $certificate->verificationUrl() }}" class="text-indigo-600 hover:underline" target="_blank">
                        Link público de verificação →
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
