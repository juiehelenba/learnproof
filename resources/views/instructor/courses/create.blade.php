<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs text-gray-500">
                <a href="{{ route('instructor.courses.index') }}" class="hover:underline">Painel do instrutor</a>
                · Novo curso
            </p>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-0.5">
                Criar curso
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            <form method="POST" action="{{ route('instructor.courses.store') }}" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-5">
                @csrf

                <div>
                    <x-input-label for="title" value="Título" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title')" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="slug" value="Slug (opcional)" />
                    <x-text-input id="slug" name="slug" class="mt-1 block w-full font-mono text-sm" :value="old('slug')" placeholder="gerado-automaticamente" />
                    <p class="mt-1 text-xs text-gray-500">Deixe em branco para gerar a partir do título.</p>
                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Descrição" />
                    <textarea id="description" name="description" rows="5" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="passing_score" value="Nota mínima (%)" />
                    <x-text-input id="passing_score" name="passing_score" type="number" min="1" max="100" class="mt-1 block w-40" :value="old('passing_score', 70)" required />
                    <x-input-error :messages="$errors->get('passing_score')" class="mt-2" />
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published')) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    Publicar imediatamente no catálogo
                </label>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('instructor.courses.index') }}" class="text-sm text-gray-500 hover:underline">Cancelar</a>
                    <x-primary-button>Criar curso</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
