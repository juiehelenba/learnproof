@props(['status' => null, 'error' => null])

@if ($status ?? session('status'))
    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200 rounded-lg border border-emerald-200 dark:border-emerald-800">
        {{ $status ?? session('status') }}
    </div>
@endif

@if ($error ?? session('error'))
    <div class="p-4 bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg border border-red-200 dark:border-red-800">
        {{ $error ?? session('error') }}
    </div>
@endif
