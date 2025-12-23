<?php

namespace App\Observers;

use App\Jobs\AutoTranslateJob;
use App\Models\Solution;
use Illuminate\Support\Facades\Log;

class SolutionObserver
{
    /**
     * Handle the Solution "created" event.
     */
    public function created(Solution $solution): void
    {
        // Only dispatch auto-translation for Vietnamese solutions
        if ($solution->locale === 'vi') {
            Log::info('Dispatching AutoTranslateJob for Vietnamese solution', [
                'solution_id' => $solution->id,
                'locale' => $solution->locale
            ]);
            
            AutoTranslateJob::dispatch($solution);
        }
    }

    /**
     * Handle the Solution "updated" event.
     */
    public function updated(Solution $solution): void
    {
        // Optionally trigger re-translation if Vietnamese content is updated
        if ($solution->locale === 'vi' && $solution->wasChanged(['url', 'type'])) {
            Log::info('Solution updated, dispatching AutoTranslateJob', [
                'solution_id' => $solution->id,
                'locale' => $solution->locale,
                'changed' => $solution->getChanges()
            ]);
            
            AutoTranslateJob::dispatch($solution);
        }
    }

    /**
     * Handle the Solution "deleted" event.
     */
    public function deleted(Solution $solution): void
    {
        //
    }

    /**
     * Handle the Solution "restored" event.
     */
    public function restored(Solution $solution): void
    {
        //
    }

    /**
     * Handle the Solution "force deleted" event.
     */
    public function forceDeleted(Solution $solution): void
    {
        //
    }
}
