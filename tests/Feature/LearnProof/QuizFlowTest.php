<?php

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\User;

test('quiz redireciona se aulas não foram concluídas', function () {
    $user = User::factory()->create();
    $course = Course::factory()->withCurriculum(2, 1)->create();

    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    $this->actingAs($user)
        ->get(route('quizzes.show', $course))
        ->assertRedirect(route('courses.show', $course))
        ->assertSessionHas('error');
});

test('aprovação no quiz emite certificado único', function () {
    $user = User::factory()->create();
    $course = Course::factory()->withCurriculum(2, 2)->create();
    enrollAndCompleteLessons($user, $course);

    $answers = correctAnswersFor($course->quiz);

    $response = $this->actingAs($user)
        ->post(route('quizzes.submit', $course), ['answers' => $answers]);

    $certificate = Certificate::query()
        ->where('user_id', $user->id)
        ->where('course_id', $course->id)
        ->first();

    expect($certificate)->not->toBeNull()
        ->and($certificate->hasIntactContentHash())->toBeTrue();

    $response->assertRedirect(route('certificates.show', $certificate));

    $this->actingAs($user)
        ->post(route('quizzes.submit', $course), ['answers' => $answers]);

    expect(
        Certificate::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->count()
    )->toBe(1);
});

test('reprovação não emite certificado e consome tentativa', function () {
    $user = User::factory()->create();
    $course = Course::factory()->withCurriculum(1, 2)->create();
    enrollAndCompleteLessons($user, $course);

    $this->actingAs($user)
        ->post(route('quizzes.submit', $course), [
            'answers' => incorrectAnswersFor($course->quiz),
        ])
        ->assertRedirect(route('quizzes.show', $course))
        ->assertSessionHas('error');

    $this->assertDatabaseMissing('certificates', [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    expect(
        QuizAttempt::query()
            ->where('user_id', $user->id)
            ->where('quiz_id', $course->quiz->id)
            ->count()
    )->toBe(1);
});

test('limite de tentativas por hora bloqueia novo submit', function () {
    config(['learnproof.quiz.max_attempts_per_hour' => 2]);

    $user = User::factory()->create();
    $course = Course::factory()->withCurriculum(1, 1)->create();
    enrollAndCompleteLessons($user, $course);

    QuizAttempt::factory()->count(2)->create([
        'user_id' => $user->id,
        'quiz_id' => $course->quiz->id,
    ]);

    $this->actingAs($user)
        ->post(route('quizzes.submit', $course), [
            'answers' => correctAnswersFor($course->quiz),
        ])
        ->assertRedirect(route('courses.show', $course))
        ->assertSessionHas('error');

    $this->assertDatabaseMissing('certificates', [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);
});
