<?php

namespace App\Console\Commands;

use App\Models\LanguageLine;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class LangImport extends Command
{
    protected $signature = 'lang:import
        {--path= : Path to the lang directory (defaults to base_path("lang"))}
        {--locales=* : Locales to import (defaults to all locales found)}
        {--dry-run : Show what would be imported without writing to the database}';

    protected $description = 'Import PHP / JSON language files from lang/ into the language_lines table.';

    public function handle(): int
    {
        $langPath = $this->option('path') ?: base_path('lang');

        if (! is_dir($langPath)) {
            $this->error("lang path not found: {$langPath}");
            return self::FAILURE;
        }

        $locales = $this->option('locales');
        if (empty($locales)) {
            $locales = $this->detectLocales($langPath);
        }

        if (empty($locales)) {
            $this->warn('No locales detected.');
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');

        $count = 0;
        foreach ($locales as $locale) {
            $count += $this->importLocalePhpFiles($langPath, $locale, $dryRun);
            $count += $this->importLocaleJsonFile($langPath, $locale, $dryRun);
        }

        $dryMsg = $dryRun ? ' (dry-run)' : '';
        $this->info("Imported/updated {$count} language line(s){$dryMsg}.");

        return self::SUCCESS;
    }

    private function detectLocales(string $langPath): array
    {
        $locales = [];

        foreach (File::directories($langPath) as $dir) {
            $locales[] = basename($dir);
        }

        foreach (File::files($langPath) as $file) {
            if (strtolower($file->getExtension()) !== 'json') {
                continue;
            }

            $locales[] = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }

        $locales = array_values(array_unique(array_filter($locales)));
        sort($locales);

        return $locales;
    }

    private function importLocalePhpFiles(string $langPath, string $locale, bool $dryRun): int
    {
        $dir = $langPath.DIRECTORY_SEPARATOR.$locale;
        if (! is_dir($dir)) {
            return 0;
        }

        $count = 0;

        foreach (File::files($dir) as $file) {
            if (strtolower($file->getExtension()) !== 'php') {
                continue;
            }

            $group = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            $data = require $file->getPathname();
            if (! is_array($data)) {
                continue;
            }

            $flat = Arr::dot($data);

            foreach ($flat as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    continue;
                }

                $value = (string) $value;

                $this->upsertLine($group, $key, $locale, $value, $dryRun);
                $count++;
            }
        }

        return $count;
    }

    private function importLocaleJsonFile(string $langPath, string $locale, bool $dryRun): int
    {
        $jsonPath = $langPath.DIRECTORY_SEPARATOR.$locale.'.json';
        if (! is_file($jsonPath)) {
            return 0;
        }

        $raw = File::get($jsonPath);
        $data = json_decode($raw, true);

        if (! is_array($data)) {
            $this->warn("Skipping invalid JSON: {$jsonPath}");
            return 0;
        }

        $flat = Arr::dot($data);

        $count = 0;
        foreach ($flat as $fullKey => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            $value = (string) $value;

            // JSON translations don't have a group in Laravel's default loader; we store them under '*'
            // using the dotted key (e.g. 'auth.login', 'assessment.progress').
            $this->upsertLine('*', $fullKey, $locale, $value, $dryRun);
            $count++;
        }

        return $count;
    }

    private function upsertLine(string $group, string $key, string $locale, string $value, bool $dryRun): void
    {
        $existing = LanguageLine::query()
            ->where('group', $group)
            ->where('key', $key)
            ->first();

        $text = $existing?->text ?? [];
        $text[$locale] = $value;

        if ($dryRun) {
            $this->line("[dry-run] {$group}.{$key} => {$locale}");
            return;
        }

        LanguageLine::query()->updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['text' => $text],
        );
    }
}
