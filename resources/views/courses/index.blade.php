<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Catálogo de Cursos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 text-white rounded-xl p-8 shadow-lg">
                <p class="text-indigo-200 text-sm font-medium uppercase tracking-wide">Plataforma LearnProof</p>
                <h3 class="mt-2 text-2xl font-bold">Aprenda, pratique e comprove seu conhecimento</h3>
                <p class="mt-3 text-indigo-100 max-w-2xl leading-relaxed">
                    Cursos curtos e objetivos, elaborados para você aprender no seu ritmo. Cada trilha inclui aulas
                    em texto, quiz de fixação, certificado de conclusão verificável e tutor de IA para tirar dúvidas
                    enquanto estuda.
                </p>
                <div class="mt-6 flex flex-wrap gap-4 text-sm text-indigo-100">
                    <span class="flex items-center gap-1.5">📚 Aulas em sequência</span>
                    <span class="flex items-center gap-1.5">✅ Quiz com nota mínima</span>
                    <span class="flex items-center gap-1.5">🏆 Certificado verificável</span>
                    <span class="flex items-center gap-1.5">🤖 Tutor de IA integrado</span>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Cursos disponíveis</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Escolha um curso, matricule-se gratuitamente e comece pela primeira aula.
                </p>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($courses as $course)
                        <article class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden border border-indigo-100 dark:border-indigo-900 flex flex-col">
                            <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                            <div class="p-6 flex flex-col flex-1">
                                <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">Microcurso</span>
                                <h3 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $course->title }}</h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-4 flex-1">{{ $course->description }}</p>
                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $course->lessons_count }} {{ Str::plural('aula', $course->lessons_count) }}
                                        · Quiz final · Certificado
                                    </p>
                                    <a href="{{ route('courses.show', $course) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 whitespace-nowrap">
                                        Ver detalhes →
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="text-gray-500 col-span-full">Nenhum curso publicado no momento. Volte em breve!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
