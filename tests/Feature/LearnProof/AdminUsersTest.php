<?php

use App\Enums\UserRole;
use App\Models\User;

test('instrutor não acessa gestão de usuários', function () {
    $instructor = User::factory()->instructor()->create();

    $this->actingAs($instructor)
        ->get(route('instructor.users.index'))
        ->assertForbidden();
});

test('admin lista usuários e altera papel', function () {
    $admin = User::factory()->admin()->create();
    $student = User::factory()->student()->create(['name' => 'Aluno Papel']);

    $this->actingAs($admin)
        ->get(route('instructor.users.index'))
        ->assertOk()
        ->assertSee('Usuários e papéis')
        ->assertSee('Aluno Papel');

    $this->actingAs($admin)
        ->patch(route('instructor.users.role', $student), [
            'role' => UserRole::Instructor->value,
        ])
        ->assertRedirect();

    expect($student->fresh()->role)->toBe(UserRole::Instructor);
});

test('último admin não consegue rebaixar a si mesmo', function () {
    $admin = User::factory()->admin()->create();

    expect(User::query()->where('role', UserRole::Admin)->count())->toBe(1);

    $this->actingAs($admin)
        ->patch(route('instructor.users.role', $admin), [
            'role' => UserRole::Student->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($admin->fresh()->role)->toBe(UserRole::Admin);
});
