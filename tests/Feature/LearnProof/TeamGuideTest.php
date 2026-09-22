<?php

use App\Models\User;

test('aluno não acessa guia da equipe', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)
        ->get(route('instructor.team'))
        ->assertForbidden();
});

test('instrutor vê guia da equipe com mensagem e passos', function () {
    $instructor = User::factory()->instructor()->create();

    $this->actingAs($instructor)
        ->get(route('instructor.team'))
        ->assertOk()
        ->assertSee('Iniciativa da equipe')
        ->assertSee('Cada um ensina')
        ->assertSee('Copiar mensagem')
        ->assertSee('Para quem vai ensinar')
        ->assertSee('learnproof:demo', false);
});
