<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\UserAssessment;
use App\Models\ConsultationThread;
use App\Models\AssessmentAssignment;
use App\Models\AssessmentOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserAssessmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get available assessments
        $availableAssessments = Assessment::active()
            ->with('questions.options')
            ->get();
            
        // Get special assessments assigned to this user
        $specialAssessments = Assessment::special()
            ->whereHas('userAssessments', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('questions.options')
            ->get();
            
        // Combine available and special assessments
        $allAvailableAssessments = $availableAssessments->merge($specialAssessments);
        
        // Get user's assessment history (latest result for each assessment)
        $userResults = UserAssessment::where('user_id', $user->id)
            ->with('assessment')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('assessment_id')
            ->values();

        return view('web.assessments.index', compact('allAvailableAssessments', 'userResults'));
    }

    public function show($assessment, $token = null)
    {
        // Handle both route model binding and raw ID
        if (is_numeric($assessment)) {
            $assessment = Assessment::findOrFail($assessment);
        } elseif (is_string($assessment)) {
            $assessment = Assessment::findOrFail($assessment);
        }
        
        // Check if user can access this assessment
        $user = Auth::user();
        
        if (!$this->canUserAccessAssessment($user, $assessment, $token)) {
            abort(403, 'You do not have access to this assessment.');
        }

        $assessment->load(['questions.options' => function ($query) {
            $query->orderBy('score', 'desc');
        }]);

        return view('web.assessments.show', compact('assessment'));
    }

    public function showSigned(Assessment $assessment, $token)
    {
        return $this->show($assessment, $token);
    }

    public function submit(Request $request, $assessmentId) // Accept $assessmentId to avoid binding issues
    {
        // 1. Manual Lookup to bypass binding errors
        $assessment = Assessment::findOrFail($assessmentId);

        // 2. Validate
        $data = $request->validate([
            'answers' => 'required|array', // Structure: [question_id => option_id]
            'submission_mode' => 'required|in:self_review,submitted_for_consultation'
        ]);

        // 3. Calculate scores using AssessmentService
        $assessmentService = app(\App\Services\AssessmentService::class);
        
        // Check if assessment uses flexible scoring (has score_ranges)
        if ($assessment->score_ranges && !empty($assessment->score_ranges)) {
            // Use flexible scoring system
            $scores = $assessmentService->calculateFlexibleScore($data['answers'], $assessment);
            
            $userAssessment = UserAssessment::create([
                'user_id' => auth()->id(),
                'assessment_id' => $assessment->id,
                'submission_mode' => $data['submission_mode'],
                'consultation_thread_id' => null,
                'answers' => $data['answers'],
                'total_score' => $scores['total_score'],
                'result_label' => $scores['result_label'],
                'pillar_scores' => null, // Not used in flexible scoring
            ]);
        } else {
            // Use original body-mind-wisdom scoring system
            $scores = $assessmentService->calculateScore($data['answers']);
            
            // Extract pillar scores for storage
            $pillarScores = [
                'body' => $scores['body'] ?? 0,
                'mind' => $scores['mind'] ?? 0,
                'wisdom' => $scores['wisdom'] ?? 0,
            ];

            $userAssessment = UserAssessment::create([
                'user_id' => auth()->id(),
                'assessment_id' => $assessment->id,
                'submission_mode' => $data['submission_mode'],
                'consultation_thread_id' => null,
                'answers' => $data['answers'],
                'pillar_scores' => $pillarScores,
                'total_score' => array_sum($pillarScores), // Overall wellness score
                'result_label' => null, // Not used in pillar scoring
            ]);
            
            // Handle Pain Points for pillar scoring
            $assessmentService->syncPainPointsFromTriggers(auth()->user(), $scores['pain_point_triggers'] ?? []);
        }

        // 4. Handle Consultation Logic
        if ($data['submission_mode'] === 'submitted_for_consultation') {
            // Check if thread creation is failing. Ensure strict creation.
            $consultationThread = \App\Models\ConsultationThread::create([
                'user_id' => auth()->id(),
                'title' => 'Kết quả đánh giá: ' . ($assessment->title['vi'] ?? 'Assessment Result'),
                'content' => 'Người dùng yêu cầu tư vấn dựa trên kết quả bài đánh giá này.',
                'related_assessment_id' => $assessment->id,
                'status' => 'open',
                'is_private' => true,
            ]);

            // Link the consultation thread to the user assessment
            $userAssessment->consultation_thread_id = $consultationThread->id;
            $userAssessment->save();
        }

        // 5. Return Response
        return response()->json([
            'message' => 'Submitted successfully',
            'redirect_url' => route('user.assessments.result', $userAssessment->id)
        ]);
    }

    public function result(UserAssessment $userAssessment)
    {
        // Ensure user can only see their own results
        if ($userAssessment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $userAssessment->load(['assessment', 'assessment.questions.options']);

        // Calculate detailed results
        $results = $this->calculateDetailedResults($userAssessment);

        return view('web.assessments.result', compact('userAssessment', 'results'));
    }

    public function convertToConsultation(UserAssessment $userAssessment)
    {
        // Ensure user can only convert their own results
        if ($userAssessment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if (!$userAssessment->canBeConvertedToConsultation()) {
            abort(403, 'This assessment cannot be converted to consultation.');
        }

        $consultationThread = ConsultationThread::create([
            'user_id' => $userAssessment->user_id,
            'title' => "Review Assessment Result: " . $userAssessment->assessment->title,
            'content' => "User has requested a review of their assessment result. Score: {$userAssessment->total_score}",
            'status' => 'pending',
        ]);

        $userAssessment->update([
            'submission_mode' => 'submitted_for_consultation',
            'consultation_thread_id' => $consultationThread->id,
        ]);

        return redirect()->route('user.consultations.show', ['consultation_id' => $consultationThread->id])
            ->with('success', 'Assessment result has been sent for consultation!');
    }

    private function canUserAccessAssessment($user, $assessment, $token = null)
    {
        // Active assessments are accessible to all users
        if ($assessment->status === 'active') {
            return true;
        }

        // Special assessments require specific access
        if ($assessment->status === 'special') {
            // Admin and Consultant can access all special assessments
            if (in_array($user->role, ['admin', 'consultant'])) {
                return true;
            }

            // Check for valid signed URL access
            if ($token) {
                $assignment = AssessmentAssignment::where('assessment_id', $assessment->id)
                    ->where('user_id', $user->id)
                    ->where('access_token', $token)
                    ->first();

                return $assignment && $assignment->isValid();
            }

            // Check if user has been assigned this assessment via consultation
            return UserAssessment::where('user_id', $user->id)
                ->where('assessment_id', $assessment->id)
                ->exists();
        }

        return false;
    }

    private function calculateDetailedResults(UserAssessment $userAssessment)
    {
        $results = [
            'total_score' => $userAssessment->total_score,
            'max_score' => 0,
            'percentage' => 0,
            'question_results' => [],
        ];

        foreach ($userAssessment->assessment->questions as $question) {
            $maxQuestionScore = $question->options->max('score');
            $results['max_score'] += $maxQuestionScore;

            $selectedOptionId = $userAssessment->answers[$question->id] ?? null;
            $selectedOption = $question->options->find($selectedOptionId);

            $results['question_results'][] = [
                'question' => $question,
                'selected_option' => $selectedOption,
                'max_score' => $maxQuestionScore,
                'score_earned' => $selectedOption ? $selectedOption->score : 0,
            ];
        }

        if ($results['max_score'] > 0) {
            $results['percentage'] = ($results['total_score'] / $results['max_score']) * 100;
        }

        return $results;
    }

    public function submitStandalone(Request $request)
    {
        // Validate the form data
        $data = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer',
        ]);

        // Check if user is authenticated
        if (!auth()->check()) {
            // Redirect to login with a message to return after assessment
            return redirect()->route('user.login')
                ->with('info', 'Please login to submit your assessment. Your answers will be saved.')
                ->with('intended', route('user.assessment.form'));
        }

        $user = auth()->user();
        
        // Create a simple assessment record or redirect to results
        // For now, just redirect to the assessments index with a success message
        return redirect()->route('user.assessments.index')
            ->with('success', 'Assessment submitted successfully!');
    }
}
