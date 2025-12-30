<?php

namespace App\Translation;

use App\Models\LanguageLine;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;

class DatabaseTranslationLoader implements Loader
{
    public function __construct(
        private Loader $fallbackLoader,
    ) {
    }

    public function load($locale, $group, $namespace = null)
    {
        $fileLines = $this->fallbackLoader->load($locale, $group, $namespace);

        // Only override default namespace translations.
        if (! empty($namespace) && $namespace !== '*') {
            return $fileLines;
        }

        try {
            $dbLines = $this->loadFromDatabase((string) $locale, (string) $group);
        } catch (QueryException) {
            return $fileLines;
        }

        if (empty($dbLines)) {
            return $fileLines;
        }

        return $this->mergeTranslations($fileLines, $dbLines);
    }

    public function addNamespace($namespace, $hint)
    {
        return $this->fallbackLoader->addNamespace($namespace, $hint);
    }

    public function addJsonPath($path)
    {
        return $this->fallbackLoader->addJsonPath($path);
    }

    public function namespaces()
    {
        return $this->fallbackLoader->namespaces();
    }

    private function loadFromDatabase(string $locale, string $group): array
    {
        $lines = LanguageLine::query()
            ->select(['key', 'text'])
            ->where('group', $group)
            ->get();

        $result = [];

        foreach ($lines as $line) {
            $text = $line->text;

            if (! is_array($text) || ! array_key_exists($locale, $text)) {
                continue;
            }

            $value = $text[$locale];

            if (! is_string($value) && ! is_numeric($value)) {
                continue;
            }

            Arr::set($result, (string) $line->key, (string) $value);
        }

        return $result;
    }

    private function mergeTranslations(array $fileLines, array $dbLines): array
    {
        // DB-first means DB values override file values.
        // array_replace_recursive replaces values in the first array with values from subsequent arrays.
        return array_replace_recursive($fileLines, $dbLines);
    }
}
