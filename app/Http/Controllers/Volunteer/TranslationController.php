<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\SolutionTranslation;
use App\Models\Solution;
use App\Models\UserTree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TranslationController extends Controller
{
    public function __construct()
    {
        $this->middleware('volunteer');
    }

    public function index(Request $request)
    {
        $query = SolutionTranslation::with(['solution', 'language'])
            ->where('is_auto_generated', true)
            ->whereNull('reviewed_at');

        if ($request->filled('language')) {
            $query->where('locale', $request->language);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $translations = $query->latest()->paginate(10);
        
        return view('volunteer.translations.index', compact('translations'));
    }

    public function review(SolutionTranslation $translation)
    {
        // Ensure this is an auto-generated translation and not already reviewed
        if (!$translation->is_auto_generated || $translation->reviewed_at) {
            return redirect()->route('volunteer.translations.index')
                ->with('error', 'This translation cannot be reviewed.');
        }

        $translation->load(['solution', 'language']);
        
        // Get the original Vietnamese translation for comparison
        $originalTranslation = SolutionTranslation::where('solution_id', $translation->solution_id)
            ->where('locale', 'vi')
            ->first();

        return view('volunteer.translations.review', compact('translation', 'originalTranslation'));
    }

    public function approve(Request $request, SolutionTranslation $translation)
    {
        // Ensure this is an auto-generated translation and not already reviewed
        if (!$translation->is_auto_generated || $translation->reviewed_at) {
            return redirect()->route('volunteer.translations.index')
                ->with('error', 'This translation cannot be approved.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DB::transaction(function () use ($translation, $validated) {
            // Update the translation with volunteer's edits and mark as reviewed
            $translation->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'is_auto_generated' => false, // No longer auto-generated after review
            ]);

            // Award EXP to the volunteer
            $userTree = auth()->user()->userTree ?? new UserTree();
            $userTree->user_id = auth()->id();
            $userTree->exp += 10; // Award 10 EXP for translation review
            $userTree->save();

            // Check for level up (simple logic: every 100 EXP = new season)
            if ($userTree->exp % 100 === 0) {
                $userTree->season += 1;
                $userTree->save();
            }
        });

        return redirect()->route('volunteer.translations.index')
            ->with('success', 'Translation approved! You earned 10 EXP.');
    }

    public function reject(Request $request, SolutionTranslation $translation)
    {
        // Ensure this is an auto-generated translation and not already reviewed
        if (!$translation->is_auto_generated || $translation->reviewed_at) {
            return redirect()->route('volunteer.translations.index')
                ->with('error', 'This translation cannot be rejected.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($translation, $validated) {
            // Mark as reviewed but keep is_auto_generated = true
            $translation->update([
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ]);

            // Log the rejection reason (you could create a separate table for this)
            // For now, we'll just log it
            \Log::info('Translation rejected', [
                'translation_id' => $translation->id,
                'reviewer_id' => auth()->id(),
                'reason' => $validated['reason'],
            ]);

            // Award smaller EXP for rejection (5 EXP)
            $userTree = auth()->user()->userTree ?? new UserTree();
            $userTree->user_id = auth()->id();
            $userTree->exp += 5;
            $userTree->save();
        });

        return redirect()->route('volunteer.translations.index')
            ->with('success', 'Translation rejected. You earned 5 EXP for your review.');
    }
}
