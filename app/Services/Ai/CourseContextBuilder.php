<?php

namespace App\Services\Ai;

use App\Models\Course;
use Illuminate\Support\Facades\Cache;

/**
 * Course → Context: monta o contexto pedagógico do curso para o tutor.
 */
class CourseContextBuilder
{
    public function build(Course $course): array
    {
        $ttl = (int) config('learnproof.ai.context_cache_ttl', 600);

        return Cache::remember(
            $this->cacheKey($course),
            $ttl,
            fn () => $this->buildFresh($course)
        );
    }

    public function forget(Course $course): void
    {
        Cache::forget($this->cacheKey($course));
    }

    public function systemPrompt(Course $course): string
    {
        $context = $this->build($course);

        $lessonsBlock = collect($context['lessons'])
            ->map(function (array $lesson, int $index) {
                $n = $index + 1;
                $excerpt = $lesson['excerpt'];

                return "### Aula {$n}: {$lesson['title']}\n{$excerpt}";
            })
            ->implode("\n\n");

        return <<<PROMPT
Você é o tutor de IA do curso "{$context['title']}" na plataforma LearnProof.

## Objetivo
Ajudar o aluno a compreender o conteúdo deste curso com base APENAS no contexto abaixo.
Responda em português brasileiro, de forma clara, objetiva e didática.
Não invente certificados, notas, aprovações nem conteúdo fora deste curso.
Se a pergunta estiver fora do escopo, diga isso e sugira revisar as aulas ou o quiz.

## Sobre o curso
{$context['description']}

## Nota mínima do quiz
{$context['passing_score']}%

## Conteúdo das aulas
{$lessonsBlock}
PROMPT;
    }

    public function snapshotMeta(Course $course): array
    {
        $context = $this->build($course);

        return [
            'course_id' => $context['id'],
            'course_slug' => $context['slug'],
            'lesson_count' => count($context['lessons']),
            'context_chars' => $context['context_chars'],
            'cached' => Cache::has($this->cacheKey($course)),
        ];
    }

    private function buildFresh(Course $course): array
    {
        $course->loadMissing(['lessons' => fn ($q) => $q->orderBy('sort_order')]);

        $maxExcerpt = (int) config('learnproof.ai.lesson_excerpt_chars', 1200);

        $lessons = $course->lessons->map(function ($lesson) use ($maxExcerpt) {
            $plain = trim(preg_replace('/\s+/', ' ', strip_tags($lesson->content)) ?? '');
            $excerpt = mb_strlen($plain) > $maxExcerpt
                ? mb_substr($plain, 0, $maxExcerpt).'…'
                : $plain;

            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'slug' => $lesson->slug,
                'excerpt' => $excerpt,
            ];
        })->values()->all();

        $payload = [
            'id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'description' => $course->description,
            'passing_score' => $course->passingScore(),
            'lessons' => $lessons,
        ];

        $payload['context_chars'] = mb_strlen(json_encode($payload, JSON_UNESCAPED_UNICODE));

        return $payload;
    }

    private function cacheKey(Course $course): string
    {
        return "learnproof.course_context.{$course->id}";
    }
}
