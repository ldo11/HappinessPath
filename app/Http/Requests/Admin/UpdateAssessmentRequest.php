<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|array|min:1',
            'title.vi' => 'required|string|max:255',
            'title.en' => 'nullable|string|max:255',
            'title.zh' => 'nullable|string|max:255',
            'title.ko' => 'nullable|string|max:255',
            
            'description' => 'required|array|min:1',
            'description.vi' => 'required|string',
            'description.en' => 'nullable|string',
            'description.zh' => 'nullable|string',
            'description.ko' => 'nullable|string',
            
            'questions' => 'required|array|min:1',
            'questions.*.content' => 'required|array|min:1',
            'questions.*.content.vi' => 'required|string',
            'questions.*.type' => ['required', Rule::in(['single_choice', 'multi_choice'])],
            'questions.*.order' => 'required|integer|min:1',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.content' => 'required|array|min:1',
            'questions.*.options.*.content.vi' => 'required|string',
            'questions.*.options.*.score' => 'required|integer|min:1|max:5',
        ];
    }

    public function messages(): array
    {
        return [
            'title.vi.required' => 'Vietnamese title is required.',
            'description.vi.required' => 'Vietnamese description is required.',
            'questions.*.content.vi.required' => 'Vietnamese question content is required.',
            'questions.*.options.*.content.vi.required' => 'Vietnamese option content is required.',
            'questions.*.options.min' => 'Each question must have at least 2 options.',
            'questions.*.options.*.score.min' => 'Option score must be at least 1.',
            'questions.*.options.*.score.max' => 'Option score must not exceed 5.',
        ];
    }
}
