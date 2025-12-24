<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentAnswer;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function getQuestions()
    {
        $questions = AssessmentQuestion::with('answers')
            ->orderBy('order')
            ->get()
            ->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->getLocalizedContent(app()->getLocale()),
                    'options' => $question->answers->map(function ($answer) {
                        return [
                            'value' => $answer->score,
                            'text' => $answer->getLocalizedContent(app()->getLocale())
                        ];
                    })->sortBy('value')->values()
                ];
            });

        return response()->json($questions);
    }
}
