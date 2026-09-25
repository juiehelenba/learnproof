<?php

namespace App\Services\Ai;

use App\Exceptions\AiUsageLimitExceededException;
use App\Models\AiInteraction;
use App\Models\User;
use Illuminate\Support\Carbon;

class AiUsageLimiter
{
    /**
     * @return array{interactions: int, estimated_cost_usd: float, max_interactions: int, max_cost_usd: float}
     */
    public function usageToday(User $user, ?Carbon $day = null): array
    {
        $day ??= now();
        $start = $day->copy()->startOfDay();
        $end = $day->copy()->endOfDay();

        $interactions = AiInteraction::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->get(['meta', 'used_fallback']);

        $promptTokens = 0;
        $completionTokens = 0;

        foreach ($interactions as $interaction) {
            $promptTokens += (int) data_get($interaction->meta, 'prompt_tokens', 0);
            $completionTokens += (int) data_get($interaction->meta, 'completion_tokens', 0);
        }

        return [
            'interactions' => $interactions->count(),
            'estimated_cost_usd' => $this->estimateCostUsd($promptTokens, $completionTokens),
            'max_interactions' => (int) config('learnproof.ai.max_interactions_per_day', 40),
            'max_cost_usd' => (float) config('learnproof.ai.max_estimated_cost_usd_per_day', 0.50),
        ];
    }

    public function assertCanUse(User $user): void
    {
        $usage = $this->usageToday($user);
        $maxInteractions = $usage['max_interactions'];
        $maxCost = $usage['max_cost_usd'];

        if ($maxInteractions > 0 && $usage['interactions'] >= $maxInteractions) {
            throw new AiUsageLimitExceededException(
                "Limite diário de {$maxInteractions} interações com o tutor de IA atingido. Tente novamente amanhã.",
                'interactions',
                $usage['interactions'],
                $maxInteractions,
            );
        }

        if ($maxCost > 0 && $usage['estimated_cost_usd'] >= $maxCost) {
            throw new AiUsageLimitExceededException(
                'Limite diário estimado de custo do tutor de IA atingido. Tente novamente amanhã.',
                'cost',
                $usage['estimated_cost_usd'],
                $maxCost,
            );
        }
    }

    private function estimateCostUsd(int $promptTokens, int $completionTokens): float
    {
        $promptPrice = (float) config('learnproof.ai.price_prompt_per_1m', 0.15);
        $completionPrice = (float) config('learnproof.ai.price_completion_per_1m', 0.60);

        return round(
            ($promptTokens / 1_000_000) * $promptPrice
            + ($completionTokens / 1_000_000) * $completionPrice,
            4
        );
    }
}
