<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuizGraderService
{
    /**
     * Impede que o aluno descubra as respostas corretas por força bruta,
     * já que as alternativas não mudam entre tentativas.
     */
    public function remainingAttempts(User $user, Quiz $quiz): int
    {
        $max = (int) config('learnproof.quiz.max_attempts_per_hour');

        if ($max <= 0) {
            return PHP_INT_MAX;
        }

        $used = QuizAttempt::query()
            ->where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        return max(0, $max - $used);
    }

    public function canAttempt(User $user, Quiz $quiz): bool
    {
        return $this->remainingAttempts($user, $quiz) > 0;
    }

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
            $passed = $score >= $quiz->passingScore();

            $attempt->update([
                'score' => $score,
                'passed' => $passed,
                'finished_at' => now(),
            ]);
        });

        return $attempt->fresh();
    }
}
