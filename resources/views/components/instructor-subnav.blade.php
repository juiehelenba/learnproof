@props([
    'active' => null,
])

<nav class="flex flex-wrap gap-2 text-sm">
    <a href="{{ route('instructor.courses.index') }}"
       class="px-3 py-1.5 rounded-md {{ ($active ?? '') === 'courses' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        Cursos
    </a>
    <a href="{{ route('instructor.metrics') }}"
       class="px-3 py-1.5 rounded-md {{ ($active ?? '') === 'metrics' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
        Métricas
    </a>
</nav>
