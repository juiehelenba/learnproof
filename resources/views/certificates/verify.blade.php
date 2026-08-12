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
        <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Verificação de Certificado</h1>
        <p class="mt-2 text-sm text-gray-500">{{ config('learnproof.name') }}</p>

        <dl class="mt-6 space-y-3 text-sm">
            <div>
                <dt class="text-gray-500">Aluno</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $certificate->metadata['student_name'] ?? $certificate->user->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Curso</dt>
                <dd class="font-medium">{{ $certificate->course->title }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Emitido em</dt>
                <dd>{{ $certificate->issued_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">UUID</dt>
                <dd class="font-mono text-xs break-all">{{ $certificate->uuid }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Hash SHA-256</dt>
                <dd class="font-mono text-xs break-all">{{ $certificate->content_hash }}</dd>
            </div>
            @if ($certificate->blockchain_tx_hash)
            <div>
                <dt class="text-gray-500">TX Blockchain</dt>
                <dd class="font-mono text-xs break-all">{{ $certificate->blockchain_tx_hash }}</dd>
            </div>
            @endif
        </dl>

        <div class="mt-6 p-4 rounded-lg {{ $verified ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : 'bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-200' }}">
            @if ($verified)
                ✓ Certificado autêntico e verificado.
            @else
                ✗ Não foi possível verificar este certificado.
            @endif
        </div>
    </div>
</body>
</html>
