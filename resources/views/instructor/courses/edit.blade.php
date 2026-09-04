<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-gray-500">
                    <a href="{{ route('instructor.courses.index') }}" class="hover:underline">Painel do instrutor</a>
                    · Editar
                </p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-0.5">
                    {{ $course->title }}
                </h2>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('instructor.courses.publish', $course) }}">
                    @csrf
                    @method('PATCH')
                    <x-secondary-button type="submit">
                        {{ $course->is_published ? 'Despublicar' : 'Publicar' }}
                    </x-secondary-button>
                </form>
                <a href="{{ route('courses.show', $course) }}" target="_blank" rel="noopener">
                    <x-secondary-button type="button">Ver como aluno</x-secondary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <x-alert />

            {{-- Dados do curso --}}
            <section class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-5">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Dados do curso</h3>

                <form method="POST" action="{{ route('instructor.courses.update', $course) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="title" value="Título" />
                        <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $course->title)" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="slug" value="Slug" />
                        <x-text-input id="slug" name="slug" class="mt-1 block w-full font-mono text-sm" :value="old('slug', $course->slug)" required />
                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Descrição" />
                        <textarea id="description" name="description" rows="4" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $course->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="passing_score" value="Nota mínima (%)" />
                        <x-text-input id="passing_score" name="passing_score" type="number" min="1" max="100" class="mt-1 block w-40"
                            :value="old('passing_score', $course->passingScore())" required />
                        <x-input-error :messages="$errors->get('passing_score')" class="mt-2" />
                    </div>

                    <input type="hidden" name="is_published" value="{{ $course->is_published ? '1' : '0' }}">

                    <div class="flex justify-end">
                        <x-primary-button>Salvar dados</x-primary-button>
                    </div>
                </form>

                @can('delete', $course)
                    <form method="POST" action="{{ route('instructor.courses.destroy', $course) }}"
                        onsubmit="return confirm('Remover este curso e todo o conteúdo associado?');"
                        class="pt-4 border-t border-gray-100 dark:border-gray-700">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>Excluir curso</x-danger-button>
                    </form>
                @endcan
            </section>

            {{-- Aulas --}}
            <section class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">Aulas</h3>
                        <p class="text-sm text-gray-500">{{ $course->lessons->count() }} aula(s) · ordem de exibição pelo campo sort</p>
                    </div>
                    <a href="{{ route('instructor.lessons.create', $course) }}">
                        <x-primary-button type="button">Nova aula</x-primary-button>
                    </a>
                </div>

                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($course->lessons as $lesson)
                        <li class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <span class="text-xs text-gray-400 mr-2">#{{ $lesson->sort_order }}</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $lesson->title }}</span>
                                <span class="text-xs text-gray-500 ml-2">{{ $lesson->duration_minutes }} min</span>
                            </div>
                            <div class="flex gap-3 text-sm">
                                <a href="{{ route('instructor.lessons.edit', [$course, $lesson]) }}" class="text-indigo-600 hover:underline">Editar</a>
                                <form method="POST" action="{{ route('instructor.lessons.destroy', [$course, $lesson]) }}"
                                    onsubmit="return confirm('Remover esta aula?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Remover</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-sm text-gray-500">Nenhuma aula cadastrada.</li>
                    @endforelse
                </ul>
            </section>

            {{-- Avaliação --}}
            <section class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Avaliação final</h3>
                    <p class="text-sm text-gray-500">A nota mínima aqui é a que realmente aprova o aluno.</p>
                </div>

                @if ($course->quiz)
                    <form method="POST" action="{{ route('instructor.quiz.update', $course) }}" class="space-y-4 max-w-xl">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="quiz_title" value="Título da avaliação" />
                            <x-text-input id="quiz_title" name="title" class="mt-1 block w-full" :value="old('title', $course->quiz->title)" required />
                        </div>
                        <div>
                            <x-input-label for="quiz_passing_score" value="Nota mínima (%)" />
                            <x-text-input id="quiz_passing_score" name="passing_score" type="number" min="1" max="100" class="mt-1 block w-40"
                                :value="old('passing_score', $course->quiz->passingScore())" required />
                        </div>
                        <x-primary-button>Salvar avaliação</x-primary-button>
                    </form>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-6 space-y-4">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100">Questões ({{ $course->quiz->questions->count() }})</h4>

                        <ul class="space-y-4">
                            @foreach ($course->quiz->questions as $question)
                                <li class="rounded-lg border border-gray-100 dark:border-gray-700 p-4">
                                    <div class="flex justify-between gap-3">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $loop->iteration }}. {{ $question->text }}</p>
                                        <form method="POST" action="{{ route('instructor.questions.destroy', [$course, $question]) }}"
                                            onsubmit="return confirm('Remover esta questão?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:underline">Remover</button>
                                        </form>
                                    </div>
                                    <ul class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                        @foreach ($question->options as $option)
                                            <li>
                                                @if ($option->is_correct)
                                                    <span class="text-emerald-600 font-medium">✓</span>
                                                @else
                                                    <span class="text-gray-400">○</span>
                                                @endif
                                                {{ $option->text }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>

                        <form method="POST" action="{{ route('instructor.questions.store', $course) }}" class="mt-6 space-y-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 p-4">
                            @csrf
                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Adicionar questão</h4>

                            <div>
                                <x-input-label for="question_text" value="Enunciado" />
                                <textarea id="question_text" name="text" rows="2" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('text') }}</textarea>
                                <x-input-error :messages="$errors->get('text')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="explanation" value="Explicação (opcional)" />
                                <x-text-input id="explanation" name="explanation" class="mt-1 block w-full" :value="old('explanation')" />
                            </div>

                            <fieldset class="space-y-3">
                                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300">Alternativas (marque a correta)</legend>
                                @for ($i = 0; $i < 4; $i++)
                                    <label class="flex items-center gap-3">
                                        <input type="radio" name="correct_option" value="{{ $i }}" @checked((int) old('correct_option', 0) === $i) required class="text-indigo-600">
                                        <input type="text" name="options[{{ $i }}][text]" value="{{ old("options.$i.text") }}"
                                            placeholder="Alternativa {{ $i + 1 }}" required
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </label>
                                @endfor
                                <x-input-error :messages="$errors->get('correct_option')" class="mt-2" />
                                <x-input-error :messages="$errors->get('options')" class="mt-2" />
                            </fieldset>

                            <x-primary-button>Adicionar questão</x-primary-button>
                        </form>
                    </div>
                @else
                    <p class="text-sm text-amber-700 dark:text-amber-300">Este curso ainda não tem quiz. Salve a avaliação abaixo para criar.</p>
                    <form method="POST" action="{{ route('instructor.quiz.update', $course) }}" class="space-y-4 max-w-xl">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="quiz_title" value="Título da avaliação" />
                            <x-text-input id="quiz_title" name="title" class="mt-1 block w-full" :value="old('title', 'Avaliação final')" required />
                        </div>
                        <div>
                            <x-input-label for="quiz_passing_score" value="Nota mínima (%)" />
                            <x-text-input id="quiz_passing_score" name="passing_score" type="number" min="1" max="100" class="mt-1 block w-40"
                                :value="old('passing_score', $course->passing_score)" required />
                        </div>
                        <x-primary-button>Criar avaliação</x-primary-button>
                    </form>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
