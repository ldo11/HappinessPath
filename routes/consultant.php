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
            Route::get('/{videoId}/edit', [AdminVideoController::class, 'edit'])->name('edit');
            Route::put('/{videoId}', [AdminVideoController::class, 'update'])->name('update');
            Route::delete('/{videoId}', [AdminVideoController::class, 'destroy'])->name('destroy');
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
    });
