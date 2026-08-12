<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::query()
            ->where('is_published', true)
            ->withCount('lessons')
            ->latest()
            ->get();

        return view('courses.index', compact('courses'));
    }

    public function show(Course $course): View
    {
        $course->load(['lessons', 'quiz.questions.options']);

        $enrollment = auth()->check()
            ? auth()->user()->enrollmentFor($course)
            : null;

        $certificate = auth()->check()
            ? auth()->user()->certificates()->where('course_id', $course->id)->first()
            : null;

        return view('courses.show', compact('course', 'enrollment', 'certificate'));
    }

    public function enroll(Request $request, Course $course): RedirectResponse
    {
        $user = $request->user();

        Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['enrolled_at' => now()]
        );

        return redirect()
            ->route('courses.show', $course)
            ->with('status', 'Matrícula confirmada! Comece pela primeira aula.');
    }
}
