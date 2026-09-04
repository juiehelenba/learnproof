<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Course::class);

        $user = $request->user('sanctum');

        $courses = Course::query()
            ->with('quiz:id,course_id,passing_score')
            ->withCount('lessons')
            ->when(
                ! $user?->role?->isStaff(),
                fn ($q) => $q->where('is_published', true)
            )
            ->orderBy('title')
            ->paginate(
                perPage: min(max((int) $request->integer('per_page', 15), 1), 50),
            )
            ->withQueryString();

        return CourseResource::collection($courses)->additional([
            'meta' => [
                'api_version' => 'v1',
            ],
        ]);
    }

    public function show(Request $request, Course $course): CourseResource
    {
        $this->authorize('view', $course);

        $course->load([
            'lessons:id,course_id,title,slug,duration_minutes,sort_order',
            'quiz:id,course_id,passing_score',
        ]);

        $user = $request->user('sanctum');
        $course->setAttribute(
            'enrolled',
            $user ? $user->enrollmentFor($course) !== null : false,
        );

        return (new CourseResource($course))->additional([
            'meta' => [
                'api_version' => 'v1',
            ],
        ]);
    }
}
