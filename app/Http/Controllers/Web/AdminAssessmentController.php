<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;

class AdminAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $assessments = Assessment::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.assessments.index', compact('assessments'));
    }

    public function create()
    {
        return view('admin.assessments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.vi' => 'required|string|max:255',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.vi' => 'required|string',
        ]);

        $assessment = Assessment::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'created',
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.assessments.index')
            ->with('success', 'Assessment created successfully');
    }

    public function show(Assessment $assessment)
    {
        $assessment->load(['questions.options', 'creator']);
        return view('admin.assessments.show', compact('assessment'));
    }

    public function edit(Assessment $assessment)
    {
        return view('admin.assessments.edit', compact('assessment'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.vi' => 'required|string|max:255',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.vi' => 'required|string',
        ]);

        $assessment->update($validated);

        return redirect()
            ->route('admin.assessments.index')
            ->with('success', 'Assessment updated successfully');
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();

        return redirect()
            ->route('admin.assessments.index')
            ->with('success', 'Assessment deleted successfully');
    }

    public function requestTranslation(Assessment $assessment)
    {
        $assessment->update(['status' => 'created']);

        return redirect()
            ->route('admin.assessments.index')
            ->with('success', 'Translation requested successfully');
    }

    public function publish($id)
    {
        $assessment = Assessment::findOrFail($id);
        $assessment->update(['status' => 'active']);

        return redirect()
            ->route('admin.assessments.index')
            ->with('success', 'Assessment published successfully');
    }

    public function markSpecial(Assessment $assessment)
    {
        $assessment->update(['status' => 'special']);

        return redirect()
            ->route('admin.assessments.index')
            ->with('success', 'Assessment marked as special successfully');
    }
}
