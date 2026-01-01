<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::withCount(['solutionTranslations', 'uiTranslations'])
            ->latest()
            ->get();
            
        return view('admin.languages.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.languages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages,code',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If setting as default, unset all other defaults
        if ($validated['is_default'] ?? false) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        Language::create($validated);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language created successfully.');
    }

    public function edit(Language $language)
    {
        return view('admin.languages.edit', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('languages')->ignore($language->code, 'code')],
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If setting as default, unset all other defaults
        if ($validated['is_default'] ?? false) {
            Language::where('is_default', true)->where('code', '!=', $language->code)->update(['is_default' => false]);
        }

        $language->update($validated);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language updated successfully.');
    }

    public function destroy(Language $language)
    {
        // Check if language has translations
        if ($language->solutionTranslations()->count() > 0 || $language->uiTranslations()->count() > 0) {
            return redirect()->route('admin.languages.index')
                ->with('error', 'Cannot delete language with existing translations.');
        }

        $language->delete();

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language deleted successfully.');
    }

    public function toggleStatus(Language $language)
    {
        $language->is_active = !$language->is_active;
        $language->save();

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language status updated successfully.');
    }
}
