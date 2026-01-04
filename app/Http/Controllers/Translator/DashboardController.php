<?php

namespace App\Http\Controllers\Translator;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $assessments = \App\Models\Assessment::whereIn('status', ['created', 'translated'])
            ->with('creator')
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->whereHas('creator', function ($q) {
                $q->where('role', 'consultant');
            })
            ->get();

        $languageLinesCount = \App\Models\LanguageLine::query()->count();

        return view('translator.dashboard', compact('assessments', 'languageLinesCount'));
    }
}
