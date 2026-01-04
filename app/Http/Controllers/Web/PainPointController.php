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

        if (Schema::hasTable('pain_points') && Schema::hasTable('pain_point_user')) {
            $painPoints = PainPoint::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
            
            $userPainPoints = $request->user()->painPoints()
                ->get()
                ->keyBy('id')
                ->map(function ($painPoint) {
                    return (int) $painPoint->pivot->score;
                })->all();
        }

        return view('pain_points.index', compact('painPoints', 'userPainPoints'));
    }

    public function store(Request $request, $locale)
    {
        $data = $request->validate([
            'pain_points' => ['nullable', 'array'],
            'pain_points.*.id' => ['required', 'integer', 'exists:pain_points,id'],
            'pain_points.*.score' => ['required', 'integer', 'min:0', 'max:10'],
        ]);

        $sync = [];
        foreach (($data['pain_points'] ?? []) as $item) {
            $sync[(int) $item['id']] = ['score' => (int) $item['score']];
        }

        $request->user()->painPoints()->syncWithoutDetaching($sync);

        return redirect()->route('user.pain-points.index', ['locale' => $locale])->with('success', 'Đã lưu cập nhật nỗi khổ.');
    }

    public function storeRequest(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:pain_points,name'],
            'description' => ['nullable', 'string'],
        ]);

        PainPoint::create([
            'name' => $data['name'], // Will be cast to array by model if needed, but here we just save the string as default locale? 
            // The model casts 'name' to array. If we pass a string, Eloquent might try to json_encode it if it expects array?
            // Let's store it as {'vi': "Name"} or just array. 
            // Since the user is in a locale, maybe we should store it keyed by locale.
            'name' => [app()->getLocale() => $data['name']],
            'description' => $data['description'] ? [app()->getLocale() => $data['description']] : null,
            'status' => 'pending',
            'created_by_user_id' => $request->user()->id,
            'category' => 'mind', // Default category or ask user? Requirement says "Name, Description". Let's default to mind or make nullable. Schema has default? Schema says enum category not nullable. Let's pick 'mind' as safe default or add to form.
        ]);

        return redirect()->back()->with('success', 'Đã gửi yêu cầu thêm nỗi khổ mới. Vui lòng chờ duyệt.');
    }
}
