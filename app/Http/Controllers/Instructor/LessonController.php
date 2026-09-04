<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreLessonRequest;
use App\Http\Requests\Instructor\UpdateLessonRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\InstructorCourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function __construct(
        private InstructorCourseService $courses,
    ) {}

    public function create(Course $course): View
    {
        $this->authorize('update', $course);

        return view('instructor.lessons.create', compact('course'));
    }

    public function store(StoreLessonRequest $request, Course $course): RedirectResponse
    {
        $this->courses->createLesson($course, $request->validated());

        return redirect()
            ->route('instructor.courses.edit', $course)
            ->with('status', 'Aula adicionada ao curso.');
    }

    public function edit(Course $course, Lesson $lesson): View
    {
        $this->authorize('update', $course);
        $this->ensureLessonBelongsToCourse($course, $lesson);

        return view('instructor.lessons.edit', compact('course', 'lesson'));
    }

    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->ensureLessonBelongsToCourse($course, $lesson);
        $this->courses->updateLesson($lesson, $request->validated());

        return redirect()
            ->route('instructor.courses.edit', $course)
            ->with('status', 'Aula atualizada.');
    }

    public function destroy(Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorize('update', $course);
        $this->ensureLessonBelongsToCourse($course, $lesson);

        $lesson->delete();

        return redirect()
            ->route('instructor.courses.edit', $course)
            ->with('status', 'Aula removida.');
    }

    private function ensureLessonBelongsToCourse(Course $course, Lesson $lesson): void
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }
    }
}
