<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreQuestionRequest extends FormRequest
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
        return [
            'text' => ['required', 'string', 'max:2000'],
            'explanation' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'options' => ['required', 'array', 'min:2', 'max:6'],
            'options.*.text' => ['required', 'string', 'max:500'],
            'correct_option' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $options = $this->input('options', []);
            $correct = (int) $this->input('correct_option', -1);

            if (! array_key_exists($correct, $options)) {
                $validator->errors()->add('correct_option', 'Selecione qual alternativa é a correta.');
            }
        });
    }
}
