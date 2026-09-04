<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-indigo-600 dark:text-indigo-400 uppercase tracking-wide font-medium">Painel do instrutor</p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Gestão de cursos
                </h2>
            </div>
            <a href="{{ route('instructor.courses.create') }}">
                <x-primary-button type="button">Novo curso</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Crie conteúdos, organize aulas, configure a avaliação final e publique quando estiver pronto.
                Alunos só veem cursos publicados.
            </p>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Curso</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Aulas</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Matrículas</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($courses as $course)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $course->title }}</div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $course->slug }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($course->is_published)
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">Publicado</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">Rascunho</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $course->lessons_count }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $course->enrollments_count }}</td>
                                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('instructor.courses.edit', $course) }}" class="text-indigo-600 hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('instructor.courses.publish', $course) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-gray-600 dark:text-gray-300 hover:underline">
                                            {{ $course->is_published ? 'Despublicar' : 'Publicar' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('courses.show', $course) }}" class="text-gray-500 hover:underline" target="_blank" rel="noopener">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                    Nenhum curso ainda. Crie o primeiro para começar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
