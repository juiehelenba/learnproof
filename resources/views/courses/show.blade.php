<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-600 dark:text-gray-400">{{ $course->description }}</p>

                @auth
                    @if (! $enrollment)
                        <form method="POST" action="{{ route('courses.enroll', $course) }}" class="mt-4">
                            @csrf
                            <x-primary-button>Matricular-se gratuitamente</x-primary-button>
                        </form>
                    @else
                        <p class="mt-4 text-sm text-indigo-600 dark:text-indigo-400">
                            Progresso: {{ $enrollment->progressPercent() }}%
                            @if ($enrollment->allLessonsCompleted())
                                · Aulas concluídas ✓
                            @endif
                        </p>
                    @endif

                    @if ($certificate)
                        <a href="{{ route('certificates.show', $certificate) }}" class="mt-4 inline-block text-sm font-medium text-emerald-600 hover:underline">
                            Ver meu certificado →
                        </a>
                    @elseif ($enrollment?->allLessonsCompleted())
                        <a href="{{ route('quizzes.show', $course) }}" class="mt-4 inline-flex">
                            <x-primary-button>Fazer quiz final</x-primary-button>
                        </a>
                    @endif
                @else
                    <p class="mt-4 text-sm"><a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Entre</a> para se matricular.</p>
                @endauth
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Aulas</h3>
                <ul class="space-y-2">
                    @foreach ($course->lessons as $lesson)
                        <li class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 py-2">
                            <span>{{ $lesson->sort_order }}. {{ $lesson->title }}</span>
                            @if ($enrollment)
                                <a href="{{ route('lessons.show', [$course, $lesson]) }}" class="text-sm text-indigo-600 hover:underline">Abrir</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            @auth
                @if ($enrollment)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6" x-data="aiTutor()">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Tutor de IA</h3>
                        <p class="text-sm text-gray-500 mb-4">Tire dúvidas sobre o conteúdo do curso.</p>
                        <div class="space-y-2 max-h-48 overflow-y-auto mb-4 text-sm" id="chat-history">
                            <template x-for="msg in messages" :key="msg.content + msg.role">
                                <div :class="msg.role === 'user' ? 'text-right' : 'text-left'">
                                    <span class="inline-block px-3 py-1 rounded-lg" :class="msg.role === 'user' ? 'bg-indigo-100 dark:bg-indigo-900' : 'bg-gray-100 dark:bg-gray-700'" x-text="msg.content"></span>
                                </div>
                            </template>
                        </div>
                        <form @submit.prevent="send" class="flex gap-2">
                            <input x-model="input" type="text" class="flex-1 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-600" placeholder="Pergunte algo sobre o curso..." required>
                            <x-primary-button type="submit" x-bind:disabled="loading">Enviar</x-primary-button>
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
                                this.messages.push({ role: 'assistant', content: 'Erro ao conectar com o tutor de IA.' });
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
