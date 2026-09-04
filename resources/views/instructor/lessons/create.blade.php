<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs text-gray-500">
                <a href="{{ route('instructor.courses.edit', $course) }}" class="hover:underline">{{ $course->title }}</a>
                · Nova aula
            </p>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-0.5">
                Adicionar aula
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            <form method="POST" action="{{ route('instructor.lessons.store', $course) }}" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-5">
                @csrf

                <div>
                    <x-input-label for="title" value="Título" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title')" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="slug" value="Slug (opcional)" />
                    <x-text-input id="slug" name="slug" class="mt-1 block w-full font-mono text-sm" :value="old('slug')" />
                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="duration_minutes" value="Duração (min)" />
                        <x-text-input id="duration_minutes" name="duration_minutes" type="number" min="1" class="mt-1 block w-full" :value="old('duration_minutes', 10)" required />
                        <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="sort_order" value="Ordem (opcional)" />
                        <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full" :value="old('sort_order')" />
                        <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="video_url" value="URL do vídeo (opcional)" />
                    <x-text-input id="video_url" name="video_url" class="mt-1 block w-full" :value="old('video_url')" />
                    <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="content" value="Conteúdo (Markdown)" />
                    <textarea id="content" name="content" rows="12" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ old('content') }}</textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('instructor.courses.edit', $course) }}" class="text-sm text-gray-500 hover:underline">Voltar</a>
                    <x-primary-button>Salvar aula</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
