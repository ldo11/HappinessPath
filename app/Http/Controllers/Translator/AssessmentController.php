<?php

namespace App\Http\Controllers\Translator;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::whereIn('status', ['created', 'translated'])
            ->with('creator')
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->whereHas('creator', function ($q) {
                $q->where('role', 'consultant');
            })
            ->get();

        return view('translator.assessments.index', compact('assessments'));
    }

    public function translate(Assessment $assessment)
    {
        if (!in_array($assessment->status, ['created', 'translated'])) {
            return back()->with('error', 'This assessment cannot be translated.');
        }

        $assessment->load(['questions.answers']);

        return view('translator.assessments.translate', compact('assessment'));
    }

    public function submitTranslation(Request $request, Assessment $assessment)
    {
        if ($assessment->status !== 'created' && $assessment->status !== 'translated') {
            return back()->with('error', 'This assessment cannot be translated.');
        }

        $data = $request->validate([
            'title' => ['nullable', 'array'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'title.vi' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.vi' => ['nullable', 'string'],
            'questions' => ['nullable', 'array'],
            'questions.*.content' => ['nullable', 'array'],
            'questions.*.content.en' => ['nullable', 'string'],
            'questions.*.content.vi' => ['nullable', 'string'],
            'answers' => ['nullable', 'array'],
            'answers.*.content' => ['nullable', 'array'],
            'answers.*.content.en' => ['nullable', 'string'],
            'answers.*.content.vi' => ['nullable', 'string'],
        ]);

        foreach (['en', 'vi'] as $locale) {
            if (array_key_exists('title', $data) && array_key_exists($locale, (array) $data['title'])) {
                $assessment->setTranslation('title', $locale, (string) ($data['title'][$locale] ?? ''));
            }
            if (array_key_exists('description', $data) && array_key_exists($locale, (array) $data['description'])) {
                $assessment->setTranslation('description', $locale, (string) ($data['description'][$locale] ?? ''));
            }
        }
        $assessment->save();

        $assessment->load(['questions.answers']);

        $questionMap = (array) ($data['questions'] ?? []);
        foreach ($assessment->questions as $question) {
            $row = (array) ($questionMap[$question->id] ?? []);
            $content = (array) ($row['content'] ?? []);
            foreach (['en', 'vi'] as $locale) {
                if (array_key_exists($locale, $content)) {
                    $question->setTranslation('content', $locale, (string) ($content[$locale] ?? ''));
                }
            }
            $question->save();
        }

        $answerMap = (array) ($data['answers'] ?? []);
        foreach ($assessment->questions as $question) {
            foreach ($question->answers as $answer) {
                $row = (array) ($answerMap[$answer->id] ?? []);
                $content = (array) ($row['content'] ?? []);
                foreach (['en', 'vi'] as $locale) {
                    if (array_key_exists($locale, $content)) {
                        $answer->setTranslation('content', $locale, (string) ($content[$locale] ?? ''));
                    }
                }
                $answer->save();
            }
        }

        if ($assessment->status === 'created') {
            $assessment->update(['status' => 'translated']);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $assessment->id,
                'status' => $assessment->status,
            ]);
        }

        return redirect()
            ->route('translator.assessments.index')
            ->with('success', 'Translation submitted successfully!');
    }
}
