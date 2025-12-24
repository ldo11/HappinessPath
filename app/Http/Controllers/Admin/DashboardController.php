<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Language;
use App\Models\Solution;
use App\Models\SolutionTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $middleware = [
        'admin'
    ];

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_languages' => Language::where('is_active', true)->count(),
            'total_solutions' => Solution::count(),
            'pending_translations' => SolutionTranslation::where('is_auto_generated', true)
                ->whereNull('reviewed_at')
                ->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentSolutions = Solution::with('translations')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentSolutions'));
    }
}
