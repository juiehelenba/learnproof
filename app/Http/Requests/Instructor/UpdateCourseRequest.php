<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
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
                'required',
                'string',
                'max:200',
                'alpha_dash',
                Rule::unique('courses', 'slug')->ignore($courseId),
            ],
            'description' => ['required', 'string', 'max:5000'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
