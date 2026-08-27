<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('courses.show', $course) }}" class="hover:underline">{{ $course->title }}</a>
                · Aula {{ $lesson->sort_order }} de {{ $course->lessons->count() }}
            </p>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-0.5">
                {{ $lesson->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg border border-green-200 dark:border-green-800">{{ session('status') }}</div>
            @endif

            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    ⏱ ~{{ $lesson->duration_minutes }} min de leitura
                </span>
                @if (in_array($lesson->id, $completedLessonIds))
                    <span class="inline-flex items-center gap-1.5 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 px-3 py-1 rounded-full">
                        ✓ Aula concluída
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 px-3 py-1 rounded-full">
                        Em andamento
                    </span>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 sm:p-8 prose dark:prose-invert max-w-none prose-headings:text-gray-900 dark:prose-headings:text-gray-100 prose-p:leading-relaxed">
                {!! Str::markdown($lesson->content) !!}
            </div>

            <div class="bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900 rounded-lg p-5">
                <p class="text-sm text-indigo-900 dark:text-indigo-200 font-medium">Antes de avançar</p>
                <p class="mt-1 text-sm text-indigo-800 dark:text-indigo-300">
                    Leia o conteúdo com atenção e marque a aula como concluída somente quando tiver compreendido os pontos principais.
                    Você pode revisitar esta aula quantas vezes quiser.
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex gap-3">
                    @if ($previousLesson)
                        <a href="{{ route('lessons.show', [$course, $previousLesson]) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                            ← Aula anterior
                        </a>
                    @endif
                    @if ($nextLesson)
                        <a href="{{ route('lessons.show', [$course, $nextLesson]) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                            Próxima aula →
                        </a>
                    @endif
                </div>

                <form method="POST" action="{{ route('lessons.complete', [$course, $lesson]) }}" class="flex items-center gap-3">
                    @csrf
                    <x-primary-button>
                        @if (in_array($lesson->id, $completedLessonIds))
                            Marcar como concluída novamente
                        @else
                            Concluir esta aula
                        @endif
                    </x-primary-button>
                </form>
            </div>

            <p class="text-center">
                <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                    ← Voltar ao programa do curso
                </a>
            </p>
        </div>
    </div>
</x-app-layout>
