<?php

namespace App\Jobs;

use App\Models\Solution;
use App\Models\SolutionTranslation;
use App\Services\GeminiTranslationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoTranslateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Solution $solution;

    /**
     * Create a new job instance.
     */
    public function __construct(Solution $solution)
    {
        $this->solution = $solution;
    }

    /**
     * Execute the job.
     */
    public function handle(GeminiTranslationService $translationService): void
    {
        // Only auto-translate from Vietnamese
        if ($this->solution->locale !== 'vi') {
            Log::info('Skipping auto-translation: solution is not in Vietnamese', [
                'solution_id' => $this->solution->id,
                'locale' => $this->solution->locale
            ]);
            return;
        }

        // Target languages to translate to
        $targetLanguages = ['en', 'de']; // Add more as needed

        foreach ($targetLanguages as $targetLang) {
            try {
                // Check if translation already exists
                $existing = SolutionTranslation::where('solution_id', $this->solution->id)
                    ->where('locale', $targetLang)
                    ->first();

                if ($existing) {
                    Log::info('Translation already exists, skipping', [
                        'solution_id' => $this->solution->id,
                        'locale' => $targetLang
                    ]);
                    continue;
                }

                // Prepare text for translation (combine title and content if available)
                $textToTranslate = '';
                
                // Get the Vietnamese title/content if they exist in translations table
                $vietnameseTranslation = SolutionTranslation::where('solution_id', $this->solution->id)
                    ->where('locale', 'vi')
                    ->first();

                if ($vietnameseTranslation) {
                    $textToTranslate = "Title: " . $vietnameseTranslation->title . "\n\nContent: " . $vietnameseTranslation->content;
                } else {
                    // Fallback to solution URL if no Vietnamese translation exists
                    $textToTranslate = "Video URL: " . $this->solution->url;
                }

                // Translate using Gemini
                $translated = $translationService->translate($textToTranslate, $targetLang);

                if (!$translated) {
                    Log::error('Translation failed', [
                        'solution_id' => $this->solution->id,
                        'target_lang' => $targetLang
                    ]);
                    continue;
                }

                // Save the translation
                DB::transaction(function () use ($translated, $targetLang) {
                    SolutionTranslation::create([
                        'solution_id' => $this->solution->id,
                        'locale' => $targetLang,
                        'title' => $translated['title'] ?? '',
                        'content' => $translated['content'] ?? '',
                        'is_auto_generated' => true,
                        'ai_provider' => 'gemini',
                    ]);
                });

                Log::info('Auto-translation completed', [
                    'solution_id' => $this->solution->id,
                    'locale' => $targetLang,
                    'title' => substr($translated['title'] ?? '', 0, 50)
                ]);

            } catch (\Exception $e) {
                Log::error('Error during auto-translation', [
                    'solution_id' => $this->solution->id,
                    'target_lang' => $targetLang,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
