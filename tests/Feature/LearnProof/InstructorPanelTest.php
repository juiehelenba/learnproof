<?php

use App\Models\Course;
use App\Models\Question;
use App\Models\User;

test('aluno não acessa o painel do instrutor', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)
        ->get(route('instructor.courses.index'))
        ->assertForbidden();
});

test('instrutor acessa o painel e lista cursos incluindo rascunhos', function () {
    $instructor = User::factory()->instructor()->create();
    $draft = Course::factory()->draft()->create(['title' => 'Rascunho Secreto']);
    $published = Course::factory()->create(['title' => 'Curso Público']);

    $this->actingAs($instructor)
        ->get(route('instructor.courses.index'))
        ->assertOk()
        ->assertSee('Rascunho Secreto')
        ->assertSee('Curso Público')
        ->assertSee('Rascunho')
        ->assertSee('Publicado');
});

test('instrutor cria curso com quiz automático', function () {
    $instructor = User::factory()->instructor()->create();

    $response = $this->actingAs($instructor)
        ->post(route('instructor.courses.store'), [
            'title' => 'Prompt Engineering na Prática',
            'description' => 'Curso curto sobre prompts.',
            'passing_score' => 80,
        ]);

    $course = Course::query()->where('title', 'Prompt Engineering na Prática')->first();

    expect($course)->not->toBeNull()
        ->and($course->is_published)->toBeFalse()
        ->and($course->slug)->toContain('prompt-engineering')
        ->and($course->quiz)->not->toBeNull()
        ->and($course->quiz->passing_score)->toBe(80);

    $response->assertRedirect(route('instructor.courses.edit', $course));
});

test('instrutor adiciona aula e questão ao curso', function () {
    $instructor = User::factory()->instructor()->create();
    $course = Course::factory()->create(['is_published' => false]);

    // Garante quiz (createCourse cria; factory simples não)
    $course->quiz()->create([
        'title' => 'Avaliação',
        'passing_score' => 70,
    ]);

    $this->actingAs($instructor)
        ->post(route('instructor.lessons.store', $course), [
            'title' => 'Primeira aula',
            'content' => "## Introdução\n\nConteúdo da aula.",
            'duration_minutes' => 15,
        ])
        ->assertRedirect(route('instructor.courses.edit', $course));

    $this->assertDatabaseHas('lessons', [
        'course_id' => $course->id,
        'title' => 'Primeira aula',
    ]);

    $this->actingAs($instructor)
        ->post(route('instructor.questions.store', $course), [
            'text' => 'O que é um prompt?',
            'explanation' => 'Instrução ao modelo.',
            'correct_option' => 1,
            'options' => [
                ['text' => 'Um banco de dados'],
                ['text' => 'Uma instrução ao modelo'],
                ['text' => 'Um certificado'],
                ['text' => 'Uma rede neural'],
            ],
        ])
        ->assertRedirect(route('instructor.courses.edit', $course));

    $question = Question::query()->where('text', 'O que é um prompt?')->first();

    expect($question)->not->toBeNull()
        ->and($question->options)->toHaveCount(4)
        ->and($question->options->firstWhere('is_correct', true)?->text)->toBe('Uma instrução ao modelo');
});

test('instrutor publica e despublica curso', function () {
    $instructor = User::factory()->instructor()->create();
    $course = Course::factory()->draft()->create();

    $this->actingAs($instructor)
        ->patch(route('instructor.courses.publish', $course))
        ->assertRedirect();

    expect($course->fresh()->is_published)->toBeTrue();

    $this->actingAs($instructor)
        ->patch(route('instructor.courses.publish', $course))
        ->assertRedirect();

    expect($course->fresh()->is_published)->toBeFalse();
});

test('instrutor não pode excluir curso — só admin', function () {
    $instructor = User::factory()->instructor()->create();
    $admin = User::factory()->admin()->create();
    $course = Course::factory()->create();

    $this->actingAs($instructor)
        ->delete(route('instructor.courses.destroy', $course))
        ->assertForbidden();

    $this->assertDatabaseHas('courses', ['id' => $course->id]);

    $this->actingAs($admin)
        ->delete(route('instructor.courses.destroy', $course))
        ->assertRedirect(route('instructor.courses.index'));

    $this->assertDatabaseMissing('courses', ['id' => $course->id]);
});

test('nav do painel aparece só para staff', function () {
    $student = User::factory()->student()->create();
    $instructor = User::factory()->instructor()->create();

    $this->actingAs($student)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Painel do instrutor');

    $this->actingAs($instructor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Painel do instrutor');
});
