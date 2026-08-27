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
            return redirect()->route('courses.show', $course)->with('error', 'Este curso ainda não possui avaliação final configurada.');
        }

        $enrollment = auth()->user()->enrollmentFor($course);

        if (! $enrollment?->allLessonsCompleted()) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Você precisa concluir todas as aulas antes de fazer a avaliação final.');
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
            return back()->with('error', 'Você precisa concluir todas as aulas antes de fazer a avaliação final.');
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
                ->with('status', 'Parabéns! Você foi aprovado. Seu certificado foi emitido e o registro na blockchain será confirmado em instantes.');
        }

        return redirect()
            ->route('quizzes.show', $course)
            ->with('error', "Sua nota foi {$attempt->score}%. É necessário atingir no mínimo {$quiz->passing_score}% para aprovação. Revise as aulas e tente novamente.");
    }
}
