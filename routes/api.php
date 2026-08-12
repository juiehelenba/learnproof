<?php

use App\Http\Controllers\Api\V1\AiTutorController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CourseController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{course:slug}', [CourseController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::middleware('throttle:20,1')->group(function () {
            Route::get('/courses/{course:slug}/ai/history', [AiTutorController::class, 'history']);
            Route::post('/courses/{course:slug}/ai/chat', [AiTutorController::class, 'chat']);
        });

        Route::middleware('role:admin,instructor')->group(function () {
            Route::get('/staff/ping', fn () => response()->json([
                'message' => 'Acesso staff OK',
                'role' => auth()->user()->role?->value,
            ]));
        });
    });
});
