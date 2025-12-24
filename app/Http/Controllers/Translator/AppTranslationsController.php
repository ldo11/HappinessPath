<?php

namespace App\Http\Controllers\Translator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class AppTranslationsController extends Controller
{
    protected $middleware = [
        'translator'
    ];

    public function index(Request $request)
    {
        $locale = $request->get('locale', 'vi');
        if (!in_array($locale, ['vi', 'en'], true)) {
            $locale = 'vi';
        }

        $baseLocale = $locale === 'vi' ? 'en' : 'vi';

        $translations = $this->loadLocaleTranslations($locale);
        $baseTranslations = $this->loadLocaleTranslations($baseLocale);

        $allKeys = array_unique(array_merge(array_keys($translations), array_keys($baseTranslations)));
        sort($allKeys);

        $rows = [];
        foreach ($allKeys as $key) {
            $value = $translations[$key] ?? null;
            $baseValue = $baseTranslations[$key] ?? null;

            $isMissing = ($value === null || $value === '') && ($baseValue !== null && $baseValue !== '');

            $rows[$key] = [
                'value' => $value,
                'base' => $baseValue,
                'missing' => $isMissing,
            ];
        }

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $rows = array_filter(
                $rows,
                fn ($row, $key) => str_contains(mb_strtolower($key), mb_strtolower($search))
                    || str_contains(mb_strtolower((string) ($row['value'] ?? '')), mb_strtolower($search))
                    || str_contains(mb_strtolower((string) ($row['base'] ?? '')), mb_strtolower($search)),
                ARRAY_FILTER_USE_BOTH
            );
        }

        $grouped = [];
        foreach ($rows as $key => $row) {
            $parts = explode('.', $key, 2);
            $group = $parts[0] ?? 'other';
            $grouped[$group] ??= [];
            $grouped[$group][$key] = $row;
        }

        ksort($grouped);

        return view('translator.app-translations.index', [
            'locale' => $locale,
            'baseLocale' => $baseLocale,
            'grouped' => $grouped,
            'search' => $search,
        ]);
    }

    public function download(Request $request)
    {
        $locale = $request->get('locale', 'vi');
        if (!in_array($locale, ['vi', 'en'], true)) {
            $locale = 'vi';
        }

        $translations = $this->loadLocaleTranslations($locale);
        ksort($translations);

        $filename = "translations_{$locale}.json";

        return response()->json($translations, 200, [
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|in:vi,en',
            'file' => 'required|file',
        ]);

        $locale = $validated['locale'];

        $raw = File::get($request->file('file')->getRealPath());
        $data = json_decode($raw, true);

        if (!is_array($data)) {
            return back()->with('error', 'Invalid JSON file.');
        }

        // Group by first segment (e.g. auth.email -> auth)
        $grouped = [];
        foreach ($data as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            if (!is_scalar($value) && $value !== null) {
                continue;
            }

            $parts = explode('.', $key, 2);
            $group = $parts[0] ?? '';
            $rest = $parts[1] ?? '';
            if ($group === '' || $rest === '') {
                continue;
            }

            $grouped[$group] ??= [];
            Arr::set($grouped[$group], $rest, $value);
        }

        foreach ($grouped as $group => $values) {
            $this->writeGroupFile($locale, $group, $values);
        }

        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
        } catch (\Throwable $e) {
            // ignore
        }

        return redirect()->route('translator.app-translations.index', ['locale' => $locale])
            ->with('success', 'Translations updated successfully.');
    }

    private function loadLocaleTranslations(string $locale): array
    {
        $basePath = lang_path($locale);
        $out = [];

        if (!File::exists($basePath)) {
            return $out;
        }

        $files = File::files($basePath);
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $group = $file->getFilenameWithoutExtension();
            $arr = include $file->getRealPath();

            if (!is_array($arr)) {
                continue;
            }

            $flat = Arr::dot($arr);
            foreach ($flat as $k => $v) {
                $out[$group . '.' . $k] = is_scalar($v) || $v === null ? $v : json_encode($v);
            }
        }

        return $out;
    }

    private function writeGroupFile(string $locale, string $group, array $values): void
    {
        $dir = lang_path($locale);
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filePath = $dir . DIRECTORY_SEPARATOR . $group . '.php';

        $existing = [];
        if (File::exists($filePath)) {
            $loaded = include $filePath;
            if (is_array($loaded)) {
                $existing = $loaded;
            }
        }

        $merged = array_replace_recursive($existing, $values);

        $export = var_export($merged, true);
        $content = "<?php\n\nreturn {$export};\n";
        File::put($filePath, $content);
    }
}
