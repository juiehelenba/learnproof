<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreCourseRequest;
use App\Http\Requests\Instructor\UpdateCourseRequest;
use App\Models\Course;
use App\Services\InstructorCourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(
        private InstructorCourseService $courses,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Course::class);

        $courses = Course::query()
            ->withCount(['lessons', 'enrollments'])
            ->with('quiz')
            ->latest()
            ->get();

        return view('instructor.courses.index', compact('courses'));
    }

    public function create(): View
    {
        $this->authorize('create', Course::class);

        return view('instructor.courses.create');
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $course = $this->courses->createCourse($request->validated());

        return redirect()
            ->route('instructor.courses.edit', $course)
            ->with('status', 'Curso criado como rascunho. Adicione aulas e configure a avaliação.');
    }

    public function edit(Course $course): View
    {
        $this->authorize('update', $course);

        $course->load(['lessons', 'quiz.questions.options']);

        return view('instructor.courses.edit', compact('course'));
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $this->courses->updateCourse($course, $request->validated());

        return redirect()
            ->route('instructor.courses.edit', $course)
            ->with('status', 'Dados do curso atualizados.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        $course->delete();

        return redirect()
            ->route('instructor.courses.index')
            ->with('status', 'Curso removido.');
    }

    public function togglePublish(Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $course->update(['is_published' => ! $course->is_published]);

        $message = $course->is_published
            ? 'Curso publicado no catálogo.'
            : 'Curso voltou para rascunho.';

        return back()->with('status', $message);
    }
}
