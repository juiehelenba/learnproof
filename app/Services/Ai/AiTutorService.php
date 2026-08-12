<?php

namespace App\Services\Ai;

use App\Events\AiTutorInteractionCompleted;
use App\Models\AiChatMessage;
use App\Models\AiInteraction;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Course → Context → AI Tutor.
 *
 * Fluxo:
 * 1) identifica o curso
 * 2) recupera contexto pedagógico (cacheado)
 * 3) envia para o modelo com histórico
 * 4) registra mensagens + interação estruturada
 */
class AiTutorService
{
    public function __construct(
        private CourseContextBuilder $contextBuilder,
    ) {}

    public function chat(User $user, Course $course, string $message): array
    {
        $startedAt = microtime(true);
        $contextMeta = $this->contextBuilder->snapshotMeta($course);
        $systemPrompt = $this->contextBuilder->systemPrompt($course);

        return DB::transaction(function () use ($user, $course, $message, $startedAt, $contextMeta, $systemPrompt) {
            $userMessage = AiChatMessage::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'role' => 'user',
                'content' => $message,
            ]);

            $generation = $this->generateReply($user, $course, $systemPrompt);

            $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);

            $assistantMessage = AiChatMessage::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'role' => 'assistant',
                'content' => $generation['reply'],
                'provider' => $generation['provider'],
                'model' => $generation['model'],
                'used_fallback' => $generation['used_fallback'],
                'latency_ms' => $latencyMs,
                'prompt_tokens' => $generation['prompt_tokens'],
                'completion_tokens' => $generation['completion_tokens'],
                'meta' => $generation['meta'],
            ]);

            $interaction = AiInteraction::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'user_message_id' => $userMessage->id,
                'assistant_message_id' => $assistantMessage->id,
                'question' => $message,
                'answer' => $generation['reply'],
                'status' => $generation['used_fallback'] ? 'fallback' : 'completed',
                'provider' => $generation['provider'],
                'model' => $generation['model'],
                'used_fallback' => $generation['used_fallback'],
                'latency_ms' => $latencyMs,
                'context_chars' => $contextMeta['context_chars'] ?? null,
                'context_snapshot' => $contextMeta,
                'meta' => [
                    'request_id' => (string) Str::uuid(),
                    'history_count' => $generation['history_count'],
                ],
            ]);

            Log::info('learnproof.ai.tutor.completed', [
                'interaction_id' => $interaction->id,
                'user_id' => $user->id,
                'course_id' => $course->id,
                'course_slug' => $course->slug,
                'provider' => $generation['provider'],
                'model' => $generation['model'],
                'used_fallback' => $generation['used_fallback'],
                'latency_ms' => $latencyMs,
                'context_chars' => $contextMeta['context_chars'] ?? null,
                'prompt_tokens' => $generation['prompt_tokens'],
                'completion_tokens' => $generation['completion_tokens'],
            ]);

            AiTutorInteractionCompleted::dispatch($interaction);

            return [
                'reply' => $generation['reply'],
                'interaction_id' => $interaction->id,
                'used_fallback' => $generation['used_fallback'],
                'latency_ms' => $latencyMs,
                'context' => $contextMeta,
                'history' => $this->history($user, $course),
            ];
        });
    }

    public function history(User $user, Course $course): Collection
    {
        return AiChatMessage::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->orderBy('created_at')
            ->limit(config('learnproof.ai.max_history'))
            ->get(['id', 'role', 'content', 'created_at', 'used_fallback']);
    }

    private function generateReply(User $user, Course $course, string $systemPrompt): array
    {
        $provider = config('learnproof.ai.provider', 'openai');
        $model = config('learnproof.ai.model', 'gpt-4o-mini');

        if (! config('learnproof.ai.enabled')) {
            return $this->fallbackPayload($course, $provider, $model, 'ai_disabled');
        }

        $apiKey = config('learnproof.ai.api_key');

        if (blank($apiKey)) {
            return $this->fallbackPayload($course, $provider, $model, 'missing_api_key');
        }

        $history = $this->history($user, $course)
            ->map(fn (AiChatMessage $m) => [
                'role' => $m->role,
                'content' => $m->content,
            ])
            ->values()
            ->all();

        try {
            $response = Http::withToken($apiKey)
                ->timeout((int) config('learnproof.ai.timeout', 30))
                ->acceptJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => array_merge(
                        [['role' => 'system', 'content' => $systemPrompt]],
                        $history
                    ),
                ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content');

                if (filled($reply)) {
                    return [
                        'reply' => $reply,
                        'provider' => $provider,
                        'model' => $model,
                        'used_fallback' => false,
                        'prompt_tokens' => $response->json('usage.prompt_tokens'),
                        'completion_tokens' => $response->json('usage.completion_tokens'),
                        'history_count' => count($history),
                        'meta' => [
                            'openai_id' => $response->json('id'),
                        ],
                    ];
                }
            }

            Log::warning('learnproof.ai.tutor.provider_failed', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => $response->status(),
                'body' => Str::limit($response->body(), 500),
            ]);
        } catch (\Throwable $e) {
            Log::error('learnproof.ai.tutor.exception', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);
        }

        return $this->fallbackPayload($course, $provider, $model, 'provider_error', count($history));
    }

    private function fallbackPayload(
        Course $course,
        string $provider,
        string $model,
        string $reason,
        int $historyCount = 0,
    ): array {
        return [
            'reply' => $this->fallbackReply($course),
            'provider' => $provider,
            'model' => $model,
            'used_fallback' => true,
            'prompt_tokens' => null,
            'completion_tokens' => null,
            'history_count' => $historyCount,
            'meta' => ['fallback_reason' => $reason],
        ];
    }

    private function fallbackReply(Course $course): string
    {
        return 'Olá! Sou o tutor de IA do curso **'.$course->title.'**. '
            .'No momento estou em modo demonstração (configure `OPENAI_API_KEY` no `.env` para respostas com o contexto completo do curso). '
            .'Revise as aulas, pergunte sobre os temas do curso e, ao terminar, faça o quiz para conquistar seu certificado verificável.';
    }
}
