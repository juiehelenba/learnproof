<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Services\CertificateIssuerService;
use App\Services\QuizGraderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    private const NOT_READY = 'Você precisa concluir todas as aulas antes de fazer a avaliação final.';

    public function __construct(
        private QuizGraderService $grader,
        private CertificateIssuerService $certificates,
    ) {}

    public function show(Request $request, Course $course): View|RedirectResponse
    {
        $course->load('quiz.questions.options');

        if (! $quiz = $course->quiz) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Este curso ainda não possui avaliação final configurada.');
        }

        if (! $this->enrollmentReadyForQuiz($request, $course)) {
            return redirect()->route('courses.show', $course)->with('error', self::NOT_READY);
        }

        $remaining = $this->grader->remainingAttempts($request->user(), $quiz);

        if ($remaining <= 0) {
            return redirect()->route('courses.show', $course)
                ->with('error', $this->attemptLimitMessage());
        }

        return view('quizzes.show', compact('course', 'quiz', 'remaining'));
    }

    public function submit(Request $request, Course $course): RedirectResponse
    {
        $course->load('quiz.questions');

        if (! $quiz = $course->quiz) {
            abort(404);
        }

        if (! $this->enrollmentReadyForQuiz($request, $course)) {
            return back()->with('error', self::NOT_READY);
        }

        if (! $this->grader->canAttempt($request->user(), $quiz)) {
            return redirect()->route('courses.show', $course)
                ->with('error', $this->attemptLimitMessage());
        }

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer', 'exists:question_options,id'],
        ]);

        $attempt = $this->grader->startAttempt($request->user(), $quiz);
        $attempt = $this->grader->grade($attempt, array_map('intval', $validated['answers']));

        if (! $attempt->passed) {
            return $this->failureRedirect($course, $quiz, $attempt->score, $request);
        }

        $certificate = $this->certificates->issue($request->user(), $course, $attempt->score);

        return redirect()
            ->route('certificates.show', $certificate)
            ->with('status', 'Parabéns! Você foi aprovado. Seu certificado foi emitido e o registro na blockchain será confirmado em instantes.');
    }

    private function enrollmentReadyForQuiz(Request $request, Course $course): bool
    {
        $enrollment = $request->user()->enrollmentFor($course);

        return $enrollment instanceof Enrollment && $enrollment->allLessonsCompleted();
    }

    private function failureRedirect(Course $course, Quiz $quiz, int $score, Request $request): RedirectResponse
    {
        $remaining = $this->grader->remainingAttempts($request->user(), $quiz);

        $message = "Sua nota foi {$score}%. É necessário atingir no mínimo {$quiz->passingScore()}% para aprovação.";

        $message .= $remaining > 0
            ? " Revise as aulas e tente novamente — você ainda tem {$remaining} tentativa(s) nesta hora."
            : ' '.$this->attemptLimitMessage();

        return redirect()
            ->route($remaining > 0 ? 'quizzes.show' : 'courses.show', $course)
            ->with('error', $message);
    }

    private function attemptLimitMessage(): string
    {
        $max = (int) config('learnproof.quiz.max_attempts_per_hour');

        return "Você atingiu o limite de {$max} tentativas por hora nesta avaliação. Aproveite para revisar as aulas e volte em breve.";
    }
}
