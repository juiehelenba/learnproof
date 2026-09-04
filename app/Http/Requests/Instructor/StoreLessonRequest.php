<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('course')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $courseId = $this->route('course')?->id;

        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'nullable',
                'string',
                'max:200',
                'alpha_dash',
                Rule::unique('lessons', 'slug')->where(fn ($q) => $q->where('course_id', $courseId)),
            ],
            'content' => ['required', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'video_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
