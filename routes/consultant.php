<?php

use App\Http\Controllers\Admin\AssessmentController as AdminAssessmentController;
use App\Http\Controllers\Admin\DailyMissionController as AdminDailyMissionController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Consultant\DashboardController as ConsultantDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('{locale}/consultant')
    ->whereIn('locale', ['en', 'vi', 'de', 'kr'])
    ->middleware(['web', 'auth', 'role:consultant', 'localization'])
    ->name('consultant.')
    ->group(function () {
        Route::get('/dashboard', [ConsultantDashboardController::class, 'index'])->name('dashboard');
        Route::get('/threads/{thread}', [ConsultantDashboardController::class, 'show'])->name('threads.show');
        Route::post('/threads/{thread}/replies', [ConsultantDashboardController::class, 'reply'])->name('threads.reply');
        Route::post('/threads/{thread}/assign-assessment', [ConsultantDashboardController::class, 'assignAssessment'])->name('threads.assign-assessment');
        Route::get('/assessments/available', [ConsultantDashboardController::class, 'getAvailableAssessments'])->name('assessments.available');

        Route::prefix('videos')->name('videos.')->group(function () {
            Route::get('/', [AdminVideoController::class, 'index'])->name('index');
            Route::get('/create', [AdminVideoController::class, 'create'])->name('create');
            Route::post('/', [AdminVideoController::class, 'store'])->name('store');
            Route::get('/{video}/edit', [AdminVideoController::class, 'edit'])->name('edit');
            Route::put('/{video}', [AdminVideoController::class, 'update'])->name('update');
            Route::delete('/{video}', [AdminVideoController::class, 'destroy'])->name('destroy');
        });

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

        Route::prefix('daily-missions')->name('daily-missions.')->group(function () {
            Route::get('/', [AdminDailyMissionController::class, 'index'])->name('index');
            Route::get('/create', [AdminDailyMissionController::class, 'create'])->name('create');
            Route::post('/', [AdminDailyMissionController::class, 'store'])->name('store');
            Route::get('/{dailyMission}/edit', [AdminDailyMissionController::class, 'edit'])->name('edit');
            Route::put('/{dailyMission}', [AdminDailyMissionController::class, 'update'])->name('update');
            Route::delete('/{dailyMission}', [AdminDailyMissionController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('mission-sets')->name('mission-sets.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\MissionSetController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\MissionSetController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\MissionSetController::class, 'store'])->name('store');
            Route::get('/{missionSet}', [\App\Http\Controllers\Admin\MissionSetController::class, 'show'])->name('show');
            Route::post('/{missionSet}/clone-mission', [\App\Http\Controllers\Admin\MissionSetController::class, 'cloneMission'])->name('clone-mission');
            Route::get('/{missionSet}/edit', [\App\Http\Controllers\Admin\MissionSetController::class, 'edit'])->name('edit');
            Route::put('/{missionSet}', [\App\Http\Controllers\Admin\MissionSetController::class, 'update'])->name('update');
            Route::delete('/{missionSet}', [\App\Http\Controllers\Admin\MissionSetController::class, 'destroy'])->name('destroy');
            Route::post('/{missionSet}/assign', [\App\Http\Controllers\Consultant\MissionSetController::class, 'assign'])->name('assign');
            Route::post('/{missionSet}/missions', [\App\Http\Controllers\Consultant\MissionSetController::class, 'storeMission'])->name('missions.store');
        });

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Consultant\UserProgressController::class, 'index'])->name('index');
            Route::get('/assign-mission', [\App\Http\Controllers\Consultant\UserProgressController::class, 'index'])->name('assign-mission');
            Route::get('/{user}/progress', [\App\Http\Controllers\Consultant\UserProgressController::class, 'show'])->name('progress');
            Route::post('/{user}/assign', [\App\Http\Controllers\Consultant\UserProgressController::class, 'assign'])->name('assign');
        });
    });
