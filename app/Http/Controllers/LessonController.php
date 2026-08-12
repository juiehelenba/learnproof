<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function show(Course $course, Lesson $lesson): View|RedirectResponse
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $enrollment = $this->requireEnrollment($course);

        $completedLessonIds = $enrollment->lessonProgress()
            ->pluck('lesson_id')
            ->all();

        return view('lessons.show', compact('course', 'lesson', 'enrollment', 'completedLessonIds'));
    }

    public function complete(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $enrollment = $this->requireEnrollment($course);

        LessonProgress::firstOrCreate(
            ['enrollment_id' => $enrollment->id, 'lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        if ($enrollment->fresh()->allLessonsCompleted()) {
            $enrollment->update(['completed_at' => now()]);
        }

        $nextLesson = $course->lessons()
            ->where('sort_order', '>', $lesson->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($nextLesson) {
            return redirect()
                ->route('lessons.show', [$course, $nextLesson])
                ->with('status', 'Aula concluída! Próxima aula liberada.');
        }

        return redirect()
            ->route('courses.show', $course)
            ->with('status', 'Parabéns! Todas as aulas concluídas. Faça o quiz para obter seu certificado.');
    }

    private function requireEnrollment(Course $course): Enrollment
    {
        $enrollment = auth()->user()->enrollmentFor($course);

        if (! $enrollment) {
            abort(403, 'Matricule-se no curso primeiro.');
        }

        return $enrollment;
    }
}
