<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Web\ProfileSettingsController;
use App\Http\Controllers\Web\OnboardingController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MeditationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\SolutionController;
use App\Http\Controllers\Translator\DashboardController as TranslatorDashboardController;
use App\Http\Controllers\Translator\AppTranslationsController as TranslatorAppTranslationsController;
use App\Http\Controllers\Translator\UserController as TranslatorUserController;
use App\Http\Controllers\Translator\TranslationController as TranslatorTranslationController;
use App\Http\Middleware\EnsureEmailVerified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Language switching routes
Route::get('/language/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'vi'])) {
        abort(404);
    }
    
    session(['locale' => $locale]);
    app()->setLocale($locale);
    
    // Save to User DB if authenticated
    if (Auth::check()) {
        $user = Auth::user();
        $user->locale = $locale;
        $user->save();
    }
    
    return redirect()->back();
})->name('language.switch');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('profile.settings.edit');
})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back();
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware(['auth', EnsureEmailVerified::class])->group(function () {
    // Assessment routes (user-only)
    Route::middleware(['user'])->group(function () {
        Route::get('/assessment', function () {
            return view('assessment');
        })->name('assessment');

        Route::post('/assessment/submit', function (Request $request) {
            $validated = $request->validate([
                'answers' => 'required|array',
                'answers.*' => 'required|integer|min:1|max:5',
            ]);

            $answers = $validated['answers'];

            $heartScore = 0;
            $gritScore = 0;
            $wisdomScore = 0;

            foreach ($answers as $questionId => $score) {
                $question = \App\Models\AssessmentQuestion::select(['id', 'pillar_group'])->find($questionId);
                if (!$question) {
                    continue;
                }

                switch ($question->pillar_group) {
                    case 'heart':
                        $heartScore += (int) $score;
                        break;
                    case 'grit':
                        $gritScore += (int) $score;
                        break;
                    case 'wisdom':
                        $wisdomScore += (int) $score;
                        break;
                }
            }

            $scores = [
                'heart' => $heartScore,
                'grit' => $gritScore,
                'wisdom' => $wisdomScore,
            ];
            $dominantIssue = array_keys($scores, min($scores))[0];

            $user = Auth::user();

            // Upsert quiz result so retakes update the stored score
            \App\Models\UserQuizResult::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'heart_score' => $heartScore,
                    'grit_score' => $gritScore,
                    'wisdom_score' => $wisdomScore,
                    'dominant_issue' => $dominantIssue,
                ]
            );

            // Mark assessment complete for onboarding redirect logic
            if ($user->onboarding_status !== 'test_completed') {
                $user->onboarding_status = 'test_completed';
                $user->save();
            }

            // Ensure user journey exists
            \App\Models\UserJourney::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'current_day' => 1,
                    'last_activity_at' => now(),
                ]
            );

            // Ensure user tree exists and update health based on scores
            $userTree = $user->userTree;
            if (!$userTree) {
                $userTree = \App\Models\UserTree::create([
                    'user_id' => $user->id,
                    'season' => 'spring',
                    'health' => 50,
                    'exp' => 0,
                    'fruits_balance' => 0,
                    'total_fruits_given' => 0,
                ]);
            }

            $totalScore = $heartScore + $gritScore + $wisdomScore;
            $healthPercentage = ($totalScore / 150) * 100;
            $userTree->health = max(20, min(100, $healthPercentage));
            $userTree->save();

            return redirect()->route('dashboard')->with('success', 'Đánh giá hoàn thành! Chào mừng đến với hành trình chữa lành.');
        })->name('assessment.submit');
    });
    
    Route::get('/settings/profile', [ProfileSettingsController::class, 'edit'])->name('profile.settings.edit');
    Route::post('/settings/profile', [ProfileSettingsController::class, 'update'])->name('profile.settings.update');
});

