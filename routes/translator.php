<?php

use App\Http\Controllers\Translator\DashboardController;
use App\Http\Controllers\Translator\LanguageLineController;
use App\Http\Controllers\Translator\AssessmentController;
use App\Http\Controllers\Translator\DailyMissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('translator')
    ->middleware(['web', 'auth', 'role:translator|admin'])
    ->name('translator.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/ui-matrix', [LanguageLineController::class, 'index'])->name('ui-matrix.index');
        Route::post('/ui-matrix/{languageLine}', [LanguageLineController::class, 'update'])->name('ui-matrix.update');

        Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::get('/assessments/{assessment}/translate', [AssessmentController::class, 'translate'])->name('assessments.translate');
        Route::post('/assessments/{assessment}/submit-translation', [AssessmentController::class, 'submitTranslation'])->name('assessments.submit-translation');

        Route::get('/daily-missions', [DailyMissionController::class, 'index'])->name('daily-missions.index');
        Route::get('/daily-missions/{dailyMission}', [DailyMissionController::class, 'show'])->name('daily-missions.show');
        Route::post('/daily-missions/{dailyMission}', [DailyMissionController::class, 'update'])->name('daily-missions.update');
    });
