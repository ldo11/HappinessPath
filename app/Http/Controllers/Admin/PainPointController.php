<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PainPoint;
use Illuminate\Http\Request;

class PainPointController extends Controller
{
    public function index()
    {
        $pendingPainPoints = PainPoint::where('status', 'pending')->with('createdByUser')->latest()->get();
        $activePainPoints = PainPoint::where('status', 'active')->latest()->paginate(30);
        $rejectedPainPoints = PainPoint::where('status', 'rejected')->latest()->paginate(10);

        return view('admin.pain-points.index', compact('pendingPainPoints', 'activePainPoints', 'rejectedPainPoints'));
    }

    public function approve(Request $request, $locale, $id)
    {
        $painPoint = PainPoint::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255', // Admin can edit name on approval
            'category' => 'required|string|in:mind,body,wisdom',
        ]);

        // Keep existing translations if any, but ensure current locale updates
        $name = $painPoint->name;
        if (is_array($name)) {
            $name[$locale] = $data['name'];
        } else {
            $name = [$locale => $data['name']];
        }

        $painPoint->update([
            'name' => $name,
            'category' => $data['category'],
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'Pain point approved successfully.');
    }

    public function reject($locale, $id)
    {
        $painPoint = PainPoint::findOrFail($id);
        $painPoint->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Pain point rejected.');
    }
}
