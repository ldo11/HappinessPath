<?php

namespace App\Console\Commands;

use App\Models\LanguageLine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportJsonTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:import-json {--fresh : Clear existing JSON translations before import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import translations from JSON files to database';

    private array $locales = ['en', 'vi', 'de', 'kr'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('Clearing existing JSON translations...');
            LanguageLine::where('group', 'json')->delete();
        }

        $langPath = base_path('lang');
        $allTranslations = [];

        // Read all JSON files and collect translations
        foreach ($this->locales as $locale) {
            $jsonFile = $langPath . '/' . $locale . '.json';
            if (File::exists($jsonFile)) {
                $translations = json_decode(File::get($jsonFile), true);
                if (is_array($translations)) {
                    $this->flattenTranslations($translations, $locale, $allTranslations);
                    $this->info("Imported " . count($translations, COUNT_RECURSIVE) . " keys from {$locale}.json");
                }
            } else {
                $this->warn("File {$jsonFile} not found.");
            }
        }

        // Insert into database
        $imported = 0;
        foreach ($allTranslations as $key => $translations) {
            LanguageLine::query()->updateOrCreate(
                ['group' => 'json', 'key' => $key],
                ['text' => $translations]
            );
            $imported++;
        }

        $this->info("Successfully imported {$imported} translation keys to database.");
        
        return 0;
    }

    /**
     * Flatten nested translation arrays with dot notation
     */
    private function flattenTranslations(array $translations, string $locale, array &$allTranslations, string $prefix = ''): void
    {
        foreach ($translations as $key => $value) {
            $fullKey = $prefix ? $prefix . '.' . $key : $key;
            
            if (is_array($value)) {
                $this->flattenTranslations($value, $locale, $allTranslations, $fullKey);
            } else {
                $allTranslations[$fullKey][$locale] = $value;
            }
        }
    }
}
