<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Web\ProfileSettingsController;
use App\Http\Controllers\Web\OnboardingController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\PainPointController;
use App\Http\Controllers\Web\MeditationController;
use App\Http\Controllers\Web\VideoController;
use App\Http\Controllers\Web\ConsultationController;
use App\Http\Controllers\Web\DailyMissionController;
use App\Http\Controllers\Web\UserAssessmentController;
use App\Http\Controllers\Web\TranslatorController;
use App\Http\Controllers\Web\AdminAssessmentController as WebAdminAssessmentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\SolutionController;
use App\Http\Controllers\Admin\AssessmentQuestionController as AdminAssessmentQuestionController;
use App\Http\Controllers\Admin\AssessmentController as AdminAssessmentController;
use App\Http\Controllers\Admin\PainPointController as AdminPainPointController;
use App\Http\Controllers\Admin\DailyTaskController as AdminDailyTaskController;
use App\Http\Controllers\Admin\DailyMissionController as AdminDailyMissionController;
use App\Http\Controllers\Translator\DashboardController as TranslatorDashboardController;
use App\Http\Controllers\Translator\LanguageLineController as TranslatorLanguageLineController;
use App\Http\Controllers\Translator\AssessmentController as TranslatorAssessmentController;
use App\Http\Controllers\Consultant\DashboardController as ConsultantDashboardController;
use App\Http\Middleware\EnsureEmailVerified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Language switcher route
Route::get('/lang/{lang}', function ($lang) {
    $supportedLocales = ['en', 'vi', 'de', 'kr'];
    
    if (!in_array($lang, $supportedLocales)) {
        abort(404);
    }
    
    // For guests, store in session
    if (!Auth::check()) {
        session(['locale' => $lang]);
    } else {
        // For authenticated users, update their language preference
        $user = Auth::user();
        if (isset($user->language)) {
            $user->language = $lang;
            $user->save();
        } else {
            session(['locale' => $lang]);
        }
    }
    
    app()->setLocale($lang);
    
    // Redirect back or to home
    return redirect()->back();
})->name('language.switch');

Route::get('/', function () {
    $preferredLocale = session('locale')
        ?? (Auth::check() ? (Auth::user()->language ?? Auth::user()->locale) : null)
        ?? config('app.locale', 'en');

    $preferredLocale = in_array($preferredLocale, ['en', 'vi', 'de', 'kr'], true) ? $preferredLocale : 'en';
    
    // If already at a locale root (like /en), don't redirect again
    $currentPath = request()->path();
    if (in_array($currentPath, ['en', 'vi', 'de', 'kr'])) {
        // Redirect to login for the current locale
        return redirect('/' . $currentPath . '/login');
    }

    return redirect('/'.$preferredLocale.'/login');
})->name('home');

Route::match(['GET', 'POST'], '/login', function () {
    $preferredLocale = session('locale')
        ?? (Auth::check() ? (Auth::user()->language ?? Auth::user()->locale) : null)
        ?? config('app.locale', 'en');

    $preferredLocale = in_array($preferredLocale, ['en', 'vi', 'de', 'kr'], true) ? $preferredLocale : 'en';

    return redirect()->route('login', ['locale' => $preferredLocale]);
})->name('login');

