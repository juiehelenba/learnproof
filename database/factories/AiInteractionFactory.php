<?php

namespace Database\Factories;

use App\Models\AiInteraction;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiInteraction>
 */
class AiInteractionFactory extends Factory
{
    protected $model = AiInteraction::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'question' => fake()->sentence().'?',
            'answer' => fake()->paragraph(),
            'status' => 'completed',
            'provider' => 'openai',
            'model' => 'gpt-4o-mini',
            'used_fallback' => false,
            'latency_ms' => fake()->numberBetween(200, 2500),
            'context_chars' => fake()->numberBetween(500, 4000),
            'context_snapshot' => null,
            'meta' => [
                'prompt_tokens' => fake()->numberBetween(100, 800),
                'completion_tokens' => fake()->numberBetween(50, 400),
                'request_id' => fake()->uuid(),
            ],
        ];
    }

    public function fallback(): static
    {
        return $this->state(fn () => [
            'status' => 'fallback',
            'used_fallback' => true,
            'meta' => [
                'prompt_tokens' => null,
                'completion_tokens' => null,
                'fallback_reason' => 'missing_api_key',
            ],
        ]);
    }
}
