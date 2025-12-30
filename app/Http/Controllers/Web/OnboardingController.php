<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AssessmentQuestion;
use App\Models\UserQuizResult;
use App\Models\UserTree;
use App\Models\UserJourney;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    protected $middleware = [
        'guest' => ['except' => ['assessment', 'submitAssessment', 'results']],
        'auth' => ['only' => ['assessment', 'submitAssessment', 'results']]
    ];

    public function step1()
    {
        return view('onboarding.step1');
    }

    public function submitStep1(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'city' => 'required|string|max:255',
            'spiritual_preference' => 'required|in:buddhism,christianity,secular',
            'start_pain_level' => 'required|integer|min:1|max:10',
            'geo_privacy' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'city' => $validated['city'],
                'spiritual_preference' => $validated['spiritual_preference'],
                'start_pain_level' => $validated['start_pain_level'],
                'geo_privacy' => $validated['geo_privacy'] ?? false,
                'email_verified_at' => now(),
                'onboarding_completed' => false,
            ]);

            // Initialize user tree
            UserTree::create([
                'user_id' => $user->id,
                'season' => 1,
                'health' => 20, // Start with withered tree
                'exp' => 0,
                'fruits_balance' => 0,
                'total_fruits_given' => 0,
            ]);

            // Initialize user journey
            UserJourney::create([
                'user_id' => $user->id,
                'current_day' => 1,
                'custom_focus' => null,
                'last_activity_at' => now(),
            ]);

            DB::commit();

            auth()->login($user);

            return redirect()->route('onboarding.step2')
                ->with('success', 'Welcome! Let\'s get to know you better.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Onboarding step 1 failed', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function step2()
    {
        if (!auth()->check() || auth()->user()->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        // Get 30 assessment questions (10 per pillar)
        $questions = AssessmentQuestion::with('answers')
            ->inRandomOrder()
            ->get()
            ->groupBy('pillar_group');

        // Ensure we have questions for each pillar
        $heartQuestions = $questions->get('heart', collect())->take(10);
        $gritQuestions = $questions->get('grit', collect())->take(10);
        $wisdomQuestions = $questions->get('wisdom', collect())->take(10);

        // If we don't have enough questions, create some defaults
        if ($heartQuestions->count() < 10 || $gritQuestions->count() < 10 || $wisdomQuestions->count() < 10) {
            $this->createDefaultQuestions();
            return redirect()->route('onboarding.step2');
        }

        $allQuestions = $heartQuestions->merge($gritQuestions)->merge($wisdomQuestions);

        return view('onboarding.step2', compact('allQuestions'));
    }

    public function submitStep2(Request $request)
    {
        if (!auth()->check() || auth()->user()->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer|exists:assessment_answers,id',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $answers = $validated['answers'];

            $inputs = [];
            foreach ($answers as $answerId) {
                $answer = \App\Models\AssessmentAnswer::query()->select(['id', 'question_id', 'score'])->find($answerId);
                if (!$answer) {
                    continue;
                }

                $inputs[(string) $answer->question_id] = (int) $answer->score;
            }

            /** @var \App\Services\AssessmentService $service */
            $service = app(\App\Services\AssessmentService::class);
            $scoreResult = $service->calculateScoreAndSyncPainPoints($user, $inputs);

            $heartScore = (int) ($scoreResult['heart_score'] ?? 0);
            $gritScore = (int) ($scoreResult['grit_score'] ?? 0);
            $wisdomScore = (int) ($scoreResult['wisdom_score'] ?? 0);
            $dominantIssue = (string) ($scoreResult['custom_focus'] ?? 'heart');

            // Save quiz results
            UserQuizResult::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'heart_score' => $heartScore,
                    'grit_score' => $gritScore,
                    'wisdom_score' => $wisdomScore,
                    'dominant_issue' => $dominantIssue,
                ]
            );

            // Update user tree based on scores
            $totalScore = $heartScore + $gritScore + $wisdomScore;
            $healthPercentage = ($totalScore / 150) * 100; // Max possible score is 150 (50 per pillar)
            
            $userJourney = $user->userJourney;
            if (!$userJourney) {
                $userJourney = \App\Models\UserJourney::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'current_day' => 1,
                        'last_activity_at' => now(),
                    ]
                );
            }

            $userTree = $user->userTree;
            if (!$userTree) {
                $userTree = \App\Models\UserTree::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'season' => 'spring',
                        'health' => 50,
                        'exp' => 0,
                        'fruits_balance' => 0,
                        'total_fruits_given' => 0,
                    ]
                );
            }

            $userTree->health = max(20, min(100, $healthPercentage)); // Between 20-100
            $userTree->save();

            DB::commit();

            return redirect()->route('onboarding.step3');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Onboarding step 2 failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return back()->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function step3()
    {
        if (!auth()->check() || auth()->user()->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        $user = auth()->user();
        $userTree = $user->userTree;
        $quizResult = $user->quizResult;

        // Determine tree status
        if ($userTree->health >= 80) {
            $treeStatus = 'thriving';
            $treeIcon = 'fa-tree';
            $treeColor = 'text-green-600';
            $treeMessage = 'Your tree is thriving! Keep up the great work.';
        } elseif ($userTree->health >= 50) {
            $treeStatus = 'growing';
            $treeIcon = 'fa-seedling';
            $treeColor = 'text-green-500';
            $treeMessage = 'Your tree is growing well. With daily care, it will thrive!';
        } else {
            $treeStatus = 'withered';
            $treeIcon = 'fa-tree';
            $treeColor = 'text-yellow-600';
            $treeMessage = 'Your tree needs care. Don\'t worry - we\'ll help you nurture it back to health.';
        }

        return view('onboarding.step3', compact('user', 'userTree', 'quizResult', 'treeStatus', 'treeIcon', 'treeColor', 'treeMessage'));
    }

    public function completeOnboarding()
    {
        if (!auth()->check() || auth()->user()->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        auth()->user()->update(['onboarding_completed' => true]);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome to your Happiness Path journey!');
    }

    private function createDefaultQuestions()
    {
        // This would create default assessment questions if they don't exist
        // For now, we'll assume they exist in the database
        Log::info('Default assessment questions would be created here');
    }
}
