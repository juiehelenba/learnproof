<?php

use App\Models\Course;
use App\Models\User;

test('visitante não acessa curso em rascunho', function () {
    $course = Course::factory()->draft()->create();

    $this->get(route('courses.show', $course))
        ->assertForbidden();
});

test('aluno não acessa curso em rascunho', function () {
    $user = User::factory()->student()->create();
    $course = Course::factory()->draft()->create();

    $this->actingAs($user)
        ->get(route('courses.show', $course))
        ->assertForbidden();
});

test('staff acessa curso em rascunho', function () {
    $instructor = User::factory()->instructor()->create();
    $course = Course::factory()->draft()->withCurriculum(1, 1)->create();

    $this->actingAs($instructor)
        ->get(route('courses.show', $course))
        ->assertOk();
});

test('aluno não se matricula em curso não publicado', function () {
    $user = User::factory()->student()->create();
    $course = Course::factory()->draft()->create();

    $this->actingAs($user)
        ->post(route('courses.enroll', $course))
        ->assertForbidden();

    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);
});

test('aluno se matricula em curso publicado', function () {
    $user = User::factory()->student()->create();
    $course = Course::factory()->create(['is_published' => true]);

    $this->actingAs($user)
        ->post(route('courses.enroll', $course))
        ->assertRedirect(route('courses.show', $course));

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);
});
