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
        $user = $request->user() ?? auth()->user();

        if (! $user) {
            abort(403);
        }

        $consultantPainPointIds = $user->consultantPainPoints()->pluck('pain_points.id')->all();

        $threadsQuery = ConsultationThread::query()
            ->where(function ($q) use ($user, $consultantPainPointIds) {
                $q->where('assigned_consultant_id', $user->id);

                if (! empty($consultantPainPointIds)) {
                    $q->orWhereIn('pain_point_id', $consultantPainPointIds)
                        ->orWhereIn('related_pain_point_id', $consultantPainPointIds);
                }
            });

        $tab = (string) $request->query('tab', 'open');
        if ($tab === 'closed') {
            $threadsQuery->where('status', 'closed');
        } else {
            $threadsQuery->where('status', '!=', 'closed');
        }

        $threads = $threadsQuery
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('consultant.dashboard', [
            'threads' => $threads,
            'tab' => $tab,
        ]);
    }

    public function show(Request $request, string $locale, ConsultationThread $thread)
    {
        $thread->load(['user', 'replies.user', 'relatedPainPoint', 'systemMessages']);

        return view('consultant.show', compact('thread'));
    }

    public function reply(Request $request, string $locale, ConsultationThread $thread)
    {
        if (in_array($thread->status, ['closed'], true)) {
            return redirect()->route('consultant.threads.show', ['locale' => $locale, 'thread' => $thread->id])
                ->with('error', 'Thread is closed.');
        }

        if (empty($thread->assigned_consultant_id)) {
            $thread->assigned_consultant_id = $request->user()->id;
            $thread->save();
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

        return redirect()->route('consultant.threads.show', ['locale' => $locale, 'thread' => $thread->id])
            ->with('success', 'Reply sent.');
    }

    public function assignAssessment(Request $request, string $locale, ConsultationThread $thread)
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
