<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-indigo-600 dark:text-indigo-400 uppercase tracking-wide font-medium">Administração</p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Usuários e papéis
                </h2>
            </div>
            <x-instructor-subnav active="users" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Somente administradores alteram papéis. Instrutores publicam cursos; alunos consomem o catálogo.
            </p>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Nome</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">E-mail</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Papel</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                    {{ $user->name }}
                                    @if (auth()->id() === $user->id)
                                        <span class="text-xs text-gray-400">(você)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                        {{ $user->role === \App\Enums\UserRole::Admin ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-200' : ($user->role === \App\Enums\UserRole::Instructor ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200') }}">
                                        {{ $user->role?->label() ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('instructor.users.role', $user) }}" class="inline-flex items-center gap-2 justify-end">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 text-sm">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                            @endforeach
                                        </select>
                                        <x-secondary-button type="submit">Salvar</x-secondary-button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>
