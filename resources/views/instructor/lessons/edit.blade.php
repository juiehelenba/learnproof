<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs text-gray-500">
                <a href="{{ route('instructor.courses.edit', $course) }}" class="hover:underline">{{ $course->title }}</a>
                · Editar aula
            </p>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-0.5">
                {{ $lesson->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            <form method="POST" action="{{ route('instructor.lessons.update', [$course, $lesson]) }}" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="title" value="Título" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $lesson->title)" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="slug" value="Slug" />
                    <x-text-input id="slug" name="slug" class="mt-1 block w-full font-mono text-sm" :value="old('slug', $lesson->slug)" required />
                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="duration_minutes" value="Duração (min)" />
                        <x-text-input id="duration_minutes" name="duration_minutes" type="number" min="1" class="mt-1 block w-full" :value="old('duration_minutes', $lesson->duration_minutes)" required />
                    </div>
                    <div>
                        <x-input-label for="sort_order" value="Ordem" />
                        <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full" :value="old('sort_order', $lesson->sort_order)" required />
                    </div>
                </div>

                <div>
                    <x-input-label for="video_url" value="URL do vídeo (opcional)" />
                    <x-text-input id="video_url" name="video_url" class="mt-1 block w-full" :value="old('video_url', $lesson->video_url)" />
                </div>

                <div>
                    <x-input-label for="content" value="Conteúdo (Markdown)" />
                    <textarea id="content" name="content" rows="12" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ old('content', $lesson->content) }}</textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('instructor.courses.edit', $course) }}" class="text-sm text-gray-500 hover:underline">Voltar</a>
                    <x-primary-button>Salvar alterações</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
