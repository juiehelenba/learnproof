<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreQuestionRequest;
use App\Http\Requests\Instructor\UpdateQuizRequest;
use App\Models\Course;
use App\Models\Question;
use App\Services\InstructorCourseService;
use Illuminate\Http\RedirectResponse;

class QuizController extends Controller
{
    public function __construct(
        private InstructorCourseService $courses,
    ) {}

    public function update(UpdateQuizRequest $request, Course $course): RedirectResponse
    {
        $this->courses->updateQuiz($course, $request->validated());

        return redirect()
            ->route('instructor.courses.edit', $course)
            ->with('status', 'Avaliação final atualizada.');
    }

    public function storeQuestion(StoreQuestionRequest $request, Course $course): RedirectResponse
    {
        $quiz = $course->quiz;

        if (! $quiz) {
            return back()->with('error', 'Crie a avaliação do curso antes de adicionar questões.');
        }

        $this->courses->createQuestion($quiz, $request->validated());

        return redirect()
            ->route('instructor.courses.edit', $course)
            ->with('status', 'Questão adicionada à avaliação.');
    }

    public function destroyQuestion(Course $course, Question $question): RedirectResponse
    {
        $this->authorize('update', $course);

        if ($question->quiz?->course_id !== $course->id) {
            abort(404);
        }

        $this->courses->deleteQuestion($question);

        return redirect()
            ->route('instructor.courses.edit', $course)
            ->with('status', 'Questão removida.');
    }
}
