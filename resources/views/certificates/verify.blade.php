<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Certificado — {{ config('learnproof.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-lg w-full bg-white dark:bg-gray-800 shadow-lg rounded-xl p-8">
        <p class="text-xs font-medium text-indigo-600 uppercase tracking-wide">{{ config('learnproof.name') }}</p>
        <h1 class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">Verificação de Certificado</h1>
        <p class="mt-2 text-sm text-gray-500 leading-relaxed">
            Esta página permite confirmar a autenticidade de um certificado de conclusão emitido pela plataforma.
            Os dados abaixo são públicos; informações sensíveis não são expostas.
        </p>

        <dl class="mt-6 space-y-4 text-sm">
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wide">Participante</dt>
                <dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-100">{{ $certificate->metadata['student_name'] ?? $certificate->user->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wide">Microcurso concluído</dt>
                <dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-100">{{ $certificate->course->title }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wide">Data de emissão</dt>
                <dd class="mt-0.5">{{ $certificate->issued_at->format('d/m/Y \à\s H:i') }}</dd>
            </div>
            @if(isset($certificate->metadata['quiz_score']))
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wide">Nota na avaliação final</dt>
                <dd class="mt-0.5">{{ $certificate->metadata['quiz_score'] }}%</dd>
            </div>
            @endif
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wide">Código de verificação</dt>
                <dd class="mt-0.5 font-mono text-xs break-all text-gray-700 dark:text-gray-300">{{ $certificate->uuid }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wide">Hash de integridade (SHA-256)</dt>
                <dd class="mt-0.5 font-mono text-xs break-all text-gray-700 dark:text-gray-300">{{ $certificate->content_hash }}</dd>
            </div>
            @if ($certificate->blockchain_tx_hash)
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wide">Registro blockchain</dt>
                <dd class="mt-0.5 font-mono text-xs break-all text-gray-700 dark:text-gray-300">{{ $certificate->blockchain_tx_hash }}</dd>
            </div>
            @endif
        </dl>

        <div class="mt-6 p-4 rounded-lg text-sm leading-relaxed {{ $verified ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800' : 'bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-200 border border-red-200 dark:border-red-800' }}">
            @if ($verified)
                <strong>✓ Certificado autêntico.</strong> A integridade dos dados foi confirmada. Este participante concluiu o microcurso indicado acima.
            @else
                <strong>✗ Verificação falhou.</strong> Não foi possível confirmar a autenticidade deste certificado. Entre em contato com quem o emitiu.
            @endif
        </div>
    </div>
</body>
</html>
