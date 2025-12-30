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

    return redirect('/'.$preferredLocale);
})->name('home');

Route::prefix('{locale}')
    ->whereIn('locale', ['en', 'vi', 'de', 'kr'])
    ->group(function () {
        Route::get('/', function () {
            return view('welcome');
        })->name('home.localized');

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
            // Advanced Assessment routes (user-only)
            Route::middleware(['user'])->group(function () {
                Route::get('/assessments', [UserAssessmentController::class, 'index'])->name('assessments.index');
                Route::get('/assessments/{id}', [UserAssessmentController::class, 'show'])->name('assessments.show');
                Route::get('/assessments/{assessment}/signed/{token}', [UserAssessmentController::class, 'showSigned'])->name('assessments.signed');
                Route::post('/assessments/{assessmentId}', [UserAssessmentController::class, 'submit'])->name('assessments.submit');
                Route::get('/assessments/result/{userAssessment}', [UserAssessmentController::class, 'result'])->name('assessments.result');
                Route::post('/assessments/result/{userAssessment}/convert-to-consultation', [UserAssessmentController::class, 'convertToConsultation'])->name('assessments.result.convert-to-consultation');
                
                // Legacy assessment route (redirect to new system)
                Route::get('/assessment', function () {
                    return redirect()->route('assessments.index');
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

            Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
            Route::get('/videos/{videoId}', [VideoController::class, 'show'])->name('videos.show');
            Route::post('/videos/{videoId}/claim', [VideoController::class, 'claim'])->name('videos.claim');

            // Consultations (User)
            Route::middleware(['role:user'])->group(function () {
                Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultations.index');
                Route::get('/consultations/create', [ConsultationController::class, 'create'])->name('consultations.create');
                Route::post('/consultations', [ConsultationController::class, 'store'])->name('consultations.store');
                Route::get('/consultations/{thread}', [ConsultationController::class, 'show'])->name('consultations.show');
                Route::post('/consultations/{thread}/replies', [ConsultationController::class, 'reply'])->name('consultations.reply');
            });
            
            // Meditation PWA
            Route::get('/meditate', [MeditationController::class, 'index'])->name('meditate');
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
            Route::patch('/users/{user}/verify', [UserController::class, 'verify'])->name('users.verify');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/reset-assessment', [UserController::class, 'resetAssessment'])->name('users.reset-assessment');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

            // Videos Management
            Route::get('/videos', [AdminVideoController::class, 'index'])->name('videos.index');
            Route::get('/videos/create', [AdminVideoController::class, 'create'])->name('videos.create');
            Route::post('/videos', [AdminVideoController::class, 'store'])->name('videos.store');
            Route::get('/videos/{videoId}/edit', [AdminVideoController::class, 'edit'])->name('videos.edit');
            Route::put('/videos/{videoId}', [AdminVideoController::class, 'update'])->name('videos.update');
            Route::delete('/videos/{videoId}', [AdminVideoController::class, 'destroy'])->name('videos.destroy');
            
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

            Route::get('/assessment-questions', [AdminAssessmentQuestionController::class, 'index'])->name('assessment-questions.index');
            Route::get('/assessment-questions/create', [AdminAssessmentQuestionController::class, 'create'])->name('assessment-questions.create');
            Route::post('/assessment-questions', [AdminAssessmentQuestionController::class, 'store'])->name('assessment-questions.store');
            Route::get('/assessment-questions/{assessmentQuestion}/edit', [AdminAssessmentQuestionController::class, 'edit'])->name('assessment-questions.edit');
            Route::put('/assessment-questions/{assessmentQuestion}', [AdminAssessmentQuestionController::class, 'update'])->name('assessment-questions.update');
            Route::delete('/assessment-questions/{assessmentQuestion}', [AdminAssessmentQuestionController::class, 'destroy'])->name('assessment-questions.destroy');

            Route::get('/pain-points', [AdminPainPointController::class, 'index'])->name('pain-points.index');

            Route::get('/daily-tasks', [AdminDailyTaskController::class, 'index'])->name('daily-tasks.index');
            Route::get('/daily-tasks/create', [AdminDailyTaskController::class, 'create'])->name('daily-tasks.create');
            Route::post('/daily-tasks', [AdminDailyTaskController::class, 'store'])->name('daily-tasks.store');
            Route::get('/daily-tasks/{dailyTask}/edit', [AdminDailyTaskController::class, 'edit'])->name('daily-tasks.edit');
            Route::put('/daily-tasks/{dailyTask}', [AdminDailyTaskController::class, 'update'])->name('daily-tasks.update');
            Route::delete('/daily-tasks/{dailyTask}', [AdminDailyTaskController::class, 'destroy'])->name('daily-tasks.destroy');

            // Advanced Assessments Management
            Route::prefix('assessments')->name('assessments.')->group(function () {
                Route::get('/', [WebAdminAssessmentController::class, 'index'])->name('index');
                Route::get('/create', [WebAdminAssessmentController::class, 'create'])->name('create');
                Route::post('/', [WebAdminAssessmentController::class, 'store'])->name('store');
                Route::get('/{assessment}/edit', [WebAdminAssessmentController::class, 'edit'])->name('edit');
                Route::put('/{assessment}', [WebAdminAssessmentController::class, 'update'])->name('update');
                Route::delete('/{assessment}', [WebAdminAssessmentController::class, 'destroy'])->name('destroy');
                Route::get('/{assessment}', [WebAdminAssessmentController::class, 'show'])->name('show');
                Route::post('/{assessment}/request-translation', [WebAdminAssessmentController::class, 'requestTranslation'])->name('request-translation');
                Route::patch('/{id}/publish', [WebAdminAssessmentController::class, 'publish'])->name('publish');
                Route::post('/{assessment}/mark-special', [WebAdminAssessmentController::class, 'markSpecial'])->name('mark-special');
            });
        });

        // Translator Routes
        Route::middleware(['auth', 'role:translator'])->prefix('translator')->name('translator.')->group(function () {
            Route::get('/dashboard', [TranslatorController::class, 'index'])->name('dashboard');
            Route::get('/language-lines', [TranslatorLanguageLineController::class, 'index'])->name('language-lines.index');
            Route::post('/language-lines/{languageLine}', [TranslatorLanguageLineController::class, 'update'])->name('language-lines.update');
            
            // Advanced Assessments Translation
            Route::get('/assessments', [TranslatorAssessmentController::class, 'index'])->name('assessments.index');
            Route::get('/assessments/{assessment}/translate', [TranslatorAssessmentController::class, 'translate'])->name('assessments.translate');
            Route::post('/assessments/{assessment}/submit-translation', [TranslatorAssessmentController::class, 'submitTranslation'])->name('assessments.submit-translation');
        });

        // Consultant Routes
        Route::middleware(['auth', 'role:consultant'])->prefix('consultant')->name('consultant.')->group(function () {
            Route::get('/dashboard', [ConsultantDashboardController::class, 'index'])->name('dashboard');
            Route::get('/threads/{thread}', [ConsultantDashboardController::class, 'show'])->name('threads.show');
            Route::post('/threads/{thread}/replies', [ConsultantDashboardController::class, 'reply'])->name('threads.reply');
            Route::post('/threads/{thread}/assign-assessment', [ConsultantDashboardController::class, 'assignAssessment'])->name('threads.assign-assessment');
            Route::get('/assessments/available', [ConsultantDashboardController::class, 'getAvailableAssessments'])->name('assessments.available');
        });
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

    return redirect('/'.$preferredLocale.$path);
});
