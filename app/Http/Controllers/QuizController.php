<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\CertificateIssuerService;
use App\Services\QuizGraderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function __construct(
        private QuizGraderService $grader,
        private CertificateIssuerService $certificates,
    ) {}

    public function show(Course $course): View|RedirectResponse
    {
        $course->load('quiz.questions.options');
        $quiz = $course->quiz;

        if (! $quiz) {
            return redirect()->route('courses.show', $course)->with('error', 'Este curso ainda não tem quiz.');
        }

        $enrollment = auth()->user()->enrollmentFor($course);

        if (! $enrollment?->allLessonsCompleted()) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Conclua todas as aulas antes do quiz.');
        }

        return view('quizzes.show', compact('course', 'quiz'));
    }

    public function submit(Request $request, Course $course): RedirectResponse
    {
        $course->load('quiz.questions');
        $quiz = $course->quiz;

        if (! $quiz) {
            abort(404);
        }

        $enrollment = auth()->user()->enrollmentFor($course);

        if (! $enrollment?->allLessonsCompleted()) {
            return back()->with('error', 'Conclua todas as aulas antes do quiz.');
        }

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer', 'exists:question_options,id'],
        ]);

        $attempt = $this->grader->startAttempt($request->user(), $quiz);
        $attempt = $this->grader->grade($attempt, array_map('intval', $validated['answers']));

        if ($attempt->passed) {
            $certificate = $this->certificates->issue($request->user(), $course, $attempt->score);

            return redirect()
                ->route('certificates.show', $certificate)
                ->with('status', 'Aprovado! Seu certificado foi emitido e registrado na blockchain.');
        }

        return redirect()
            ->route('quizzes.show', $course)
            ->with('error', "Nota {$attempt->score}%. Mínimo: {$quiz->passing_score}%. Tente novamente.");
    }
}
