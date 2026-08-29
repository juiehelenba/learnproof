<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Course::class);

        $courses = Course::query()
            ->with('quiz:id,course_id,passing_score')
            ->withCount('lessons')
            ->when(
                ! $request->user()?->role?->isStaff(),
                fn ($q) => $q->where('is_published', true)
            )
            ->orderBy('title')
            ->get()
            ->map(fn (Course $course) => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'description' => $course->description,
                'passing_score' => $course->passingScore(),
                'is_published' => $course->is_published,
                'lessons_count' => $course->lessons_count,
            ]);

        return response()->json(['data' => $courses]);
    }

    public function show(Request $request, Course $course): JsonResponse
    {
        $this->authorize('view', $course);

        $course->load([
            'lessons:id,course_id,title,slug,duration_minutes,sort_order',
            'quiz:id,course_id,passing_score',
        ]);

        return response()->json([
            'data' => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'description' => $course->description,
                'passing_score' => $course->passingScore(),
                'is_published' => $course->is_published,
                'lessons' => $course->lessons,
                'enrolled' => $request->user()
                    ? $request->user()->enrollmentFor($course) !== null
                    : false,
            ],
        ]);
    }
}
