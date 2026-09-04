@props([
    'label',
    'value',
    'hint' => null,
    'tone' => 'default',
])

@php
    $tones = [
        'default' => 'border-gray-100 dark:border-gray-700',
        'ok' => 'border-emerald-200 dark:border-emerald-800',
        'warn' => 'border-amber-200 dark:border-amber-800',
        'bad' => 'border-red-200 dark:border-red-800',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-5 border '.$tones[$tone]]) }}>
    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $label }}</p>
    <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $hint }}</p>
    @endif
</div>
