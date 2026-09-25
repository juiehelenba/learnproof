<?php

namespace App\Http\Controllers\Instructor;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('instructor.users.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
        ]);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        $newRole = UserRole::from($request->validated('role'));

        if (
            $request->user()->is($user)
            && $user->isAdmin()
            && $newRole !== UserRole::Admin
            && User::query()->where('role', UserRole::Admin)->count() <= 1
        ) {
            return back()->with('error', 'Não é possível remover o último administrador.');
        }

        $user->update(['role' => $newRole]);

        return back()->with('status', "Papel de {$user->name} atualizado para {$newRole->label()}.");
    }
}
