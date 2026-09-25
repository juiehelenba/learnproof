<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiUsageLimitExceededException extends Exception
{
    public function __construct(
        string $message,
        public readonly string $limitType,
        public readonly int|float $used,
        public readonly int|float $limit,
    ) {
        parent::__construct($message);
    }

    public function render(Request $request): ?JsonResponse
    {
        if (! $request->expectsJson() && ! $request->is('api/*')) {
            return null;
        }

        return response()->json([
            'message' => $this->getMessage(),
            'error' => 'ai_usage_limit_exceeded',
            'limit_type' => $this->limitType,
            'used' => $this->used,
            'limit' => $this->limit,
            'meta' => ['api_version' => 'v1'],
        ], 429);
    }
}
