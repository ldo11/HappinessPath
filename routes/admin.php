<?php

use App\Http\Controllers\Admin\AssessmentController as AdminAssessmentController;
use App\Http\Controllers\Admin\AssessmentQuestionController as AdminAssessmentQuestionController;
use App\Http\Controllers\Admin\DailyMissionController as AdminDailyMissionController;
use App\Http\Controllers\Admin\DailyTaskController as AdminDailyTaskController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\PainPointController as AdminPainPointController;
use App\Http\Controllers\Admin\SolutionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['web', 'auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}/verify', [UserController::class, 'verify'])->name('users.verify');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/reset-assessment', [UserController::class, 'resetAssessment'])->name('users.reset-assessment');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/languages', [LanguageController::class, 'index'])->name('languages.index');
        Route::get('/languages/create', [LanguageController::class, 'create'])->name('languages.create');
        Route::post('/languages', [LanguageController::class, 'store'])->name('languages.store');
        Route::get('/languages/{language}/edit', [LanguageController::class, 'edit'])->name('languages.edit');
        Route::put('/languages/{language}', [LanguageController::class, 'update'])->name('languages.update');
        Route::delete('/languages/{language}', [LanguageController::class, 'destroy'])->name('languages.destroy');
        Route::patch('/languages/{language}/toggle', [LanguageController::class, 'toggleStatus'])->name('languages.toggle');

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

        Route::get('/videos', [AdminVideoController::class, 'index'])->name('videos.index');
        Route::get('/videos/create', [AdminVideoController::class, 'create'])->name('videos.create');
        Route::post('/videos', [AdminVideoController::class, 'store'])->name('videos.store');
        Route::get('/videos/{video}/edit', [AdminVideoController::class, 'edit'])->name('videos.edit');
        Route::put('/videos/{video}', [AdminVideoController::class, 'update'])->name('videos.update');
        Route::delete('/videos/{video}', [AdminVideoController::class, 'destroy'])->name('videos.destroy');

        Route::prefix('assessments')->name('assessments.')->group(function () {
            Route::get('/', [AdminAssessmentController::class, 'index'])->name('index');
            Route::post('/import-json', [AdminAssessmentController::class, 'importJson'])->name('import-json');
            Route::get('/create', [AdminAssessmentController::class, 'create'])->name('create');
            Route::post('/', [AdminAssessmentController::class, 'store'])->name('store');
            Route::get('/{assessment}', [AdminAssessmentController::class, 'show'])->name('show');
            Route::get('/{assessment}/export-json', [AdminAssessmentController::class, 'exportJson'])->name('export-json');
            Route::get('/{assessment}/edit', [AdminAssessmentController::class, 'edit'])->name('edit');
            Route::put('/{assessment}', [AdminAssessmentController::class, 'update'])->name('update');
            Route::delete('/{assessment}', [AdminAssessmentController::class, 'destroy'])->name('destroy');

            Route::post('/{assessment}/questions', [AdminAssessmentController::class, 'storeQuestion'])->name('questions.store');
            Route::get('/{assessment}/questions/{question}/edit', [AdminAssessmentController::class, 'editQuestion'])->name('questions.edit');
            Route::put('/{assessment}/questions/{question}', [AdminAssessmentController::class, 'updateQuestion'])->name('questions.update');
            Route::delete('/{assessment}/questions/{question}', [AdminAssessmentController::class, 'destroyQuestion'])->name('questions.destroy');
        });

        Route::get('/daily-missions', [AdminDailyMissionController::class, 'index'])->name('daily-missions.index');
        Route::get('/daily-missions/create', [AdminDailyMissionController::class, 'create'])->name('daily-missions.create');
        Route::post('/daily-missions', [AdminDailyMissionController::class, 'store'])->name('daily-missions.store');
        Route::get('/daily-missions/{dailyMission}/edit', [AdminDailyMissionController::class, 'edit'])->name('daily-missions.edit');
        Route::put('/daily-missions/{dailyMission}', [AdminDailyMissionController::class, 'update'])->name('daily-missions.update');
        Route::delete('/daily-missions/{dailyMission}', [AdminDailyMissionController::class, 'destroy'])->name('daily-missions.destroy');

        Route::prefix('mission-sets')->name('mission-sets.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\MissionSetController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\MissionSetController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\MissionSetController::class, 'store'])->name('store');
            Route::get('/{missionSet}', [\App\Http\Controllers\Admin\MissionSetController::class, 'show'])->name('show');
            Route::get('/{missionSet}/edit', [\App\Http\Controllers\Admin\MissionSetController::class, 'edit'])->name('edit');
            Route::put('/{missionSet}', [\App\Http\Controllers\Admin\MissionSetController::class, 'update'])->name('update');
            Route::delete('/{missionSet}', [\App\Http\Controllers\Admin\MissionSetController::class, 'destroy'])->name('destroy');
        });
    });
