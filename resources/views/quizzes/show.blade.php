<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('courses.show', $course) }}" class="hover:underline">{{ $course->title }}</a>
                · Avaliação final
            </p>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-0.5">
                {{ $quiz->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('error'))
                <div class="p-4 bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg border border-red-200 dark:border-red-800">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Instruções da avaliação</h3>
                <ul class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex gap-2"><span class="text-indigo-500">•</span> Esta avaliação contém <strong>{{ $quiz->questions->count() }} questões</strong> de múltipla escolha sobre todo o conteúdo do curso.</li>
                    <li class="flex gap-2"><span class="text-indigo-500">•</span> Você precisa atingir nota mínima de <strong>{{ $quiz->passing_score }}%</strong> para ser aprovado.</li>
                    <li class="flex gap-2"><span class="text-indigo-500">•</span> Leia cada questão com calma antes de marcar sua resposta — só é possível enviar uma vez por tentativa.</li>
                    <li class="flex gap-2"><span class="text-indigo-500">•</span> Se não atingir a nota mínima, revise as aulas e tente novamente.</li>
                    <li class="flex gap-2"><span class="text-emerald-500">•</span> Ao ser aprovado, seu <strong>certificado verificável</strong> será emitido automaticamente.</li>
                </ul>
            </div>

            <form method="POST" action="{{ route('quizzes.submit', $course) }}" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-8">
                @csrf

                @foreach ($quiz->questions as $question)
                    <fieldset class="space-y-3 pb-6 border-b border-gray-100 dark:border-gray-700 last:border-0 last:pb-0">
                        <legend class="font-medium text-gray-900 dark:text-gray-100 leading-relaxed">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 text-sm mr-2">{{ $loop->iteration }}</span>
                            {{ $question->text }}
                        </legend>
                        <div class="ml-9 space-y-2">
                            @foreach ($question->options as $option)
                                <label class="flex items-start gap-3 cursor-pointer p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required class="mt-1 text-indigo-600">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $option->text }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach

                <div class="pt-2 flex items-center justify-between">
                    <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-500 hover:underline">← Revisar aulas</a>
                    <x-primary-button>Enviar avaliação</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
