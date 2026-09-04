<?php

use App\Http\Controllers\AiTutorController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Instructor\LessonController as InstructorLessonController;
use App\Http\Controllers\Instructor\MetricsController as InstructorMetricsController;
use App\Http\Controllers\Instructor\QuizController as InstructorQuizController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', HealthController::class)
    ->middleware('throttle:60,1')
    ->name('health');

Route::get('/certificados/{certificate:uuid}/verificar', [CertificateController::class, 'verify'])
    ->middleware('throttle:30,1')
    ->name('certificates.verify');

Route::get('/cursos', [CourseController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('courses.index');
Route::get('/cursos/{course:slug}', [CourseController::class, 'show'])->name('courses.show');

Route::get('/dashboard', function () {
    $enrollments = auth()->user()
        ->enrollments()
        ->with('course')
        ->latest()
        ->get();

    $certificates = auth()->user()
        ->certificates()
        ->with('course')
        ->latest()
        ->get();

    return view('dashboard', compact('enrollments', 'certificates'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/cursos/{course:slug}/matricular', [CourseController::class, 'enroll'])->name('courses.enroll');

    Route::get('/cursos/{course:slug}/aulas/{lesson:slug}', [LessonController::class, 'show'])->name('lessons.show');
    Route::post('/cursos/{course:slug}/aulas/{lesson:slug}/concluir', [LessonController::class, 'complete'])->name('lessons.complete');

    Route::get('/cursos/{course:slug}/quiz', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/cursos/{course:slug}/quiz', [QuizController::class, 'submit'])
        ->middleware('throttle:10,1')
        ->name('quizzes.submit');

    Route::get('/certificados/{certificate:uuid}', [CertificateController::class, 'show'])->name('certificates.show');

    // A UI usa esta rota, não a da API: sem throttle aqui o custo do provedor
    // de IA fica ilimitado por usuário autenticado.
    Route::post('/cursos/{course:slug}/ia/chat', [AiTutorController::class, 'chat'])
        ->middleware('throttle:20,1')
        ->name('ai.chat');
});

Route::middleware(['auth', 'verified', 'role:admin,instructor'])
    ->prefix('instrutor')
    ->name('instructor.')
    ->group(function () {
        Route::get('/metricas', InstructorMetricsController::class)->name('metrics');

        Route::get('/cursos', [InstructorCourseController::class, 'index'])->name('courses.index');
        Route::get('/cursos/criar', [InstructorCourseController::class, 'create'])->name('courses.create');
        Route::post('/cursos', [InstructorCourseController::class, 'store'])->name('courses.store');
        Route::get('/cursos/{course:slug}/editar', [InstructorCourseController::class, 'edit'])->name('courses.edit');
        Route::put('/cursos/{course:slug}', [InstructorCourseController::class, 'update'])->name('courses.update');
        Route::delete('/cursos/{course:slug}', [InstructorCourseController::class, 'destroy'])->name('courses.destroy');
        Route::patch('/cursos/{course:slug}/publicar', [InstructorCourseController::class, 'togglePublish'])->name('courses.publish');

        Route::get('/cursos/{course:slug}/aulas/criar', [InstructorLessonController::class, 'create'])->name('lessons.create');
        Route::post('/cursos/{course:slug}/aulas', [InstructorLessonController::class, 'store'])->name('lessons.store');
        Route::get('/cursos/{course:slug}/aulas/{lesson:slug}/editar', [InstructorLessonController::class, 'edit'])->name('lessons.edit');
        Route::put('/cursos/{course:slug}/aulas/{lesson:slug}', [InstructorLessonController::class, 'update'])->name('lessons.update');
        Route::delete('/cursos/{course:slug}/aulas/{lesson:slug}', [InstructorLessonController::class, 'destroy'])->name('lessons.destroy');

        Route::put('/cursos/{course:slug}/quiz', [InstructorQuizController::class, 'update'])->name('quiz.update');
        Route::post('/cursos/{course:slug}/questoes', [InstructorQuizController::class, 'storeQuestion'])->name('questions.store');
        Route::delete('/cursos/{course:slug}/questoes/{question}', [InstructorQuizController::class, 'destroyQuestion'])->name('questions.destroy');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
