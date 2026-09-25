<?php

use App\Exceptions\AiUsageLimitExceededException;
use App\Models\AiInteraction;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\Ai\AiTutorService;
use App\Services\Ai\AiUsageLimiter;
use Laravel\Sanctum\Sanctum;

test('AiUsageLimiter bloqueia após atingir interações do dia', function () {
    config(['learnproof.ai.max_interactions_per_day' => 2]);
    config(['learnproof.ai.max_estimated_cost_usd_per_day' => 0]);

    $user = User::factory()->create();
    $course = Course::factory()->create();

    AiInteraction::factory()->count(2)->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    expect(fn () => app(AiUsageLimiter::class)->assertCanUse($user))
        ->toThrow(AiUsageLimitExceededException::class);
});

test('chat do tutor retorna 429 quando limite diário é atingido', function () {
    config(['learnproof.ai.max_interactions_per_day' => 1]);
    config(['learnproof.ai.max_estimated_cost_usd_per_day' => 0]);
    config(['learnproof.ai.enabled' => false]);

    $user = User::factory()->student()->create();
    $course = Course::factory()->create(['is_published' => true]);

    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    AiInteraction::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    Sanctum::actingAs($user);

    $this->postJson(route('api.v1.courses.ai.chat', $course), [
        'message' => 'O que é um prompt?',
    ])
        ->assertStatus(429)
        ->assertJsonPath('error', 'ai_usage_limit_exceeded')
        ->assertJsonPath('limit_type', 'interactions');
});

test('AiTutorService chama o limiter antes de gerar resposta', function () {
    config(['learnproof.ai.max_interactions_per_day' => 0]);
    config(['learnproof.ai.max_estimated_cost_usd_per_day' => 0]);
    config(['learnproof.ai.enabled' => false]);

    $user = User::factory()->create();
    $course = Course::factory()->create();

    $result = app(AiTutorService::class)->chat($user, $course, 'Explique tokens.');

    expect($result)->toHaveKeys(['reply', 'interaction_id', 'used_fallback']);
});
