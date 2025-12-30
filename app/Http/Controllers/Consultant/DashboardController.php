<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Models\ConsultationReply;
use App\Models\ConsultationThread;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $threads = ConsultationThread::query()
            ->where('status', 'open')
            ->latest()
            ->paginate(20);

        return view('consultant.dashboard', compact('threads'));
    }

    public function show(Request $request, ConsultationThread $thread)
    {
        $thread->load(['user', 'replies.user', 'relatedPainPoint']);

        return view('consultant.show', compact('thread'));
    }

    public function reply(Request $request, ConsultationThread $thread)
    {
        if (in_array($thread->status, ['closed'], true)) {
            return redirect()->route('consultant.threads.show', $thread)
                ->with('error', 'Thread is closed.');
        }

        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        ConsultationReply::create([
            'thread_id' => $thread->id,
            'user_id' => $request->user()->id,
            'content' => $data['content'],
        ]);

        if ($thread->status === 'open') {
            $thread->update(['status' => 'answered']);
        }

        return redirect()->route('consultant.threads.show', $thread)
            ->with('success', 'Reply sent.');
    }
}
