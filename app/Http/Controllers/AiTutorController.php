<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\V1\AiTutorChatRequest;
use App\Models\Course;
use App\Services\Ai\AiTutorService;
use Illuminate\Http\JsonResponse;

class AiTutorController extends Controller
{
    public function __construct(
        private AiTutorService $tutor,
    ) {}

    public function chat(AiTutorChatRequest $request, Course $course): JsonResponse
    {
        $this->authorize('useAiTutor', $course);

        $result = $this->tutor->chat(
            $request->user(),
            $course,
            $request->validated('message')
        );

        return response()->json([
            'reply' => $result['reply'],
            'interaction_id' => $result['interaction_id'],
            'used_fallback' => $result['used_fallback'],
            'latency_ms' => $result['latency_ms'],
            'context' => $result['context'],
            'history' => $result['history']->map(fn ($m) => [
                'role' => $m->role,
                'content' => $m->content,
            ]),
        ]);
    }
}
