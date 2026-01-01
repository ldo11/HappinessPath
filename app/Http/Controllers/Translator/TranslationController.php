<?php

namespace App\Http\Controllers\Translator;

use App\Http\Controllers\Controller;
use App\Models\SolutionTranslation;
use App\Models\UserTree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TranslationController extends Controller
{
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
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $translations = $query->latest()->paginate(10);

        return view('translator.translations.index', compact('translations'));
    }

    public function review(SolutionTranslation $translation)
    {
        if (!$translation->is_auto_generated || $translation->reviewed_at) {
            return redirect()->route('translator.translations.index')
                ->with('error', 'This translation cannot be reviewed.');
        }

        $translation->load(['solution', 'language']);

        $originalTranslation = SolutionTranslation::where('solution_id', $translation->solution_id)
            ->where('locale', 'vi')
            ->first();

        return view('translator.translations.review', compact('translation', 'originalTranslation'));
    }

    public function approve(Request $request, SolutionTranslation $translation)
    {
        if (!$translation->is_auto_generated || $translation->reviewed_at) {
            return redirect()->route('translator.translations.index')
                ->with('error', 'This translation cannot be approved.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DB::transaction(function () use ($translation, $validated) {
            $translation->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'is_auto_generated' => false,
            ]);

            $userTree = auth()->user()->userTree ?? new UserTree();
            $userTree->user_id = auth()->id();
            $userTree->exp += 10;
            $userTree->save();

            if ($userTree->exp % 100 === 0) {
                $userTree->season += 1;
                $userTree->save();
            }
        });

        return redirect()->route('translator.translations.index')
            ->with('success', 'Translation approved! You earned 10 EXP.');
    }

    public function reject(Request $request, SolutionTranslation $translation)
    {
        if (!$translation->is_auto_generated || $translation->reviewed_at) {
            return redirect()->route('translator.translations.index')
                ->with('error', 'This translation cannot be rejected.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($translation, $validated) {
            $translation->update([
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ]);

            \Log::info('Translation rejected', [
                'translation_id' => $translation->id,
                'reviewer_id' => auth()->id(),
                'reason' => $validated['reason'],
            ]);

            $userTree = auth()->user()->userTree ?? new UserTree();
            $userTree->user_id = auth()->id();
            $userTree->exp += 5;
            $userTree->save();
        });

        return redirect()->route('translator.translations.index')
            ->with('success', 'Translation rejected. You earned 5 EXP for your review.');
    }
}
