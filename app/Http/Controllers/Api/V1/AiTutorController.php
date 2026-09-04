<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AiTutorChatRequest;
use App\Http\Resources\Api\V1\AiChatMessageResource;
use App\Http\Resources\Api\V1\AiTutorChatResource;
use App\Models\Course;
use App\Services\Ai\AiTutorService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AiTutorController extends Controller
{
    public function __construct(
        private AiTutorService $tutor,
    ) {}

    public function history(Request $request, Course $course): AnonymousResourceCollection
    {
        $this->authorize('useAiTutor', $course);

        return AiChatMessageResource::collection(
            $this->tutor->history($request->user(), $course)
        )->additional([
            'meta' => ['api_version' => 'v1'],
        ]);
    }

    public function chat(AiTutorChatRequest $request, Course $course): AiTutorChatResource
    {
        $this->authorize('useAiTutor', $course);

        $result = $this->tutor->chat(
            $request->user(),
            $course,
            $request->validated('message')
        );

        return (new AiTutorChatResource($result))->additional([
            'meta' => ['api_version' => 'v1'],
        ]);
    }
}
