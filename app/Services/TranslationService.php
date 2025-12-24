<?php

namespace App\Services;

use App\Models\Solution;

class TranslationService
{
    /**
     * Return solutions that do not yet have a translation row for the given locale.
     */
    public function getMissingTranslations(string $locale)
    {
        return Solution::query()
            ->whereDoesntHave('translations', function ($q) use ($locale) {
                $q->where('locale', $locale);
            })
            ->get();
    }
}
