<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SolutionTranslation;
use App\Models\UserTree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $middleware = [
        'volunteer'
    ];

    public function index()
    {
        $user = auth()->user();
        $userTree = $user->userTree ?? new UserTree();
        
        $stats = [
            'pending_translations' => SolutionTranslation::where('is_auto_generated', true)
                ->whereNull('reviewed_at')
                ->count(),
            'reviewed_today' => SolutionTranslation::where('reviewed_by', $user->id)
                ->whereDate('reviewed_at', today())
                ->count(),
            'total_reviewed' => SolutionTranslation::where('reviewed_by', $user->id)
                ->count(),
            'current_level' => $userTree->season ?? 1,
            'current_exp' => $userTree->exp ?? 0,
            'fruits_given' => $userTree->total_fruits_given ?? 0,
        ];

        $recentReviews = SolutionTranslation::with(['solution', 'reviewer'])
            ->where('reviewed_by', $user->id)
            ->latest('reviewed_at')
            ->take(5)
            ->get();

        $pendingTranslations = SolutionTranslation::with(['solution', 'language'])
            ->where('is_auto_generated', true)
            ->whereNull('reviewed_at')
            ->latest()
            ->take(5)
            ->get();

        return view('volunteer.dashboard', compact('stats', 'recentReviews', 'pendingTranslations'));
    }
}
