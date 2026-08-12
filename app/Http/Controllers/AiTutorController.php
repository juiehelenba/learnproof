<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\AiTutorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiTutorController extends Controller
{
    public function __construct(
        private AiTutorService $tutor,
    ) {}

    public function chat(Request $request, Course $course): JsonResponse
    {
        $enrollment = $request->user()->enrollmentFor($course);

        if (! $enrollment) {
            return response()->json(['error' => 'Matricule-se no curso para usar o tutor de IA.'], 403);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $reply = $this->tutor->chat($request->user(), $course, $validated['message']);

        return response()->json([
            'reply' => $reply,
            'history' => $this->tutor->history($request->user(), $course)->map(fn ($m) => [
                'role' => $m->role,
                'content' => $m->content,
            ]),
        ]);
    }
}
