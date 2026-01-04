<?php

namespace App\Http\Controllers\Translator;

use App\Http\Controllers\Controller;
use App\Models\PainPoint;
use Illuminate\Http\Request;

class PainPointController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = PainPoint::query()->latest();

        if ($search) {
            // Need to search inside JSON column or exact match?
            // Simple search might not work well with JSON on some DBs without specific syntax
            // We'll filter by ID or raw string match for now
             $query->where('id', 'like', "%{$search}%")
                   ->orWhere('name', 'like', "%{$search}%");
        }

        $painPoints = $query->paginate(20);

        return view('translator.pain-points.index', compact('painPoints', 'search'));
    }

    public function update(Request $request, $id)
    {
        $painPoint = PainPoint::findOrFail($id);

        $data = $request->validate([
            'name' => 'array',
            'description' => 'array',
        ]);

        // Merge new translations with existing ones
        $name = $painPoint->name;
        if (!is_array($name)) $name = [];
        
        $description = $painPoint->description;
        if (!is_array($description)) $description = [];

        foreach (($data['name'] ?? []) as $locale => $val) {
            if ($val) $name[$locale] = $val;
        }

        foreach (($data['description'] ?? []) as $locale => $val) {
            if ($val) $description[$locale] = $val;
        }

        $painPoint->update([
            'name' => $name,
            'description' => $description,
        ]);

        return redirect()->back()->with('success', 'Translations updated successfully.');
    }
}
