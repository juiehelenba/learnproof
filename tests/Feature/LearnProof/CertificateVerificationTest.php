<?php

use App\Models\Certificate;
use App\Models\User;

test('dono do certificado vê a página autenticada', function () {
    $user = User::factory()->create();
    $certificate = Certificate::factory()->for($user)->simulated()->create();

    $this->actingAs($user)
        ->get(route('certificates.show', $certificate))
        ->assertOk()
        ->assertSee($certificate->uuid);
});

test('outro usuário não vê certificado alheio', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $certificate = Certificate::factory()->for($owner)->create();

    $this->actingAs($stranger)
        ->get(route('certificates.show', $certificate))
        ->assertForbidden();
});

test('verificação pública distingue âncora simulada de autêntica', function () {
    $certificate = Certificate::factory()->simulated()->create();

    $this->get(route('certificates.verify', $certificate))
        ->assertOk()
        ->assertSee('registro simulado', false)
        ->assertSee('ambiente de demonstração', false)
        ->assertDontSee('✓ Certificado autêntico', false);
});

test('verificação pública não exibe a nota da avaliação', function () {
    $certificate = Certificate::factory()->simulated()->create([
        'metadata' => [
            'student_name' => 'Maria Teste',
            'course_title' => 'Curso X',
            'quiz_score' => 97,
            'issuer' => 'LearnProof',
        ],
    ]);

    $this->get(route('certificates.verify', $certificate))
        ->assertOk()
        ->assertSee('Maria Teste')
        ->assertDontSee('Nota na avaliação')
        ->assertDontSee('97%');
});
