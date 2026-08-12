<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $lesson->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg">{{ session('status') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 prose dark:prose-invert max-w-none">
                {!! Str::markdown($lesson->content) !!}
            </div>

            <form method="POST" action="{{ route('lessons.complete', [$course, $lesson]) }}">
                @csrf
                <x-primary-button>
                    @if (in_array($lesson->id, $completedLessonIds))
                        Marcar novamente como concluída
                    @else
                        Concluir aula
                    @endif
                </x-primary-button>
                <a href="{{ route('courses.show', $course) }}" class="ms-4 text-sm text-gray-500 hover:underline">Voltar ao curso</a>
            </form>
        </div>
    </div>
</x-app-layout>
