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
            'title.vi' => 'nullable|string|max:255|required_without:title.en',
            'title.en' => 'nullable|string|max:255|required_without:title.vi',
            'title.zh' => 'nullable|string|max:255',
            'title.ko' => 'nullable|string|max:255',
            
            'description' => 'required|array|min:1',
            'description.vi' => 'nullable|string|required_without:description.en',
            'description.en' => 'nullable|string|required_without:description.vi',
            'description.zh' => 'nullable|string',
            'description.ko' => 'nullable|string',

            'status' => ['nullable', Rule::in(['created', 'active'])],
            
            'questions' => 'required|array|min:1',
            'questions.*.content' => 'required|array|min:1',
            'questions.*.content.vi' => 'nullable|string|required_without:questions.*.content.en',
            'questions.*.content.en' => 'nullable|string|required_without:questions.*.content.vi',
            'questions.*.type' => ['required', Rule::in(['single_choice', 'multi_choice'])],
            'questions.*.order' => 'required|integer|min:1',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.content' => 'required|array|min:1',
            'questions.*.options.*.content.vi' => 'nullable|string|required_without:questions.*.options.*.content.en',
            'questions.*.options.*.content.en' => 'nullable|string|required_without:questions.*.options.*.content.vi',
            'questions.*.options.*.content.en' => 'nullable|string|required_without:questions.*.options.*.content.vi',
            'questions.*.options.*.score' => 'required|integer|min:1|max:5',
        ];
    }

    public function messages(): array
    {
        return [
            'title.vi.required_without' => 'Vietnamese title or English title is required.',
            'title.en.required_without' => 'English title or Vietnamese title is required.',
            'description.vi.required_without' => 'Vietnamese description or English description is required.',
            'description.en.required_without' => 'English description or Vietnamese description is required.',
            'questions.*.content.vi.required_without' => 'Vietnamese question content or English question content is required.',
            'questions.*.content.en.required_without' => 'English question content or Vietnamese question content is required.',
            'questions.*.options.*.content.vi.required_without' => 'Vietnamese option content or English option content is required.',
            'questions.*.options.*.content.en.required_without' => 'English option content or Vietnamese option content is required.',
            'questions.*.options.min' => 'Each question must have at least 2 options.',
            'questions.*.options.*.score.min' => 'Option score must be at least 1.',
            'questions.*.options.*.score.max' => 'Option score must not exceed 5.',
        ];
    }
}
