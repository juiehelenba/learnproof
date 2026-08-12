<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Catálogo de Cursos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <p class="text-gray-600 dark:text-gray-400 px-2">
                Plataforma <strong>LearnProof</strong> — curso online, quiz, certificado verificável e tutor de IA.
            </p>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($courses as $course)
                    <article class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden border border-indigo-100 dark:border-indigo-900">
                        <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $course->title }}</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-3">{{ $course->description }}</p>
                            <p class="mt-3 text-xs text-indigo-600 dark:text-indigo-400">{{ $course->lessons_count }} aulas · Quiz + Certificado blockchain</p>
                            <a href="{{ route('courses.show', $course) }}" class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                Ver curso →
                            </a>
                        </div>
                    </article>
                @empty
                    <p class="text-gray-500 col-span-full">Nenhum curso publicado ainda.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
