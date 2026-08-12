<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuizGraderService
{
    public function startAttempt(User $user, Quiz $quiz): QuizAttempt
    {
        return QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'started_at' => now(),
        ]);
    }

    /**
     * @param  array<int, int>  $answers  question_id => option_id
     */
    public function grade(QuizAttempt $attempt, array $answers): QuizAttempt
    {
        $quiz = $attempt->quiz()->with('questions.options')->first();
        $total = $quiz->questions->count();
        $correct = 0;

        DB::transaction(function () use ($attempt, $quiz, $answers, $total, &$correct) {
            foreach ($quiz->questions as $question) {
                $optionId = $answers[$question->id] ?? null;
                $selected = $question->options->firstWhere('id', $optionId);
                $isCorrect = $selected?->is_correct ?? false;

                if ($isCorrect) {
                    $correct++;
                }

                $attempt->answers()->create([
                    'question_id' => $question->id,
                    'question_option_id' => $optionId,
                    'is_correct' => $isCorrect,
                ]);
            }

            $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
            $passed = $score >= $quiz->passing_score;

            $attempt->update([
                'score' => $score,
                'passed' => $passed,
                'finished_at' => now(),
            ]);
        });

        return $attempt->fresh();
    }
}
