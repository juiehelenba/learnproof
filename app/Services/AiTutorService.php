<?php

namespace App\Services;

use App\Models\AiChatMessage;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class AiTutorService
{
    public function chat(User $user, Course $course, string $message): string
    {
        AiChatMessage::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'role' => 'user',
            'content' => $message,
        ]);

        $reply = $this->generateReply($user, $course, $message);

        AiChatMessage::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'role' => 'assistant',
            'content' => $reply,
        ]);

        return $reply;
    }

    public function history(User $user, Course $course): Collection
    {
        return AiChatMessage::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->orderBy('created_at')
            ->limit(config('learnproof.ai.max_history'))
            ->get();
    }

    private function generateReply(User $user, Course $course, string $message): string
    {
        if (! config('learnproof.ai.enabled')) {
            return $this->fallbackReply($course, $message);
        }

        $apiKey = config('learnproof.ai.api_key');

        if (blank($apiKey)) {
            return $this->fallbackReply($course, $message);
        }

        try {
            $history = $this->history($user, $course)
                ->map(fn (AiChatMessage $m) => [
                    'role' => $m->role,
                    'content' => $m->content,
                ])
                ->values()
                ->all();

            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('learnproof.ai.model'),
                    'messages' => array_merge(
                        [['role' => 'system', 'content' => $this->systemPrompt($course)]],
                        $history
                    ),
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content')
                    ?? $this->fallbackReply($course, $message);
            }
        } catch (\Throwable) {
            // fallback silencioso em dev
        }

        return $this->fallbackReply($course, $message);
    }

    private function systemPrompt(Course $course): string
    {
        $lessonTitles = $course->lessons->pluck('title')->implode(', ');

        return "Você é o tutor de IA do curso \"{$course->title}\" na plataforma LearnProof. "
            ."Ajude o aluno com conceitos das aulas: {$lessonTitles}. "
            .'Responda em português brasileiro, de forma clara e objetiva. '
            .'Não invente certificados nem notas; incentive o estudo e o quiz final.';
    }

    private function fallbackReply(Course $course, string $message): string
    {
        return 'Olá! Sou o tutor de IA do curso **'.$course->title."**. "
            .'No momento estou em modo demonstração (configure `OPENAI_API_KEY` no `.env` para respostas completas). '
            .'Sua pergunta foi: «'.$message.'». '
            .'Revise as aulas do curso e, ao terminar, faça o quiz para conquistar seu certificado verificável na blockchain.';
    }
}
