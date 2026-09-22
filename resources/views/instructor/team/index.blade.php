<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-indigo-600 dark:text-indigo-400 uppercase tracking-wide font-medium">Painel do instrutor</p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Iniciativa da equipe
                </h2>
            </div>
            <x-instructor-subnav active="team" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                Modelo <strong>“Cada um ensina”</strong>: a equipe produz microcursos práticos; a plataforma entrega aulas,
                quiz, certificado verificável e tutor de IA. Use esta página para convidar colegas e seguir o fluxo de publicação.
            </p>

            <div class="grid gap-4 sm:grid-cols-3">
                <x-metric-card label="Cursos publicados" :value="$guide['stats']['published']" tone="ok" />
                <x-metric-card label="Rascunhos" :value="$guide['stats']['draft']" :tone="$guide['stats']['draft'] > 0 ? 'warn' : 'default'" />
                <x-metric-card label="Matrículas (total)" :value="$guide['stats']['enrollments']" />
            </div>

            <section class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-4" x-data="{ copied: false }">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Mensagem para convidar a equipe</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Copie e envie no Teams, Slack ou e-mail interno.</p>
                <textarea
                    id="team-invite"
                    readonly
                    rows="5"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm font-sans"
                >{{ $guide['invite_message'] }}</textarea>
                <div class="flex flex-wrap gap-3">
                    <x-primary-button type="button"
                        @click="navigator.clipboard.writeText(document.getElementById('team-invite').value); copied = true; setTimeout(() => copied = false, 2000)">
                        <span x-show="!copied">Copiar mensagem</span>
                        <span x-show="copied" x-cloak>Copiado!</span>
                    </x-primary-button>
                    <a href="{{ route('instructor.courses.create') }}">
                        <x-secondary-button type="button">Criar curso da equipe</x-secondary-button>
                    </a>
                </div>
            </section>

            <div class="grid gap-6 md:grid-cols-2">
                <section class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Para quem vai ensinar</h3>
                    <ol class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400 list-decimal list-inside">
                        @foreach ($guide['author_steps'] as $step)
                            <li>{{ $step }}</li>
                        @endforeach
                    </ol>
                </section>

                <section class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Para quem organiza (você)</h3>
                    <ol class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400 list-decimal list-inside">
                        @foreach ($guide['organizer_steps'] as $step)
                            <li>{{ $step }}</li>
                        @endforeach
                    </ol>
                </section>
            </div>

            <p class="text-xs text-gray-500">
                Documento completo do projeto: <code class="font-mono">PROJETO.md</code> (seção “Iniciativa da equipe”).
            </p>
        </div>
    </div>
</x-app-layout>
