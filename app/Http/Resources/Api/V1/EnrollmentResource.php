<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Enrollment
 */
class EnrollmentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $course = $this->course;

        return [
            'id' => $this->id,
            'course' => [
                'id' => $course?->id,
                'title' => $course?->title,
                'slug' => $course?->slug,
            ],
            'enrolled_at' => $this->enrolled_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'progress_percent' => $this->progressPercent(),
            'lessons_completed' => $this->completedLessonsCount(),
            'lessons_total' => $this->lessonsCount(),
            'completed_lesson_slugs' => $this->when(
                $this->relationLoaded('lessonProgress'),
                fn () => $this->lessonProgress
                    ->loadMissing('lesson:id,slug')
                    ->pluck('lesson.slug')
                    ->filter()
                    ->values()
                    ->all()
            ),
            'has_certificate' => $this->when(
                array_key_exists('has_certificate', $this->resource->getAttributes())
                    || isset($this->resource->has_certificate),
                fn () => (bool) $this->resource->has_certificate,
            ),
        ];
    }
}
