<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EnrollmentResource;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Request $request, Course $course): EnrollmentResource
    {
        $this->authorize('enroll', $course);

        $enrollment = Enrollment::query()->firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'course_id' => $course->id,
            ],
            [
                'enrolled_at' => now(),
            ]
        );

        return $this->resourceFor($request, $enrollment->fresh(), $course, created: $enrollment->wasRecentlyCreated);
    }

    public function progress(Request $request, Course $course): EnrollmentResource
    {
        $this->authorize('view', $course);

        $enrollment = $request->user()->enrollmentFor($course);

        abort_if($enrollment === null, 404, 'Você não está matriculado neste curso.');

        return $this->resourceFor($request, $enrollment, $course);
    }

    private function resourceFor(
        Request $request,
        Enrollment $enrollment,
        Course $course,
        bool $created = false,
    ): EnrollmentResource {
        $course->loadCount('lessons');
        $enrollment->setRelation('course', $course);
        $enrollment->load(['lessonProgress.lesson:id,slug']);

        $enrollment->setAttribute(
            'has_certificate',
            $request->user()
                ->certificates()
                ->where('course_id', $course->id)
                ->exists()
        );

        return (new EnrollmentResource($enrollment))->additional([
            'meta' => [
                'api_version' => 'v1',
                'created' => $created,
            ],
        ]);
    }
}
