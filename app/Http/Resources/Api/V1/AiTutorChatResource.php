<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiTutorChatResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'reply' => $this->resource['reply'],
            'interaction_id' => $this->resource['interaction_id'],
            'used_fallback' => $this->resource['used_fallback'],
            'latency_ms' => $this->resource['latency_ms'],
            'context' => $this->resource['context'],
            'history' => AiChatMessageResource::collection($this->resource['history']),
        ];
    }
}
