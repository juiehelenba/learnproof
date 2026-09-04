<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuizAttempt>
 */
class QuizAttemptFactory extends Factory
{
    protected $model = QuizAttempt::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'quiz_id' => Quiz::factory(),
            'score' => 0,
            'passed' => false,
            'started_at' => now(),
            'finished_at' => now(),
        ];
    }

    public function passed(int $score = 100): static
    {
        return $this->state(fn () => [
            'score' => $score,
            'passed' => true,
            'finished_at' => now(),
        ]);
    }

    public function failed(int $score = 40): static
    {
        return $this->state(fn () => [
            'score' => $score,
            'passed' => false,
            'finished_at' => now(),
        ]);
    }
}
