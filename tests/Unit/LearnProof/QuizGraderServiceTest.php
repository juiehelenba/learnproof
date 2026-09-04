<?php

use App\Models\Course;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\QuizGraderService;

test('grade aprova quando atinge a nota mínima do quiz', function () {
    $course = Course::factory()->withCurriculum(1, 2)->create(['passing_score' => 70]);
    $user = User::factory()->create();
    $quiz = $course->quiz;
    $grader = app(QuizGraderService::class);

    $attempt = $grader->startAttempt($user, $quiz);
    $graded = $grader->grade($attempt, correctAnswersFor($quiz));

    expect($graded->passed)->toBeTrue()
        ->and($graded->score)->toBe(100)
        ->and($graded->finished_at)->not->toBeNull();
});

test('grade reprova quando as respostas estão erradas', function () {
    $course = Course::factory()->withCurriculum(1, 2)->create(['passing_score' => 70]);
    $user = User::factory()->create();
    $quiz = $course->quiz;
    $grader = app(QuizGraderService::class);

    $attempt = $grader->startAttempt($user, $quiz);
    $graded = $grader->grade($attempt, incorrectAnswersFor($quiz));

    expect($graded->passed)->toBeFalse()
        ->and($graded->score)->toBe(0);
});

test('remainingAttempts respeita o limite por hora', function () {
    config(['learnproof.quiz.max_attempts_per_hour' => 2]);

    $course = Course::factory()->withCurriculum(1, 1)->create();
    $user = User::factory()->create();
    $quiz = $course->quiz;
    $grader = app(QuizGraderService::class);

    QuizAttempt::factory()->count(2)->create([
        'user_id' => $user->id,
        'quiz_id' => $quiz->id,
        'created_at' => now()->subMinutes(10),
    ]);

    expect($grader->remainingAttempts($user, $quiz))->toBe(0)
        ->and($grader->canAttempt($user, $quiz))->toBeFalse();
});
