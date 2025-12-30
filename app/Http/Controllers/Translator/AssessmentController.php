<?php

namespace App\Http\Controllers\Translator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Translator\SubmitTranslationRequest;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::whereIn('status', ['created', 'translated'])
            ->with('creator')
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('translator.assessments.index', compact('assessments'));
    }

    public function translate($assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        
        if (!in_array($assessment->status, ['created', 'translated'])) {
            return back()->with('error', 'This assessment cannot be translated.');
        }

        $assessment->load(['questions.options']);

        return view('translator.assessments.translate', compact('assessment'));
    }

    public function submitTranslation(SubmitTranslationRequest $request, $assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        
        if ($assessment->status !== 'created' && $assessment->status !== 'translated') {
            return back()->with('error', 'This assessment cannot be translated.');
        }

        $validated = $request->validated();

        // Update assessment title and description
        $currentTitle = $assessment->title;
        $currentDescription = $assessment->description;
        
        $assessment->update([
            'title' => array_merge($currentTitle, $validated['title']),
            'description' => array_merge($currentDescription, $validated['description']),
        ]);

        // Update questions and options
        foreach ($validated['questions'] as $index => $questionData) {
            $question = $assessment->questions()->where('order', $index + 1)->first();
            
            if ($question) {
                $currentContent = $question->content;
                $question->update([
                    'content' => array_merge($currentContent, $questionData['content']),
                ]);

                // Update options
                foreach ($questionData['options'] as $optionIndex => $optionData) {
                    $option = $question->options()->skip($optionIndex)->first();
                    
                    if ($option) {
                        $currentOptionContent = $option->content;
                        $option->update([
                            'content' => array_merge($currentOptionContent, $optionData['content']),
                        ]);
                    }
                }
            }
        }

        $assessment->update(['status' => 'translated']);

        return redirect()
            ->route('translator.assessments.index')
            ->with('success', 'Translation submitted successfully!');
    }
}
