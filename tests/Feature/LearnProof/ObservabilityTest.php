<?php

use App\Models\AiInteraction;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Services\HealthCheckService;
use App\Services\MetricsService;

test('health endpoint responde com checks sem expor segredos', function () {
    $response = $this->getJson(route('health'));

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'checks' => [
                'database' => ['status'],
                'cache' => ['status'],
                'queue' => ['status'],
                'ai' => ['status', 'enabled', 'api_key_configured'],
                'blockchain' => ['status', 'mode', 'configured'],
            ],
        ]);

    $json = $response->json();

    expect(json_encode($json))
        ->not->toContain('sk-')
        ->not->toContain('private_key')
        ->and($json['checks']['blockchain'])->not->toHaveKey('wallet_private_key')
        ->and($json['checks']['ai'])->not->toHaveKey('api_key');
});

test('aluno não acessa métricas; staff acessa', function () {
    $student = User::factory()->student()->create();
    $instructor = User::factory()->instructor()->create();

    $this->actingAs($student)
        ->get(route('instructor.metrics'))
        ->assertForbidden();

    $this->actingAs($instructor)
        ->get(route('instructor.metrics'))
        ->assertOk()
        ->assertSee('Métricas operacionais')
        ->assertSee('Tutor de IA');
});

test('MetricsService agrega fallback e custo estimado', function () {
    $course = Course::factory()->create();
    $user = User::factory()->create();

    AiInteraction::factory()->count(3)->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'used_fallback' => false,
        'latency_ms' => 1000,
        'meta' => ['prompt_tokens' => 1000, 'completion_tokens' => 500],
    ]);

    AiInteraction::factory()->fallback()->count(1)->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'latency_ms' => 100,
    ]);

    Certificate::factory()->simulated()->create();
    Certificate::factory()->pending()->create();

    $metrics = app(MetricsService::class)->dashboard(7);

    expect($metrics['ai']['interactions'])->toBe(4)
        ->and($metrics['ai']['fallbacks'])->toBe(1)
        ->and($metrics['ai']['fallback_rate'])->toBe(25.0)
        ->and($metrics['ai']['prompt_tokens'])->toBe(3000)
        ->and($metrics['ai']['estimated_cost_usd'])->toBeGreaterThan(0)
        ->and($metrics['certificates']['simulated'])->toBeGreaterThanOrEqual(1)
        ->and($metrics['certificates']['pending'])->toBeGreaterThanOrEqual(1)
        ->and($metrics['ai_by_course'])->not->toBeEmpty();
});

test('HealthCheckService marca database como ok em teste', function () {
    $result = app(HealthCheckService::class)->check();

    expect($result['checks']['database']['status'])->toBe('ok')
        ->and($result['status'])->toBeIn(['ok', 'degraded']);
});
