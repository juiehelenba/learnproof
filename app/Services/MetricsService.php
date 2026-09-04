<?php

namespace App\Services;

use App\Models\AiInteraction;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MetricsService
{
    /**
     * @return array<string, mixed>
     */
    public function dashboard(int $days = 7): array
    {
        $since = now()->subDays($days)->startOfDay();

        return [
            'period_days' => $days,
            'since' => $since->toIso8601String(),
            'learning' => $this->learningMetrics($since),
            'ai' => $this->aiMetrics($since),
            'certificates' => $this->certificateMetrics(),
            'queue' => $this->queueMetrics(),
            'recent_interactions' => $this->recentInteractions(15),
            'ai_by_course' => $this->aiByCourse($since),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function learningMetrics(Carbon $since): array
    {
        $enrollmentsTotal = Enrollment::query()->count();
        $enrollmentsPeriod = Enrollment::query()->where('created_at', '>=', $since)->count();
        $completed = Enrollment::query()->whereNotNull('completed_at')->count();
        $attempts = QuizAttempt::query()->where('created_at', '>=', $since)->count();
        $passed = QuizAttempt::query()->where('created_at', '>=', $since)->where('passed', true)->count();

        return [
            'courses_published' => Course::query()->where('is_published', true)->count(),
            'courses_draft' => Course::query()->where('is_published', false)->count(),
            'enrollments_total' => $enrollmentsTotal,
            'enrollments_period' => $enrollmentsPeriod,
            'completions_total' => $completed,
            'quiz_attempts_period' => $attempts,
            'quiz_pass_rate_period' => $attempts > 0 ? round(($passed / $attempts) * 100, 1) : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function aiMetrics(Carbon $since): array
    {
        $base = AiInteraction::query()->where('created_at', '>=', $since);

        $total = (clone $base)->count();
        $fallbacks = (clone $base)->where('used_fallback', true)->count();
        $avgLatency = (clone $base)->whereNotNull('latency_ms')->avg('latency_ms');

        $promptTokens = 0;
        $completionTokens = 0;

        AiInteraction::query()
            ->where('created_at', '>=', $since)
            ->whereNotNull('meta')
            ->orderBy('id')
            ->chunkById(200, function (Collection $chunk) use (&$promptTokens, &$completionTokens) {
                foreach ($chunk as $interaction) {
                    $promptTokens += (int) data_get($interaction->meta, 'prompt_tokens', 0);
                    $completionTokens += (int) data_get($interaction->meta, 'completion_tokens', 0);
                }
            });

        $estimatedCost = $this->estimateCostUsd($promptTokens, $completionTokens);

        return [
            'interactions' => $total,
            'fallbacks' => $fallbacks,
            'fallback_rate' => $total > 0 ? round(($fallbacks / $total) * 100, 1) : null,
            'avg_latency_ms' => $avgLatency !== null ? (int) round($avgLatency) : null,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'estimated_cost_usd' => $estimatedCost,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function certificateMetrics(): array
    {
        $total = Certificate::query()->count();
        $simulated = Certificate::query()->where('blockchain_network', 'like', 'mock%')->count();
        $pending = Certificate::query()
            ->whereNull('blockchain_tx_hash')
            ->where(function ($q) {
                $q->whereNull('blockchain_network')
                    ->orWhere('blockchain_network', 'not like', 'mock%');
            })
            ->count();
        $anchored = Certificate::query()->whereNotNull('blockchain_tx_hash')->count();

        return [
            'total' => $total,
            'anchored' => $anchored,
            'simulated' => $simulated,
            'pending' => $pending,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function queueMetrics(): array
    {
        $pending = null;
        $failed = null;

        if (Schema::hasTable('jobs')) {
            $pending = DB::table('jobs')->count();
        }
        if (Schema::hasTable('failed_jobs')) {
            $failed = DB::table('failed_jobs')->count();
        }

        return [
            'connection' => config('queue.default'),
            'pending_jobs' => $pending,
            'failed_jobs' => $failed,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function recentInteractions(int $limit): array
    {
        return AiInteraction::query()
            ->with(['user:id,name', 'course:id,title,slug'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (AiInteraction $i) => [
                'id' => $i->id,
                'created_at' => $i->created_at?->toIso8601String(),
                'user' => $i->user?->name,
                'course' => $i->course?->title,
                'status' => $i->status,
                'used_fallback' => $i->used_fallback,
                'latency_ms' => $i->latency_ms,
                'prompt_tokens' => data_get($i->meta, 'prompt_tokens'),
                'completion_tokens' => data_get($i->meta, 'completion_tokens'),
                'question_preview' => str($i->question)->limit(80)->toString(),
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function aiByCourse(Carbon $since): array
    {
        return AiInteraction::query()
            ->selectRaw('course_id, count(*) as total, sum(case when used_fallback = 1 then 1 else 0 end) as fallbacks, avg(latency_ms) as avg_latency')
            ->where('created_at', '>=', $since)
            ->groupBy('course_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $course = Course::query()->find($row->course_id);

                return [
                    'course_id' => $row->course_id,
                    'course_title' => $course?->title ?? '—',
                    'total' => (int) $row->total,
                    'fallbacks' => (int) $row->fallbacks,
                    'fallback_rate' => $row->total > 0
                        ? round(($row->fallbacks / $row->total) * 100, 1)
                        : null,
                    'avg_latency_ms' => $row->avg_latency !== null ? (int) round($row->avg_latency) : null,
                ];
            })
            ->all();
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
