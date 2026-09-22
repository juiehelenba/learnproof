<?php

use App\Models\AiInteraction;
use App\Models\Course;
use App\Models\User;
use App\Services\MetricsService;
use Illuminate\Support\Facades\File;

test('MetricsService gera CSV com cabeçalho e seções', function () {
    $course = Course::factory()->create();
    AiInteraction::factory()->create(['course_id' => $course->id]);

    $csv = app(MetricsService::class)->toCsv(7);

    expect($csv)->toContain('section,metric,value')
        ->and($csv)->toContain('ai,interactions')
        ->and($csv)->toContain('certificates,total')
        ->and($csv)->toContain('learning,quiz_pass_rate_period');
});

test('staff baixa CSV de métricas; aluno não', function () {
    $student = User::factory()->student()->create();
    $instructor = User::factory()->instructor()->create();

    $this->actingAs($student)
        ->get(route('instructor.metrics.export', ['days' => 7]))
        ->assertForbidden();

    $response = $this->actingAs($instructor)
        ->get(route('instructor.metrics.export', ['days' => 7]));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('.csv');
    expect($response->streamedContent())->toContain('section,metric,value');
});

test('learnproof:metrics --csv grava arquivo', function () {
    $relative = 'storage/app/testing-learnproof-metrics.csv';
    $absolute = base_path($relative);

    if (File::exists($absolute)) {
        File::delete($absolute);
    }

    $this->artisan('learnproof:metrics', [
        '--days' => 7,
        '--csv' => $relative,
    ])->assertSuccessful();

    expect(File::exists($absolute))->toBeTrue()
        ->and(File::get($absolute))->toContain('section,metric,value');

    File::delete($absolute);
});
