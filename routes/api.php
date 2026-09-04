<?php

use App\Http\Controllers\Api\V1\AiTutorController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\OpenApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/', [OpenApiController::class, 'index'])->name('index');
    Route::get('/openapi.yaml', [OpenApiController::class, 'spec'])->name('openapi');
    Route::get('/docs', [OpenApiController::class, 'ui'])->name('docs');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login');

    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::middleware('throttle:20,1')->group(function () {
            Route::get('/courses/{course:slug}/ai/history', [AiTutorController::class, 'history'])
                ->name('courses.ai.history');
            Route::post('/courses/{course:slug}/ai/chat', [AiTutorController::class, 'chat'])
                ->name('courses.ai.chat');
        });

        Route::middleware('role:admin,instructor')->group(function () {
            Route::get('/staff/ping', function () {
                return response()->json([
                    'data' => [
                        'message' => 'Acesso staff OK',
                        'role' => auth()->user()->role?->value,
                    ],
                    'meta' => ['api_version' => 'v1'],
                ]);
            })->name('staff.ping');
        });
    });
});
