<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium uppercase tracking-wide">Microcurso</p>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-0.5">
                {{ $course->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg border border-green-200 dark:border-green-800">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg border border-red-200 dark:border-red-800">{{ session('error') }}</div>
            @endif

            {{-- Sobre o curso --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Sobre este curso</h3>
                <p class="mt-3 text-gray-600 dark:text-gray-400 leading-relaxed">{{ $course->description }}</p>

                <dl class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3">
                        <dt class="text-gray-500 dark:text-gray-400">Carga horária</dt>
                        <dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">~{{ $course->totalDurationMinutes() }} min</dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3">
                        <dt class="text-gray-500 dark:text-gray-400">Aulas</dt>
                        <dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $course->lessons->count() }}</dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3">
                        <dt class="text-gray-500 dark:text-gray-400">Nota mínima</dt>
                        <dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $course->passing_score }}%</dd>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3">
                        <dt class="text-gray-500 dark:text-gray-400">Certificado</dt>
                        <dd class="mt-1 font-semibold text-emerald-600 dark:text-emerald-400">Verificável</dd>
                    </div>
                </dl>
            </div>

            {{-- O que você vai aprender --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">O que você vai aprender</h3>
                <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex gap-2"><span class="text-emerald-500 shrink-0">✓</span> Compreender o que é IA generativa e como os modelos de linguagem funcionam</li>
                    <li class="flex gap-2"><span class="text-emerald-500 shrink-0">✓</span> Escrever prompts eficazes com contexto, tarefa e formato definidos</li>
                    <li class="flex gap-2"><span class="text-emerald-500 shrink-0">✓</span> Aplicar boas práticas de ética, privacidade e LGPD no uso de IA</li>
                    <li class="flex gap-2"><span class="text-emerald-500 shrink-0">✓</span> Entender como certificados digitais são verificados com blockchain</li>
                </ul>
            </div>

            {{-- Como funciona --}}
            <div class="bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900 rounded-lg p-6">
                <h3 class="text-sm font-semibold text-indigo-900 dark:text-indigo-200">Como concluir e receber seu certificado</h3>
                <ol class="mt-3 space-y-2 text-sm text-indigo-800 dark:text-indigo-300 list-decimal list-inside">
                    <li>Matricule-se gratuitamente no curso</li>
                    <li>Assista ou leia todas as aulas na ordem sugerida</li>
                    <li>Marque cada aula como concluída ao terminar</li>
                    <li>Faça o quiz final e obtenha nota mínima de {{ $course->passing_score }}%</li>
                    <li>Receba seu certificado com link público de verificação</li>
                </ol>

                @auth
                    @if (! $enrollment)
                        <form method="POST" action="{{ route('courses.enroll', $course) }}" class="mt-5">
                            @csrf
                            <x-primary-button>Matricular-se e começar agora</x-primary-button>
                        </form>
                    @else
                        <div class="mt-5 flex flex-wrap items-center gap-4">
                            <div class="flex-1 min-w-[200px]">
                                <p class="text-sm font-medium text-indigo-900 dark:text-indigo-200">Seu progresso</p>
                                <div class="mt-2 h-2 bg-indigo-200 dark:bg-indigo-900 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-600 rounded-full transition-all" style="width: {{ $enrollment->progressPercent() }}%"></div>
                                </div>
                                <p class="mt-1 text-xs text-indigo-700 dark:text-indigo-400">{{ $enrollment->progressPercent() }}% concluído
                                    @if ($enrollment->allLessonsCompleted()) · Todas as aulas finalizadas ✓ @endif
                                </p>
                            </div>

                            @if ($certificate)
                                <a href="{{ route('certificates.show', $certificate) }}" class="inline-flex items-center text-sm font-medium text-emerald-700 dark:text-emerald-400 hover:underline">
                                    🏆 Ver meu certificado
                                </a>
                            @elseif ($enrollment->allLessonsCompleted())
                                <a href="{{ route('quizzes.show', $course) }}">
                                    <x-primary-button>Fazer avaliação final</x-primary-button>
                                </a>
                            @elseif ($course->lessons->first())
                                <a href="{{ route('lessons.show', [$course, $course->lessons->first()]) }}">
                                    <x-primary-button>
                                        {{ $enrollment->progressPercent() > 0 ? 'Continuar de onde parei' : 'Iniciar primeira aula' }}
                                    </x-primary-button>
                                </a>
                            @endif
                        </div>
                    @endif
                @else
                    <p class="mt-4 text-sm text-indigo-800 dark:text-indigo-300">
                        <a href="{{ route('login') }}" class="font-medium underline hover:no-underline">Faça login</a>
                        ou
                        <a href="{{ route('register') }}" class="font-medium underline hover:no-underline">crie uma conta gratuita</a>
                        para se matricular e acompanhar seu progresso.
                    </p>
                @endauth
            </div>

            {{-- Programa do curso --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Programa do curso</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Siga a ordem das aulas para melhor aproveitamento.</p>
                <ul class="mt-4 divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($course->lessons as $lesson)
                        @php
                            $isCompleted = in_array($lesson->id, $completedLessonIds);
                        @endphp
                        <li class="flex justify-between items-center py-4 gap-4">
                            <div class="flex gap-3 min-w-0">
                                <span class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                    {{ $isCompleted ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $isCompleted ? '✓' : $lesson->sort_order }}
                                </span>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $lesson->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Aula {{ $lesson->sort_order }} · ~{{ $lesson->duration_minutes }} min de leitura</p>
                                </div>
                            </div>
                            @if ($enrollment)
                                <a href="{{ route('lessons.show', [$course, $lesson]) }}" class="shrink-0 text-sm font-medium text-indigo-600 hover:underline">
                                    {{ $isCompleted ? 'Revisar' : 'Acessar' }}
                                </a>
                            @else
                                <span class="shrink-0 text-xs text-gray-400">Matricule-se para acessar</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            @auth
                @if ($enrollment)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6" x-data="aiTutor()">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tutor de IA do curso</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                            Tem dúvidas sobre alguma aula? Converse com o tutor de IA — ele conhece o conteúdo deste curso
                            e pode explicar conceitos, dar exemplos ou ajudar a revisar antes do quiz.
                            <span class="block mt-1 text-xs text-gray-400">Sempre valide informações críticas; o tutor pode cometer erros.</span>
                        </p>
                        <div class="mt-4 space-y-2 max-h-52 overflow-y-auto text-sm border border-gray-100 dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-gray-900/30" id="chat-history">
                            <template x-if="messages.length === 0">
                                <p class="text-gray-400 text-center py-4">Nenhuma mensagem ainda. Faça sua primeira pergunta!</p>
                            </template>
                            <template x-for="msg in messages" :key="msg.content + msg.role">
                                <div :class="msg.role === 'user' ? 'text-right' : 'text-left'">
                                    <span class="inline-block px-3 py-2 rounded-lg max-w-[85%] text-left" :class="msg.role === 'user' ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-900 dark:text-indigo-100' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600'" x-text="msg.content"></span>
                                </div>
                            </template>
                        </div>
                        <form @submit.prevent="send" class="mt-4 flex gap-2">
                            <input x-model="input" type="text" class="flex-1 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-600 shadow-sm" placeholder="Ex.: O que é alucinação em IA? Como escrever um bom prompt?" required>
                            <x-primary-button type="submit" x-bind:disabled="loading">
                                <span x-show="!loading">Enviar</span>
                                <span x-show="loading">Aguarde...</span>
                            </x-primary-button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    @auth
        @if ($enrollment)
            @push('scripts')
            <script>
                function aiTutor() {
                    return {
                        input: '',
                        loading: false,
                        messages: [],
                        async send() {
                            if (!this.input.trim()) return;
                            this.loading = true;
                            const message = this.input;
                            this.input = '';
                            this.messages.push({ role: 'user', content: message });
                            try {
                                const res = await fetch('{{ route('ai.chat', $course) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ message }),
                                });
                                const data = await res.json();
                                if (data.history) this.messages = data.history;
                                else if (data.reply) this.messages.push({ role: 'assistant', content: data.reply });
                            } catch (e) {
                                this.messages.push({ role: 'assistant', content: 'Não foi possível conectar ao tutor. Tente novamente em instantes.' });
                            }
                            this.loading = false;
                        }
                    };
                }
            </script>
            @endpush
        @endif
    @endauth
</x-app-layout>
