<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\LanguageLine;
use Illuminate\Http\Request;

class TranslatorController extends Controller
{
    public function index(Request $request)
    {
        $assessments = Assessment::whereIn('status', ['created', 'translated'])
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        $languageLinesCount = LanguageLine::count();

        return view('translator.dashboard', compact('assessments', 'languageLinesCount'));
    }

    public function translate($id)
    {
        $assessment = Assessment::findOrFail($id);
        
        if (!in_array($assessment->status, ['created', 'translated'])) {
            abort(404, 'Assessment not available for translation');
        }

        return view('translator.assessments.translate', compact('assessment'));
    }

    public function submitTranslation(Request $request, $id)
    {
        $assessment = Assessment::findOrFail($id);
        
        if (!in_array($assessment->status, ['created', 'translated'])) {
            abort(404, 'Assessment not available for translation');
        }

        $validated = $request->validate([
            'title' => 'required|array',
            'title.en' => 'required|string',
            'title.vi' => 'required|string',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.vi' => 'required|string',
        ]);

        $assessment->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'translated',
        ]);

        return redirect()
            ->route('translator.dashboard')
            ->with('success', 'Translation submitted successfully');
    }
}