// Onboarding Routes
Route::middleware('guest')->group(function () {
    Route::get('/onboarding/step1', [OnboardingController::class, 'step1'])->name('onboarding.step1');
    Route::post('/onboarding/step1', [OnboardingController::class, 'submitStep1'])->name('onboarding.step1.submit');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding/step2', [OnboardingController::class, 'step2'])->name('onboarding.step2');
    Route::post('/onboarding/step2', [OnboardingController::class, 'submitStep2'])->name('onboarding.step2.submit');
    Route::get('/onboarding/step3', [OnboardingController::class, 'step3'])->name('onboarding.step3');
    Route::post('/onboarding/complete', [OnboardingController::class, 'completeOnboarding'])->name('onboarding.complete');
});

// User Frontend Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/complete-task', [DashboardController::class, 'completeTask'])->name('dashboard.complete.task');
    Route::post('/dashboard/donate-fruit', [DashboardController::class, 'donateFruit'])->name('dashboard.donate.fruit');
    
    // Meditation PWA
    Route::get('/meditate', [MeditationController::class, 'index'])->name('meditate');
});

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/assessment/questions', [App\Http\Controllers\API\AssessmentController::class, 'getQuestions']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/meditate/start', [MeditationController::class, 'startSession'])->name('meditate.start');
    Route::post('/meditate/complete', [MeditationController::class, 'completeSession'])->name('meditate.complete');
    Route::post('/meditate/cancel', [MeditationController::class, 'cancelSession'])->name('meditate.cancel');
    Route::get('/meditate/status', [MeditationController::class, 'getSessionStatus'])->name('meditate.status');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/reset-assessment', [UserController::class, 'resetAssessment'])->name('users.reset-assessment');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Languages Management
    Route::get('/languages', [LanguageController::class, 'index'])->name('languages.index');
    Route::get('/languages/create', [LanguageController::class, 'create'])->name('languages.create');
    Route::post('/languages', [LanguageController::class, 'store'])->name('languages.store');
    Route::get('/languages/{language}/edit', [LanguageController::class, 'edit'])->name('languages.edit');
    Route::put('/languages/{language}', [LanguageController::class, 'update'])->name('languages.update');
    Route::delete('/languages/{language}', [LanguageController::class, 'destroy'])->name('languages.destroy');
    Route::patch('/languages/{language}/toggle', [LanguageController::class, 'toggleStatus'])->name('languages.toggle');
    
    // Solutions Management
    Route::get('/solutions', [SolutionController::class, 'index'])->name('solutions.index');
    Route::get('/solutions/create', [SolutionController::class, 'create'])->name('solutions.create');
    Route::post('/solutions', [SolutionController::class, 'store'])->name('solutions.store');
    Route::get('/solutions/{solution}', [SolutionController::class, 'show'])->name('solutions.show');
    Route::get('/solutions/{solution}/edit', [SolutionController::class, 'edit'])->name('solutions.edit');
    Route::put('/solutions/{solution}', [SolutionController::class, 'update'])->name('solutions.update');
    Route::delete('/solutions/{solution}', [SolutionController::class, 'destroy'])->name('solutions.destroy');
});

// Translator Routes
Route::middleware(['auth', 'translator'])->prefix('translator')->name('translator.')->group(function () {
    Route::get('/dashboard', [TranslatorDashboardController::class, 'index'])->name('dashboard');

    // Translation Review
    Route::get('/translations', [TranslatorTranslationController::class, 'index'])->name('translations.index');
    Route::get('/translations/{translation}/review', [TranslatorTranslationController::class, 'review'])->name('translations.review');
    Route::post('/translations/{translation}/approve', [TranslatorTranslationController::class, 'approve'])->name('translations.approve');
    Route::post('/translations/{translation}/reject', [TranslatorTranslationController::class, 'reject'])->name('translations.reject');

    // App Translations
    Route::get('/app-translations', [TranslatorAppTranslationsController::class, 'index'])->name('app-translations.index');
    Route::get('/app-translations/download', [TranslatorAppTranslationsController::class, 'download'])->name('app-translations.download');
    Route::post('/app-translations/upload', [TranslatorAppTranslationsController::class, 'upload'])->name('app-translations.upload');

    // Users management (user + translator)
    Route::get('/users', [TranslatorUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [TranslatorUserController::class, 'create'])->name('users.create');
    Route::post('/users', [TranslatorUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [TranslatorUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [TranslatorUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [TranslatorUserController::class, 'destroy'])->name('users.destroy');
});
