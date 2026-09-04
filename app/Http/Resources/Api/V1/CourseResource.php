<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Course
 */
class CourseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'passing_score' => $this->passingScore(),
            'is_published' => $this->is_published,
            'lessons_count' => $this->when(
                isset($this->lessons_count),
                fn () => (int) $this->lessons_count,
            ),
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            'enrolled' => $this->when(
                array_key_exists('enrolled', $this->resource->getAttributes())
                    || isset($this->resource->enrolled),
                fn () => (bool) $this->resource->enrolled,
            ),
        ];
    }
}
