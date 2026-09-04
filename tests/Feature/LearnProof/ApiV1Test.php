<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('GET /api/v1 retorna descoberta com links de docs', function () {
    $this->getJson(route('api.v1.index'))
        ->assertOk()
        ->assertJsonPath('data.version', 'v1')
        ->assertJsonPath('meta.api_version', 'v1')
        ->assertJsonStructure(['data' => ['documentation', 'openapi', 'endpoints']]);
});

test('openapi.yaml é servido', function () {
    $this->get(route('api.v1.openapi'))
        ->assertOk()
        ->assertHeader('content-type', 'application/yaml; charset=UTF-8')
        ->assertSee('openapi: 3.0.3', false)
        ->assertSee('LearnProof API', false);
});

test('login retorna envelope data + meta com user resource', function () {
    $user = User::factory()->create([
        'email' => 'api@learnproof.test',
        'password' => 'password',
    ]);

    $this->postJson(route('api.v1.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'pest',
    ])
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'token',
                'token_type',
                'user' => ['id', 'name', 'email', 'role'],
            ],
            'meta' => ['api_version'],
        ])
        ->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonPath('data.user.email', $user->email)
        ->assertJsonMissingPath('token'); // token só dentro de data
});

test('login inválido retorna 422', function () {
    $user = User::factory()->create();

    $this->postJson(route('api.v1.login'), [
        'email' => $user->email,
        'password' => 'wrong',
    ])->assertStatus(422);
});

test('GET /courses é paginado e só lista publicados para anônimo', function () {
    Course::factory()->count(2)->create(['is_published' => true]);
    Course::factory()->draft()->create(['title' => 'Rascunho API']);

    $this->getJson(route('api.v1.courses.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'title', 'slug', 'passing_score', 'is_published', 'lessons_count']],
            'links',
            'meta' => ['current_page', 'total', 'api_version'],
        ])
        ->assertJsonMissing(['title' => 'Rascunho API']);
});

test('staff com token vê rascunhos em /courses', function () {
    $instructor = User::factory()->instructor()->create();
    Course::factory()->draft()->create(['title' => 'Rascunho Staff']);

    Sanctum::actingAs($instructor);

    $this->getJson(route('api.v1.courses.index'))
        ->assertOk()
        ->assertJsonFragment(['title' => 'Rascunho Staff']);
});

test('GET /courses/{slug} inclui lessons e enrolled', function () {
    $user = User::factory()->create();
    $course = Course::factory()->withCurriculum(2, 1)->create();

    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    Sanctum::actingAs($user);

    $this->getJson(route('api.v1.courses.show', $course))
        ->assertOk()
        ->assertJsonPath('data.slug', $course->slug)
        ->assertJsonPath('data.enrolled', true)
        ->assertJsonPath('meta.api_version', 'v1')
        ->assertJsonCount(2, 'data.lessons');
});

test('me e logout usam envelope consistente', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->getJson(route('api.v1.me'))
        ->assertOk()
        ->assertJsonPath('data.email', $user->email)
        ->assertJsonPath('meta.api_version', 'v1');

    $this->postJson(route('api.v1.logout'))
        ->assertOk()
        ->assertJsonPath('message', 'Logout realizado.')
        ->assertJsonPath('meta.api_version', 'v1');
});

test('aluno não acessa staff/ping; instrutor acessa', function () {
    $student = User::factory()->student()->create();
    $instructor = User::factory()->instructor()->create();

    Sanctum::actingAs($student);
    $this->getJson(route('api.v1.staff.ping'))->assertForbidden();

    Sanctum::actingAs($instructor);
    $this->getJson(route('api.v1.staff.ping'))
        ->assertOk()
        ->assertJsonPath('data.role', 'instructor')
        ->assertJsonPath('meta.api_version', 'v1');
});

test('AI history exige matrícula', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    Sanctum::actingAs($user);

    $this->getJson(route('api.v1.courses.ai.history', $course))
        ->assertForbidden();

    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    $this->getJson(route('api.v1.courses.ai.history', $course))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta' => ['api_version']]);
});
