<?php

namespace App\Http\Resources\Api\V1;

use App\Models\AiChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AiChatMessage
 */
class AiChatMessageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'content' => $this->content,
            'used_fallback' => $this->when(
                array_key_exists('used_fallback', $this->resource->getAttributes()),
                fn () => (bool) $this->used_fallback,
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
