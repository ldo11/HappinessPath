<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solution;
use App\Models\SolutionTranslation;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SolutionController extends Controller
{
    protected $middleware = [
        'admin'
    ];

    public function index(Request $request)
    {
        $query = Solution::with('translations');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('author_name', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhereHas('translations', function($subQ) use ($search) {
                      $subQ->where('title', 'like', "%{$search}%")
                           ->orWhere('content', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('pillar_tag')) {
            $query->where('pillar_tag', $request->pillar_tag);
        }

        if ($request->filled('locale')) {
            $query->where('locale', $request->locale);
        }

        $solutions = $query->latest()->paginate(15);
        $languages = Language::where('is_active', true)->get();
        
        return view('admin.solutions.index', compact('solutions', 'languages'));
    }

    public function create()
    {
        $languages = Language::where('is_active', true)->get();
        return view('admin.solutions.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['video', 'article'])],
            'url' => 'required|url',
            'author_name' => 'nullable|string|max:255',
            'pillar_tag' => ['required', Rule::in(['heart', 'grit', 'wisdom'])],
            'locale' => 'required|string|max:10|exists:languages,code',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:10|exists:languages,code',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.content' => 'required|string',
        ]);

        $solution = Solution::create([
            'type' => $validated['type'],
            'url' => $validated['url'],
            'author_name' => $validated['author_name'],
            'pillar_tag' => $validated['pillar_tag'],
            'locale' => $validated['locale'],
        ]);

        foreach ($validated['translations'] as $translationData) {
            SolutionTranslation::create([
                'solution_id' => $solution->id,
                'locale' => $translationData['locale'],
                'title' => $translationData['title'],
                'content' => $translationData['content'],
                'is_auto_generated' => false,
            ]);
        }

        return redirect()->route('admin.solutions.index')
            ->with('success', 'Solution created successfully.');
    }

    public function edit(Solution $solution)
    {
        $solution->load('translations');
        $languages = Language::where('is_active', true)->get();
        
        return view('admin.solutions.edit', compact('solution', 'languages'));
    }

    public function update(Request $request, Solution $solution)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['video', 'article'])],
            'url' => 'required|url',
            'author_name' => 'nullable|string|max:255',
            'pillar_tag' => ['required', Rule::in(['heart', 'grit', 'wisdom'])],
            'locale' => 'required|string|max:10|exists:languages,code',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:10|exists:languages,code',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.content' => 'required|string',
        ]);

        $solution->update([
            'type' => $validated['type'],
            'url' => $validated['url'],
            'author_name' => $validated['author_name'],
            'pillar_tag' => $validated['pillar_tag'],
            'locale' => $validated['locale'],
        ]);

        // Update or create translations
        foreach ($validated['translations'] as $translationData) {
            SolutionTranslation::updateOrCreate(
                [
                    'solution_id' => $solution->id,
                    'locale' => $translationData['locale'],
                ],
                [
                    'title' => $translationData['title'],
                    'content' => $translationData['content'],
                    'is_auto_generated' => false,
                ]
            );
        }

        return redirect()->route('admin.solutions.index')
            ->with('success', 'Solution updated successfully.');
    }

    public function destroy(Solution $solution)
    {
        $solution->delete();

        return redirect()->route('admin.solutions.index')
            ->with('success', 'Solution deleted successfully.');
    }

    public function show(Solution $solution)
    {
        $solution->load('translations.language');
        return view('admin.solutions.show', compact('solution'));
    }
}
