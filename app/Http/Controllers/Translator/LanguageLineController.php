<?php

namespace App\Http\Controllers\Translator;

use App\Http\Controllers\Controller;
use App\Models\LanguageLine;
use Illuminate\Http\Request;

class LanguageLineController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $query = LanguageLine::query()->orderBy('group')->orderBy('key');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $like = "%{$search}%";
                $q->where('group', 'like', $like)
                    ->orWhere('key', 'like', $like);
            });
        }

        $lines = $query->paginate(50)->withQueryString();

        return view('translator.language-lines.index', compact('lines', 'search'));
    }

    public function update(Request $request, LanguageLine $languageLine)
    {
        $data = $request->validate([
            'vi' => ['nullable', 'string'],
            'en' => ['nullable', 'string'],
            'de' => ['nullable', 'string'],
            'kr' => ['nullable', 'string'],
        ]);

        $text = (array) ($languageLine->text ?? []);
        foreach (['vi', 'en', 'de', 'kr'] as $locale) {
            if (array_key_exists($locale, $data)) {
                $text[$locale] = $data[$locale] ?? '';
            }
        }

        $languageLine->update([
            'text' => $text,
        ]);

        return redirect()->route('translator.language-lines.index', $request->only('search'))
            ->with('success', 'Translations updated.');
    }
}
