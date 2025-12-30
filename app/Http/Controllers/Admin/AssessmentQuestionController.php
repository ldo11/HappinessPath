<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentQuestion;
use Illuminate\Http\Request;

class AssessmentQuestionController extends Controller
{
    protected $middleware = [
        'admin'
    ];

    public function index()
    {
        $questions = AssessmentQuestion::query()->orderBy('pillar_group')->orderBy('order')->paginate(20);

        return view('admin.assessment-questions.index', compact('questions'));
    }

    public function create()
    {
        return view('admin.assessment-questions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content_vi' => ['required', 'string'],
            'content_en' => ['nullable', 'string'],
            'pillar_group' => ['required', 'in:heart,grit,wisdom'],
            'order' => ['nullable', 'integer', 'min:1'],
        ]);

        AssessmentQuestion::create([
            'content' => [
                'vi' => $data['content_vi'],
                'en' => $data['content_en'] ?? null,
            ],
            'pillar_group' => $data['pillar_group'],
            'order' => $data['order'] ?? 1,
        ]);

        return redirect()->route('admin.assessment-questions.index');
    }

    public function edit(AssessmentQuestion $assessmentQuestion)
    {
        return view('admin.assessment-questions.edit', [
            'question' => $assessmentQuestion,
        ]);
    }

    public function update(Request $request, AssessmentQuestion $assessmentQuestion)
    {
        $data = $request->validate([
            'content_vi' => ['required', 'string'],
            'content_en' => ['nullable', 'string'],
            'pillar_group' => ['required', 'in:heart,grit,wisdom'],
            'order' => ['nullable', 'integer', 'min:1'],
        ]);

        $assessmentQuestion->update([
            'content' => [
                'vi' => $data['content_vi'],
                'en' => $data['content_en'] ?? null,
            ],
            'pillar_group' => $data['pillar_group'],
            'order' => $data['order'] ?? $assessmentQuestion->order,
        ]);

        return redirect()->route('admin.assessment-questions.index');
    }

    public function destroy(AssessmentQuestion $assessmentQuestion)
    {
        $assessmentQuestion->delete();

        return redirect()->route('admin.assessment-questions.index');
    }
}
