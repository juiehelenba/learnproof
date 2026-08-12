<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AiTutorChatRequest;
use App\Models\Course;
use App\Services\Ai\AiTutorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiTutorController extends Controller
{
    public function __construct(
        private AiTutorService $tutor,
    ) {}

    public function history(Request $request, Course $course): JsonResponse
    {
        $this->authorize('useAiTutor', $course);

        return response()->json([
            'data' => $this->tutor->history($request->user(), $course)->map(fn ($m) => [
                'id' => $m->id,
                'role' => $m->role,
                'content' => $m->content,
                'used_fallback' => $m->used_fallback,
                'created_at' => $m->created_at,
            ]),
        ]);
    }

    public function chat(AiTutorChatRequest $request, Course $course): JsonResponse
    {
        $this->authorize('useAiTutor', $course);

        $result = $this->tutor->chat(
            $request->user(),
            $course,
            $request->validated('message')
        );

        return response()->json([
            'data' => [
                'reply' => $result['reply'],
                'interaction_id' => $result['interaction_id'],
                'used_fallback' => $result['used_fallback'],
                'latency_ms' => $result['latency_ms'],
                'context' => $result['context'],
                'history' => $result['history']->map(fn ($m) => [
                    'id' => $m->id,
                    'role' => $m->role,
                    'content' => $m->content,
                    'created_at' => $m->created_at,
                ]),
            ],
        ]);
    }
}
