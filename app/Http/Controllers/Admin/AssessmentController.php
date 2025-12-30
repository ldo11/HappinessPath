<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAssessmentRequest;
use App\Http\Requests\Admin\UpdateAssessmentRequest;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use App\Models\UserAssessment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AssessmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::with('creator')
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.assessments.index', compact('assessments'));
    }

    public function create()
    {
        return view('admin.assessments.create');
    }

    public function store(StoreAssessmentRequest $request)
    {
        $validated = $request->validated();

        $assessment = Assessment::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'created',
            'created_by' => Auth::id(),
        ]);

        foreach ($validated['questions'] as $questionData) {
            $question = AssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'content' => $questionData['content'],
                'type' => $questionData['type'],
                'order' => $questionData['order'],
            ]);

            foreach ($questionData['options'] as $optionData) {
                AssessmentOption::create([
                    'question_id' => $question->id,
                    'content' => $optionData['content'],
                    'score' => $optionData['score'],
                ]);
            }
        }

        return redirect()
            ->route('admin.assessments.index')
            ->with('success', 'Assessment created successfully!');
    }

    public function edit(Assessment $assessment)
    {
        $assessment->load(['questions.options']);
        return view('admin.assessments.edit', compact('assessment'));
    }

    public function update(UpdateAssessmentRequest $request, Assessment $assessment)
    {
        $validated = $request->validated();

        $assessment->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
        ]);

        // Delete existing questions and options
        $assessment->questions()->each(function ($question) {
            $question->options()->delete();
            $question->delete();
        });

        // Create new questions and options
        foreach ($validated['questions'] as $questionData) {
            $question = AssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'content' => $questionData['content'],
                'type' => $questionData['type'],
                'order' => $questionData['order'],
            ]);

            foreach ($questionData['options'] as $optionData) {
                AssessmentOption::create([
                    'question_id' => $question->id,
                    'content' => $optionData['content'],
                    'score' => $optionData['score'],
                ]);
            }
        }

        return redirect()
            ->route('admin.assessments.index')
            ->with('success', 'Assessment updated successfully!');
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->questions()->each(function ($question) {
            $question->options()->delete();
            $question->delete();
        });
        $assessment->delete();

        return redirect()
            ->route('admin.assessments.index')
            ->with('success', 'Assessment deleted successfully!');
    }

    public function requestTranslation(Assessment $assessment)
    {
        if ($assessment->status !== 'created') {
            return back()->with('error', 'Only assessments with "created" status can be sent for translation.');
        }

        $assessment->update(['status' => 'created']); // Status stays 'created' but notifies translator

        return back()->with('success', 'Translation request sent to translator!');
    }

    public function approveAndPublish(Assessment $assessment)
    {
        if ($assessment->status !== 'reviewed') {
            return back()->with('error', 'Only reviewed assessments can be published.');
        }

        $assessment->update(['status' => 'active']);

        return back()->with('success', 'Assessment published successfully!');
    }

    public function markAsSpecial(Assessment $assessment)
    {
        if ($assessment->status !== 'reviewed') {
            return back()->with('error', 'Only reviewed assessments can be marked as special.');
        }

        $assessment->update(['status' => 'special']);

        return back()->with('success', 'Assessment marked as special!');
    }

    public function markAsReviewed(Assessment $assessment)
    {
        if ($assessment->status !== 'translated') {
            return back()->with('error', 'Only translated assessments can be reviewed.');
        }

        $assessment->update(['status' => 'reviewed']);

        return back()->with('success', 'Assessment marked as reviewed!');
    }

    public function userResults(Assessment $assessment)
    {
        $userAssessments = UserAssessment::where('assessment_id', $assessment->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Filter results based on submission mode and user role
        $filteredResults = $userAssessments->map(function ($userAssessment) {
            $result = $userAssessment->toArray();
            
            // Hide private results from non-admin users
            if ($userAssessment->submission_mode === 'self_review' && Auth::user()->role !== 'admin') {
                $result['total_score'] = 'Private';
                $result['answers'] = null;
                $result['is_private'] = true;
            } else {
                $result['is_private'] = false;
            }
            
            return $result;
        });

        return view('admin.assessments.results', compact('assessment', 'filteredResults'));
    }
}
