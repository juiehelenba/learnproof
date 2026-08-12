<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('quizzes.submit', $course) }}" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-8">
                @csrf
                <p class="text-sm text-gray-500">Nota mínima: {{ $quiz->passing_score }}%</p>

                @foreach ($quiz->questions as $question)
                    <fieldset class="space-y-3">
                        <legend class="font-medium text-gray-900 dark:text-gray-100">{{ $loop->iteration }}. {{ $question->text }}</legend>
                        @foreach ($question->options as $option)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required class="text-indigo-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $option->text }}</span>
                            </label>
                        @endforeach
                    </fieldset>
                @endforeach

                <x-primary-button>Enviar respostas</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
