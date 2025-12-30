<?php

namespace App\Http\Requests\Translator;

use Illuminate\Foundation\Http\FormRequest;

class SubmitTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|array|min:1',
            'title.en' => 'required|string|max:255',
            'title.zh' => 'nullable|string|max:255',
            'title.ko' => 'nullable|string|max:255',
            
            'description' => 'required|array|min:1',
            'description.en' => 'required|string',
            'description.zh' => 'nullable|string',
            'description.ko' => 'nullable|string',
            
            'questions' => 'required|array',
            'questions.*.content' => 'required|array|min:1',
            'questions.*.content.en' => 'required|string',
            'questions.*.content.zh' => 'nullable|string',
            'questions.*.content.ko' => 'nullable|string',
            'questions.*.options' => 'required|array',
            'questions.*.options.*.content' => 'required|array|min:1',
            'questions.*.options.*.content.en' => 'required|string',
            'questions.*.options.*.content.zh' => 'nullable|string',
            'questions.*.options.*.content.ko' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.en.required' => 'English title translation is required.',
            'description.en.required' => 'English description translation is required.',
            'questions.*.content.en.required' => 'English question translation is required.',
            'questions.*.options.*.content.en.required' => 'English option translation is required.',
        ];
    }
}
