<?php

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Services\CertificateIssuerService;
use Illuminate\Support\Str;

test('contentHashFor é determinístico e bate com hasIntactContentHash', function () {
    $uuid = (string) Str::uuid();
    $issuedAt = now();

    $hash = Certificate::contentHashFor($uuid, 10, 20, $issuedAt);

    expect($hash)->toHaveLength(64)
        ->and($hash)->toBe(Certificate::contentHashFor($uuid, 10, 20, $issuedAt));
});

test('hasIntactContentHash detecta adulteração', function () {
    $certificate = Certificate::factory()->create();

    expect($certificate->hasIntactContentHash())->toBeTrue();

    $certificate->forceFill(['content_hash' => str_repeat('a', 64)])->save();

    expect($certificate->fresh()->hasIntactContentHash())->toBeFalse();
});

test('isSimulatedAnchor reconhece redes mock', function () {
    $simulated = Certificate::factory()->simulated()->create();
    $pending = Certificate::factory()->pending()->create();

    expect($simulated->isSimulatedAnchor())->toBeTrue()
        ->and($pending->isSimulatedAnchor())->toBeFalse();
});

test('issue é idempotente para o mesmo par usuário/curso', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $service = app(CertificateIssuerService::class);

    $first = $service->issue($user, $course, 90);
    $second = $service->issue($user, $course, 55);

    expect($first->id)->toBe($second->id)
        ->and(Certificate::query()->where('user_id', $user->id)->where('course_id', $course->id)->count())->toBe(1)
        ->and($first->fresh()->hasIntactContentHash())->toBeTrue()
        ->and($first->metadata['quiz_score'])->toBe(90);
});

test('passingScore do curso segue o do quiz quando existe', function () {
    $course = Course::factory()->withCurriculum(1, 1)->create([
        'passing_score' => 70,
    ]);

    $course->quiz->update(['passing_score' => 60]);
    $course->refresh()->load('quiz');

    expect($course->passingScore())->toBe(60)
        ->and($course->quiz->passingScore())->toBe(60);
});
