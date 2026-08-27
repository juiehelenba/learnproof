<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Meu painel de estudos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl p-8 shadow-lg">
                <p class="text-indigo-200 text-sm font-medium">Olá, {{ Auth::user()->name }} 👋</p>
                <h3 class="mt-1 text-xl font-bold">Bem-vindo à {{ config('learnproof.name') }}</h3>
                <p class="mt-3 text-indigo-100 max-w-2xl leading-relaxed">
                    Aqui você acompanha suas matrículas, retoma cursos de onde parou e acessa os certificados
                    que conquistou. Cada curso tem aulas sequenciais, avaliação final e certificado verificável
                    ao ser aprovado.
                </p>
                <a href="{{ route('courses.index') }}" class="mt-5 inline-block bg-white text-indigo-700 px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-indigo-50 transition">
                    Explorar catálogo de cursos
                </a>
            </div>

            <section>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Cursos em andamento</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-4">Retome de onde parou ou conclua o quiz para receber seu certificado.</p>
                @forelse ($enrollments as $enrollment)
                    <div class="mb-4 bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex flex-wrap justify-between items-start gap-4">
                            <div class="flex-1 min-w-[200px]">
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $enrollment->course->title }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Matriculado em {{ $enrollment->enrolled_at->format('d/m/Y') }}
                                </p>
                                <div class="mt-3 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden max-w-xs">
                                    <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $enrollment->progressPercent() }}%"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">{{ $enrollment->progressPercent() }}% concluído</p>
                            </div>
                            <a href="{{ route('courses.show', $enrollment->course) }}" class="shrink-0 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                                {{ $enrollment->progressPercent() >= 100 ? 'Fazer quiz' : 'Continuar curso' }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-6 text-center">
                        <p class="text-gray-500 dark:text-gray-400">Você ainda não está matriculado em nenhum curso.</p>
                        <a href="{{ route('courses.index') }}" class="mt-3 inline-block text-sm text-indigo-600 hover:underline">Ver cursos disponíveis →</a>
                    </div>
                @endforelse
            </section>

            <section>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Certificados conquistados</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-4">Comprovantes de conclusão com verificação pública de autenticidade.</p>
                @forelse ($certificates as $certificate)
                    <div class="mb-4 bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-emerald-100 dark:border-emerald-900/50 flex flex-wrap justify-between items-center gap-4">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $certificate->course->title }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Emitido em {{ $certificate->issued_at->format('d/m/Y') }}
                                @if(isset($certificate->metadata['quiz_score']))
                                    · Nota: {{ $certificate->metadata['quiz_score'] }}%
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 font-mono mt-1">ID: {{ Str::limit($certificate->uuid, 24) }}</p>
                        </div>
                        <a href="{{ route('certificates.show', $certificate) }}" class="shrink-0 text-sm font-medium text-emerald-600 hover:underline">
                            Ver certificado →
                        </a>
                    </div>
                @empty
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-6 text-center">
                        <p class="text-gray-500 dark:text-gray-400">Nenhum certificado ainda. Conclua um curso e passe na avaliação final para receber o seu.</p>
                    </div>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
