<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\TreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeditationController extends Controller
{
    protected $treeService;

    public function __construct(TreeService $treeService)
    {
        $this->treeService = $treeService;
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $user = Auth::user();
        
        // Redirect to onboarding if not completed
        if (!$user->onboarding_completed) {
            return redirect()->route('onboarding.step1');
        }

        $treeStatus = $this->treeService->getTreeStatus($user);
        $streakInfo = $this->treeService->getStreakInfo($user);

        // Available meditation types
        $meditationTypes = [
            [
                'id' => 'mindfulness',
                'name' => 'Mindfulness',
                'description' => 'Focus on the present moment with guided awareness',
                'duration' => [5, 10, 15, 20],
                'icon' => 'fa-brain',
                'color' => 'purple',
                'audio_url' => '/audio/mindfulness.mp3'
            ],
            [
                'id' => 'breathing',
                'name' => 'Breathing',
                'description' => 'Deep breathing exercises for calm and relaxation',
                'duration' => [3, 5, 10, 15],
                'icon' => 'fa-wind',
                'color' => 'blue',
                'audio_url' => '/audio/breathing.mp3'
            ],
            [
                'id' => 'loving-kindness',
                'name' => 'Loving Kindness',
                'description' => 'Cultivate compassion and kindness for yourself and others',
                'duration' => [10, 15, 20, 30],
                'icon' => 'fa-heart',
                'color' => 'pink',
                'audio_url' => '/audio/loving-kindness.mp3'
            ],
            [
                'id' => 'body-scan',
                'name' => 'Body Scan',
                'description' => 'Progressive relaxation through body awareness',
                'duration' => [10, 15, 20, 25],
                'icon' => 'fa-user',
                'color' => 'green',
                'audio_url' => '/audio/body-scan.mp3'
            ],
            [
                'id' => 'walking',
                'name' => 'Walking',
                'description' => 'Mindful walking meditation for movement and awareness',
                'duration' => [10, 15, 20, 30],
                'icon' => 'fa-walking',
                'color' => 'orange',
                'audio_url' => '/audio/walking.mp3'
            ]
        ];

        return view('meditate', compact(
            'user', 
            'treeStatus', 
            'streakInfo', 
            'meditationTypes'
        ));
    }

    public function startSession(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:mindfulness,breathing,loving-kindness,body-scan,walking',
            'duration' => 'required|integer|min:1|max:60',
        ]);

        // Store session start time in session
        session([
            'meditation_session' => [
                'type' => $validated['type'],
                'duration' => $validated['duration'],
                'start_time' => now()->timestamp,
                'started' => true
            ]
        ]);

        return response()->json([
            'success' => true,
            'session' => session('meditation_session')
        ]);
    }

    public function completeSession(Request $request)
    {
        $session = session('meditation_session');
        
        if (!$session || !$session['started']) {
            return response()->json([
                'success' => false,
                'message' => 'No active meditation session found'
            ], 400);
        }

        // Calculate actual duration
        $actualDuration = min($session['duration'], intval((now()->timestamp - $session['start_time']) / 60));

        // Process the session using TreeService
        $result = $this->treeService->processMeditationSession(
            Auth::user(),
            $actualDuration,
            $session['type']
        );

        // Clear the session
        session()->forget('meditation_session');

        return response()->json($result);
    }

    public function cancelSession()
    {
        session()->forget('meditation_session');
        
        return response()->json([
            'success' => true,
            'message' => 'Meditation session cancelled'
        ]);
    }

    public function getSessionStatus()
    {
        $session = session('meditation_session');
        
        if (!$session || !$session['started']) {
            return response()->json([
                'active' => false,
                'session' => null
            ]);
        }

        $elapsed = now()->timestamp - $session['start_time'];
        $remaining = max(0, ($session['duration'] * 60) - $elapsed);

        return response()->json([
            'active' => true,
            'session' => [
                'type' => $session['type'],
                'duration' => $session['duration'],
                'elapsed' => $elapsed,
                'remaining' => $remaining,
                'progress' => (($session['duration'] * 60 - $remaining) / ($session['duration'] * 60)) * 100
            ]
        ]);
    }
}
