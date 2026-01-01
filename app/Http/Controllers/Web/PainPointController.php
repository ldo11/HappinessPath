<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PainPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PainPointController extends Controller
{
    public function index(Request $request)
    {
        $painPoints = collect();
        $userPainPoints = [];

        if (Schema::hasTable('pain_points') && Schema::hasTable('user_pain_points')) {
            $painPoints = PainPoint::query()->orderBy('name')->get();
            $userPainPoints = $request->user()->painPoints()->get()->keyBy('id')->map(function ($painPoint) {
                return (int) $painPoint->pivot->severity;
            })->all();
        }

        return view('pain_points.index', compact('painPoints', 'userPainPoints'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pain_points' => ['nullable', 'array'],
            'pain_points.*.id' => ['required', 'integer', 'exists:pain_points,id'],
            'pain_points.*.severity' => ['required', 'integer', 'min:0', 'max:10'],
        ]);

        $sync = [];
        foreach (($data['pain_points'] ?? []) as $item) {
            $sync[(int) $item['id']] = ['severity' => (int) $item['severity']];
        }

        $request->user()->painPoints()->sync($sync);

        return redirect()->route('user.pain-points.index')->with('success', 'Đã lưu cập nhật nỗi khổ.');
    }
}
