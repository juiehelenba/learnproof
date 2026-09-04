<?php

namespace App\Http\Requests\Instructor;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Course::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', 'alpha_dash', Rule::unique('courses', 'slug')],
            'description' => ['required', 'string', 'max:5000'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
