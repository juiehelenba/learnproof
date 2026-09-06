<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('progressPercent usa withCount e memoiza (sem queries extras por chamada)', function () {
    $user = User::factory()->create();
    $course = Course::factory()->withCurriculum(2, 1)->create();
    $enrollment = Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    LessonProgress::query()->create([
        'enrollment_id' => $enrollment->id,
        'lesson_id' => $course->lessons->first()->id,
        'completed_at' => now(),
    ]);

    $enrollment = Enrollment::query()
        ->with(['course' => fn ($q) => $q->withCount('lessons')])
        ->withCount('lessonProgress')
        ->findOrFail($enrollment->id);

    DB::enableQueryLog();
    DB::flushQueryLog();

    expect($enrollment->progressPercent())->toBe(50)
        ->and($enrollment->progressPercent())->toBe(50)
        ->and($enrollment->progressPercent())->toBe(50);

    expect(DB::getQueryLog())->toHaveCount(0);
});

test('CertificatePolicy bloqueia não-dono via authorize view', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $certificate = \App\Models\Certificate::factory()->for($owner)->create();

    expect($stranger->can('view', $certificate))->toBeFalse()
        ->and($owner->can('view', $certificate))->toBeTrue();
});
