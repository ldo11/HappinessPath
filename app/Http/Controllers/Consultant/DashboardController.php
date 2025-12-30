<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Models\ConsultationReply;
use App\Models\ConsultationThread;
use App\Models\Assessment;
use App\Models\AssessmentAssignment;
use App\Models\ConsultationSystemMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $thread->load(['user', 'replies.user', 'relatedPainPoint', 'systemMessages']);

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

    public function assignAssessment(Request $request, ConsultationThread $thread)
    {
        $data = $request->validate([
            'assessment_id' => ['required', 'exists:assessments,id'],
        ]);

        $assessment = Assessment::findOrFail($data['assessment_id']);
        
        // Only allow assignment of active or special assessments
        if (!in_array($assessment->status, ['active', 'special'])) {
            return back()->with('error', 'Only active or special assessments can be assigned.');
        }

        // Create or update assessment assignment
        $assignment = AssessmentAssignment::updateOrCreate(
            [
                'consultation_thread_id' => $thread->id,
                'assessment_id' => $assessment->id,
                'user_id' => $thread->user_id,
            ],
            [
                'assigned_by' => Auth::id(),
                'access_token' => \Illuminate\Support\Str::random(64),
                'expires_at' => now()->addDays(30), // 30 days expiration
            ]
        );

        // Create system message in the thread
        $accessUrl = $assignment->getAccessUrl();
        $message = "Consultant has assigned you a special assessment: [{$assessment->title}]({$accessUrl})";
        
        ConsultationSystemMessage::create([
            'thread_id' => $thread->id,
            'content' => $message,
            'type' => 'assessment_assignment',
            'metadata' => [
                'assessment_id' => $assessment->id,
                'assignment_id' => $assignment->id,
                'access_url' => $accessUrl,
            ],
        ]);

        return back()->with('success', 'Assessment assigned successfully.');
    }

    public function getAvailableAssessments()
    {
        $assessments = Assessment::whereIn('status', ['active', 'special'])
            ->orderBy('status')
            ->orderBy('title')
            ->get(['id', 'title', 'status']);

        return response()->json($assessments);
    }
}
