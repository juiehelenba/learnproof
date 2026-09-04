<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Unit/LearnProof');

/*
|--------------------------------------------------------------------------
| Helpers LearnProof
|--------------------------------------------------------------------------
*/

/**
 * Matricula o aluno e marca todas as aulas do curso como concluídas.
 */
function enrollAndCompleteLessons(User $user, Course $course): Enrollment
{
    $course->loadMissing('lessons');

    $enrollment = Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
    ]);

    foreach ($course->lessons as $lesson) {
        LessonProgress::query()->create([
            'enrollment_id' => $enrollment->id,
            'lesson_id' => $lesson->id,
            'completed_at' => now(),
        ]);
    }

    $enrollment->update(['completed_at' => now()]);

    return $enrollment->fresh();
}

/**
 * @return array<int, int> question_id => option_id correta
 */
function correctAnswersFor(Quiz $quiz): array
{
    $quiz->loadMissing('questions.options');

    $answers = [];

    foreach ($quiz->questions as $question) {
        $correct = $question->options->firstWhere('is_correct', true);
        $answers[$question->id] = $correct->id;
    }

    return $answers;
}

/**
 * @return array<int, int> question_id => option_id incorreta
 */
function incorrectAnswersFor(Quiz $quiz): array
{
    $quiz->loadMissing('questions.options');

    $answers = [];

    foreach ($quiz->questions as $question) {
        $wrong = $question->options->firstWhere('is_correct', false);
        $answers[$question->id] = $wrong->id;
    }

    return $answers;
}
