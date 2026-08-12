<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Meu painel — {{ config('learnproof.name') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg p-6 shadow">
                <h3 class="text-lg font-semibold">Bem-vindo à LearnProof</h3>
                <p class="mt-2 text-indigo-100 text-sm">Curso → Aulas → Quiz → Certificado verificável na blockchain + Tutor de IA.</p>
                <a href="{{ route('courses.index') }}" class="mt-4 inline-block bg-white text-indigo-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-50">
                    Explorar cursos
                </a>
            </div>

            <section>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Minhas matrículas</h3>
                @forelse ($enrollments as $enrollment)
                    <div class="mb-3 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm flex justify-between items-center">
                        <div>
                            <p class="font-medium">{{ $enrollment->course->title }}</p>
                            <p class="text-sm text-gray-500">Progresso: {{ $enrollment->progressPercent() }}%</p>
                        </div>
                        <a href="{{ route('courses.show', $enrollment->course) }}" class="text-sm text-indigo-600 hover:underline">Continuar</a>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Você ainda não está matriculado em nenhum curso.</p>
                @endforelse
            </section>

            <section>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Meus certificados</h3>
                @forelse ($certificates as $certificate)
                    <div class="mb-3 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm flex justify-between items-center">
                        <div>
                            <p class="font-medium">{{ $certificate->course->title }}</p>
                            <p class="text-xs text-gray-500 font-mono">{{ Str::limit($certificate->uuid, 18) }}</p>
                        </div>
                        <a href="{{ route('certificates.show', $certificate) }}" class="text-sm text-emerald-600 hover:underline">Ver certificado</a>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Conclua um curso e passe no quiz para emitir seu certificado.</p>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
