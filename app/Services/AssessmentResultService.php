<?php

namespace App\Services;

use App\Models\UserAssessment;
use App\Models\AssessmentOption;

class AssessmentResultService
{
    /**
     * Calculate detailed results for a user assessment
     */
    public function calculateResults(UserAssessment $userAssessment): array
    {
        $results = [
            'total_score' => $userAssessment->total_score,
            'max_score' => 0,
            'percentage' => 0,
            'question_results' => [],
            'performance_level' => 'beginner',
            'recommendations' => [],
        ];

        $assessment = $userAssessment->assessment;
        $assessment->load(['questions.options']);

        foreach ($assessment->questions->sortBy('order') as $question) {
            $maxQuestionScore = $question->options->max('score');
            $results['max_score'] += $maxQuestionScore;

            $selectedOptionId = $userAssessment->answers[$question->id] ?? null;
            $selectedOption = AssessmentOption::find($selectedOptionId);

            $questionResult = [
                'question' => $question,
                'selected_option' => $selectedOption,
                'max_score' => $maxQuestionScore,
                'score_earned' => $selectedOption ? $selectedOption->score : 0,
                'percentage' => 0,
            ];

            if ($maxQuestionScore > 0) {
                $questionResult['percentage'] = ($questionResult['score_earned'] / $maxQuestionScore) * 100;
            }

            $results['question_results'][] = $questionResult;
        }

        // Calculate overall percentage
        if ($results['max_score'] > 0) {
            $results['percentage'] = ($results['total_score'] / $results['max_score']) * 100;
        }

        // Determine performance level
        $results['performance_level'] = $this->getPerformanceLevel($results['percentage']);

        // Generate recommendations
        $results['recommendations'] = $this->generateRecommendations($results);

        return $results;
    }

    /**
     * Determine performance level based on percentage
     */
    private function getPerformanceLevel(float $percentage): string
    {
        if ($percentage >= 90) {
            return 'expert';
        } elseif ($percentage >= 75) {
            return 'advanced';
        } elseif ($percentage >= 60) {
            return 'intermediate';
        } elseif ($percentage >= 40) {
            return 'beginner';
        } else {
            return 'needs_improvement';
        }
    }

    /**
     * Generate personalized recommendations based on results
     */
    private function generateRecommendations(array $results): array
    {
        $recommendations = [];
        $percentage = $results['percentage'];

        // Performance-based recommendations
        if ($percentage >= 80) {
            $recommendations[] = [
                'type' => 'achievement',
                'title' => 'Excellent Performance!',
                'description' => 'You\'ve shown outstanding understanding. Consider exploring advanced topics or mentoring others.',
                'action' => 'Explore advanced assessments',
            ];
        } elseif ($percentage >= 60) {
            $recommendations[] = [
                'type' => 'progress',
                'title' => 'Good Progress',
                'description' => 'You\'re doing well! Focus on areas where you didn\'t get maximum points to continue improving.',
                'action' => 'Review missed questions',
            ];
        } else {
            $recommendations[] = [
                'type' => 'improvement',
                'title' => 'Room for Growth',
                'description' => 'This is a great starting point. Review the material and consider retaking the assessment.',
                'action' => 'Study and retake assessment',
            ];
        }

        // Find weak areas (questions with low performance)
        $weakAreas = [];
        foreach ($results['question_results'] as $questionResult) {
            if ($questionResult['percentage'] < 50) {
                $weakAreas[] = $questionResult['question'];
            }
        }

        if (!empty($weakAreas)) {
            $recommendations[] = [
                'type' => 'focus_area',
                'title' => 'Focus Areas Identified',
                'description' => 'Consider spending more time on ' . count($weakAreas) . ' areas where you scored below 50%.',
                'action' => 'Review specific topics',
                'questions' => $weakAreas,
            ];
        }

        // Consultation recommendation
        if ($percentage < 70) {
            $recommendations[] = [
                'type' => 'consultation',
                'title' => 'Consider Professional Guidance',
                'description' => 'A consultation could provide personalized insights and strategies for improvement.',
                'action' => 'Submit for consultation',
            ];
        }

        return $recommendations;
    }

    /**
     * Convert self-review assessment to consultation mode
     */
    public function convertToConsultation(UserAssessment $userAssessment): ?UserAssessment
    {
        if (!$userAssessment->canBeConvertedToConsultation()) {
            return null;
        }

        $consultationThread = \App\Models\ConsultationThread::create([
            'user_id' => $userAssessment->user_id,
            'title' => "Kết quả đánh giá: {$userAssessment->assessment->title}",
            'content' => "Người dùng đã yêu cầu tư vấn dựa trên kết quả đánh giá này. Điểm số: {$userAssessment->total_score} ({{ round(($userAssessment->total_score / $this->calculateMaxScore($userAssessment)) * 100, 1) }}%)",
            'status' => 'pending',
        ]);

        $userAssessment->update([
            'submission_mode' => 'submitted_for_consultation',
            'consultation_thread_id' => $consultationThread->id,
        ]);

        return $userAssessment;
    }

    /**
     * Calculate maximum possible score for an assessment
     */
    private function calculateMaxScore(UserAssessment $userAssessment): int
    {
        $maxScore = 0;
        $assessment = $userAssessment->assessment;
        
        foreach ($assessment->questions as $question) {
            $maxScore += $question->options->max('score');
        }
        
        return $maxScore;
    }
}