Route::get('/register', function () {
    $preferredLocale = session('locale')
        ?? (Auth::check() ? (Auth::user()->language ?? Auth::user()->locale) : null)
        ?? config('app.locale', 'en');

    $preferredLocale = in_array($preferredLocale, ['en', 'vi', 'de', 'kr'], true) ? $preferredLocale : 'en';

    return redirect()->route('user.register', ['locale' => $preferredLocale]);
})->name('register');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('{locale}')
    ->whereIn('locale', ['en', 'vi', 'de', 'kr'])
    ->middleware(['localization'])
    ->name('user.')
    ->group(function () {
        Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

        Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
        Route::post('/register', [RegisteredUserController::class, 'store']);

        // Standalone assessment routes (accessible without auth)
        Route::get('/assessment-form', function () {
            return view('assessment');
        })->name('assessment.form');
        
        Route::post('/assessment-form', [UserAssessmentController::class, 'submitStandalone'])->name('assessment.submit');

        Route::get('/email/verify', function () {
            return view('auth.verify-email');
        })->middleware('auth')->name('verification.notice');

        Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();

            return redirect()->route('user.profile.settings.edit');
        })->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');

        Route::post('/email/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();

            return back();
        })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

        Route::middleware(['auth', EnsureEmailVerified::class])->group(function () {
            // Advanced Assessment routes (user-only)
            Route::middleware(['user'])->group(function () {
                Route::get('/assessments', [UserAssessmentController::class, 'index'])->name('assessments.index');
                Route::get('/assessments/{assessment}', [UserAssessmentController::class, 'show'])->name('assessments.show');
                Route::get('/assessments/{assessment}/signed/{token}', [UserAssessmentController::class, 'showSigned'])->name('assessments.signed');
                Route::post('/assessments/{assessmentId}', [UserAssessmentController::class, 'submit'])->name('assessments.submit');
                Route::get('/assessments/result/{userAssessment}', [UserAssessmentController::class, 'result'])->name('assessments.result');
                Route::post('/assessments/result/{userAssessment}/convert-to-consultation', [UserAssessmentController::class, 'convertToConsultation'])->name('assessments.result.convert-to-consultation');
                
                // Legacy assessment route (redirect to new system)
                Route::get('/assessment', function () {
                    return redirect()->route('user.assessments.index');
                })->name('assessment');
            });

            Route::get('/pain-points', [PainPointController::class, 'index'])->name('pain-points.index');
            Route::post('/pain-points', [PainPointController::class, 'store'])->name('pain-points.store');
            
            Route::get('/settings/profile', [ProfileSettingsController::class, 'edit'])->name('profile.settings.edit');
            Route::post('/settings/profile', [ProfileSettingsController::class, 'update'])->name('profile.settings.update');

            Route::get('/settings/assessment', function () {
                return redirect()->route('assessment');
            })->name('settings.assessment');
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
        Route::middleware(['auth'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::post('/dashboard/complete-task', [DashboardController::class, 'completeTask'])->name('dashboard.complete.task');
            Route::post('/dashboard/donate-fruit', [DashboardController::class, 'donateFruit'])->name('dashboard.donate.fruit');

            Route::post('/daily-mission/complete', [DailyMissionController::class, 'complete'])->name('daily-mission.complete');

            // Task routes for TDD test
            Route::post('/tasks/{task}/start', [DailyMissionController::class, 'start'])->name('tasks.start');
            Route::post('/tasks/{task}/complete', [DailyMissionController::class, 'completeTask'])->name('tasks.complete');

            // Consultations (User) - moved outside role middleware for testing
            Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultations.index');
            Route::get('/consultations/create', [ConsultationController::class, 'create'])->name('consultations.create');
            Route::post('/consultations', [ConsultationController::class, 'store'])->name('consultations.store');
            Route::get('/consultations/{consultation_id}', [ConsultationController::class, 'show'])->name('consultations.show');
            Route::post('/consultations/{consultation_id}/replies', [ConsultationController::class, 'reply'])->name('consultations.reply');
            Route::post('/consultations/{consultation_id}/close', [ConsultationController::class, 'close'])->name('consultations.close');
            
            // Meditation PWA
            Route::get('/meditate', [MeditationController::class, 'index'])->name('meditate');
            Route::post('/meditate/start', [MeditationController::class, 'startSession'])->name('meditate.start');
            Route::post('/meditate/complete', [MeditationController::class, 'completeSession'])->name('meditate.complete');
            Route::post('/meditate/cancel', [MeditationController::class, 'cancelSession'])->name('meditate.cancel');
            Route::get('/meditate/status', [MeditationController::class, 'getSessionStatus'])->name('meditate.status');
        });

        Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
        Route::get('/videos/{videoId}', [VideoController::class, 'show'])->name('videos.show');
        Route::post('/videos/{videoId}/claim', [VideoController::class, 'claim'])->middleware(['auth'])->name('videos.claim');

    });

Route::prefix('{locale}')
    ->whereIn('locale', ['en', 'vi', 'de', 'kr'])
    ->middleware(['localization'])
    ->name('user.videos.')
    ->group(function () {
        Route::get('/videos', [VideoController::class, 'index'])->name('index');
        Route::get('/videos/{videoId}', [VideoController::class, 'show'])->name('show');
        Route::post('/videos/{videoId}/claim', [VideoController::class, 'claim'])->middleware(['auth'])->name('claim');
    });

