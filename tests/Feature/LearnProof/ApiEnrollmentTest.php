<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('POST enroll matricula aluno em curso publicado', function () {
    $user = User::factory()->student()->create();
    $course = Course::factory()->withCurriculum(2, 1)->create(['is_published' => true]);

    Sanctum::actingAs($user);

    $this->postJson(route('api.v1.courses.enroll', $course))
        ->assertOk()
        ->assertJsonPath('data.course.slug', $course->slug)
        ->assertJsonPath('data.progress_percent', 0)
        ->assertJsonPath('data.lessons_total', 2)
        ->assertJsonPath('data.has_certificate', false)
        ->assertJsonPath('meta.created', true)
        ->assertJsonPath('meta.api_version', 'v1');

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);
});

test('POST enroll é idempotente', function () {
    $user = User::factory()->student()->create();
    $course = Course::factory()->create(['is_published' => true]);

    Sanctum::actingAs($user);

    $this->postJson(route('api.v1.courses.enroll', $course))
        ->assertOk()
        ->assertJsonPath('meta.created', true);

    $this->postJson(route('api.v1.courses.enroll', $course))
        ->assertOk()
        ->assertJsonPath('meta.created', false);

    expect(
        Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->count()
    )->toBe(1);
});

test('POST enroll bloqueia curso em rascunho', function () {
    $user = User::factory()->student()->create();
    $course = Course::factory()->draft()->create();

    Sanctum::actingAs($user);

    $this->postJson(route('api.v1.courses.enroll', $course))
        ->assertForbidden();
});

test('GET progress exige matrícula e devolve aulas concluídas', function () {
    $user = User::factory()->student()->create();
    $course = Course::factory()->withCurriculum(2, 1)->create();
    $enrollment = Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    $firstLesson = $course->lessons()->orderBy('sort_order')->first();

    LessonProgress::query()->create([
        'enrollment_id' => $enrollment->id,
        'lesson_id' => $firstLesson->id,
        'completed_at' => now(),
    ]);

    Sanctum::actingAs($user);

    $this->getJson(route('api.v1.courses.progress', $course))
        ->assertOk()
        ->assertJsonPath('data.lessons_completed', 1)
        ->assertJsonPath('data.lessons_total', 2)
        ->assertJsonPath('data.progress_percent', 50)
        ->assertJsonPath('data.completed_lesson_slugs.0', $firstLesson->slug)
        ->assertJsonPath('meta.api_version', 'v1');
});

test('GET progress sem matrícula retorna 404', function () {
    $user = User::factory()->student()->create();
    $course = Course::factory()->create();

    Sanctum::actingAs($user);

    $this->getJson(route('api.v1.courses.progress', $course))
        ->assertNotFound();
});
