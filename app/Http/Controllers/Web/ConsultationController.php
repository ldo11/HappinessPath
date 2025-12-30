<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ConsultationReply;
use App\Models\ConsultationThread;
use App\Models\PainPoint;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $threads = ConsultationThread::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return view('consultations.index', compact('threads'));
    }

    public function create(Request $request)
    {
        $painPoints = PainPoint::query()->orderBy('title')->get();

        return view('consultations.create', compact('painPoints'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'related_pain_point_id' => ['nullable', 'integer', 'exists:pain_points,id'],
        ]);

        $thread = ConsultationThread::create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'content' => $data['content'],
            'related_pain_point_id' => $data['related_pain_point_id'] ?? null,
            'status' => 'open',
            'is_private' => true,
        ]);

        return redirect()->route('consultations.show', $thread)
            ->with('success', 'Gửi yêu cầu tư vấn thành công.');
    }

    public function show(Request $request, ConsultationThread $thread)
    {
        if ($thread->user_id !== $request->user()->id) {
            abort(404);
        }

        $thread->load(['replies.user', 'relatedPainPoint']);

        return view('consultations.show', compact('thread'));
    }

    public function reply(Request $request, ConsultationThread $thread)
    {
        if ($thread->user_id !== $request->user()->id) {
            abort(404);
        }

        if (in_array($thread->status, ['closed'], true)) {
            return redirect()->route('consultations.show', $thread)
                ->with('error', 'Yêu cầu tư vấn đã được đóng.');
        }

        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        ConsultationReply::create([
            'thread_id' => $thread->id,
            'user_id' => $request->user()->id,
            'content' => $data['content'],
        ]);

        if ($thread->status === 'answered') {
            $thread->update(['status' => 'open']);
        }

        return redirect()->route('consultations.show', $thread)
            ->with('success', 'Đã gửi phản hồi.');
    }
}