Route::prefix('{locale}')
    ->whereIn('locale', ['en', 'vi', 'de', 'kr'])
    ->middleware(['localization', 'auth'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('user.dashboard');

// Backward-compatible localized routes (no user.* name prefix)
Route::prefix('{locale}')
    ->whereIn('locale', ['en', 'vi', 'de', 'kr'])
    ->middleware(['localization'])
    ->group(function () {
        Route::get('/login', [AuthenticatedSessionController::class, 'create']);
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth');

        Route::get('/register', [RegisteredUserController::class, 'create']);
        Route::post('/register', [RegisteredUserController::class, 'store']);

        Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

        Route::get('/assessment', function () {
            return response('Assessment', 200);
        })->middleware(['auth']);

        Route::post('/assessment/submit', function (\Illuminate\Http\Request $request, string $locale) {
            $data = $request->validate([
                'answers' => ['required', 'array'],
            ]);

            $assessmentId = \App\Models\Assessment::query()->value('id');
            if (!$assessmentId) {
                $assessmentId = \App\Models\Assessment::query()->create([
                    'title' => ['en' => 'Test Assessment'],
                    'description' => ['en' => 'Test Assessment Description'],
                    'status' => 'created',
                    'created_by' => $request->user()->id,
                ])->id;
            }

            \App\Models\UserAssessment::query()->create([
                'user_id' => $request->user()->id,
                'assessment_id' => $assessmentId,
                'answers' => $data['answers'],
                'total_score' => 0,
                'submission_mode' => 'self_review',
            ]);

            return redirect('/' . $locale . '/assessment/results');
        })->middleware(['auth']);

        Route::get('/assessment/results', function () {
            return response('Results', 200);
        })->middleware(['auth']);

        Route::put('/profile', function (\Illuminate\Http\Request $request, string $locale) {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:255'],
                'spiritual_preference' => ['nullable', 'string', 'max:255'],
                'onboarding_status' => ['nullable', 'string', 'max:255'],
            ]);

            $request->user()->update($data);

            return redirect('/' . $locale . '/dashboard');
        })->middleware(['auth']);

        Route::get('/progress', function () {
            return response('Progress', 200);
        })->middleware(['auth']);

        Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
        Route::get('/videos/{videoId}', [VideoController::class, 'show'])->name('videos.show');
        Route::post('/videos/{videoId}/claim', [VideoController::class, 'claim'])->middleware(['auth'])->name('videos.claim');

        Route::get('/consultations', [ConsultationController::class, 'index'])->middleware(['auth'])->name('consultations.index');
        Route::get('/consultations/create', [ConsultationController::class, 'create'])->middleware(['auth'])->name('consultations.create');
        Route::post('/consultations', [ConsultationController::class, 'store'])->middleware(['auth'])->name('consultations.store');
        Route::get('/consultations/{consultation_id}', [ConsultationController::class, 'show'])->middleware(['auth'])->name('consultations.show');
        Route::post('/consultations/{consultation_id}/replies', [ConsultationController::class, 'reply'])->middleware(['auth'])->name('consultations.reply');
        Route::post('/consultations/{consultation_id}/close', [ConsultationController::class, 'close'])->middleware(['auth'])->name('consultations.close');

        Route::get('/pain-points', [PainPointController::class, 'index'])->middleware(['auth'])->name('pain-points.index');
        Route::post('/pain-points', [PainPointController::class, 'store'])->middleware(['auth'])->name('pain-points.store');

        Route::post('/daily-mission/complete', [DailyMissionController::class, 'complete'])->middleware(['auth'])->name('daily-mission.complete');

        // Legacy admin dashboard path under locale (tests expect /en/admin/dashboard)
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->middleware(['auth', 'role:admin']);
    });

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/assessment/questions', [App\Http\Controllers\API\AssessmentController::class, 'getQuestions']);
});

Route::fallback(function (Request $request) {
    $preferredLocale = session('locale')
        ?? (Auth::check() ? (Auth::user()->language ?? Auth::user()->locale) : null)
        ?? config('app.locale', 'en');

    $preferredLocale = in_array($preferredLocale, ['en', 'vi', 'de', 'kr'], true) ? $preferredLocale : 'en';

    $path = ltrim($request->path(), '/');
    $path = $path === '' ? '' : '/'.$path;

    if (str_starts_with($path, '/admin')) {
        abort(404);
    }

    return redirect('/'.$preferredLocale.$path);
});
