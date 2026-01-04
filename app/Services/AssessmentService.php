<?php

namespace App\Services;

use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use App\Models\User;
use App\Models\Assessment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssessmentService
{
    public function calculateFlexibleScore(array $answers, Assessment $assessment): array
    {
        $totalScore = 0;
        $textAnswers = [];
        $scoredAnswers = [];

        foreach ($answers as $questionId => $answer) {
            $question = AssessmentQuestion::query()->find($questionId);
            if (!$question) {
                continue;
            }

            // Handle text questions (qualitative only)
            if ($question->type === 'text') {
                $textAnswers[$questionId] = $answer;
                continue;
            }

            // Handle single_choice and multi_choice questions
            if (in_array($question->type, ['single_choice', 'multi_choice'])) {
                // Get the option(s) and their scores
                if (is_array($answer)) {
                    // Multi-choice: sum scores of all selected options
                    foreach ($answer as $optionId) {
                        $option = AssessmentOption::query()->find($optionId);
                        if ($option) {
                            $totalScore += $option->score ?? 0;
                            $scoredAnswers[$questionId][] = [
                                'option_id' => $optionId,
                                'score' => $option->score ?? 0,
                                'content' => $option->content
                            ];
                        }
                    }
                } else {
                    // Single-choice: get score of selected option
                    $option = AssessmentOption::query()->find($answer);
                    if ($option) {
                        $totalScore += $option->score ?? 0;
                        $scoredAnswers[$questionId] = [
                            'option_id' => $answer,
                            'score' => $option->score ?? 0,
                            'content' => $option->content
                        ];
                    }
                }
            }
        }

        // Calculate result label based on score ranges
        $resultLabel = $assessment->getResultLabel($totalScore);

        return [
            'total_score' => $totalScore,
            'result_label' => $resultLabel,
            'scored_answers' => $scoredAnswers,
            'text_answers' => $textAnswers,
        ];
    }

    public function calculateScore(array $answers): array
    {
        // Group answers by pillar and collect raw scores
        $pillarScores = [
            'body' => [],
            'mind' => [],
            'wisdom' => []
        ];

        $painPointTriggers = [];

        foreach ($answers as $questionId => $score) {
            $question = AssessmentQuestion::query()
                ->select(['id', 'pillar_group_new', 'is_reversed', 'related_pain_point_key'])
                ->find($questionId);
            
            if (!$question || !$question->pillar_group_new) {
                continue;
            }

            $inputScore = (int) $score;
            
            // Handle reverse scoring
            $adjustedScore = $question->is_reversed ? (6 - $inputScore) : $inputScore;

            // Add to pillar scores
            $pillarScores[$question->pillar_group_new][] = $adjustedScore;

            // Check for pain point triggers
            if ($question->related_pain_point_key && $adjustedScore <= 2) {
                $painPointTriggers[$question->related_pain_point_key] = $adjustedScore;
            }
        }

        // Calculate percentage scores for each pillar
        $result = [];
        foreach ($pillarScores as $pillar => $scores) {
            if (empty($scores)) {
                $result[$pillar] = 0;
                continue;
            }

            $totalScore = array_sum($scores);
            $maxPossibleScore = count($scores) * 5; // 5-point scale
            $percentage = ($totalScore / $maxPossibleScore) * 100;
            
            $result[$pillar] = round($percentage, 1);
        }

        // Add pain point triggers to result
        $result['pain_point_triggers'] = $painPointTriggers;

        return $result;
    }

    public function adjustAnswerScore(int $score, bool $isNegative): int
    {
        // This method is deprecated in favor of the new scoring logic
        // Keeping for backward compatibility
        return $isNegative ? (6 - $score) : $score;
    }

    public function calculateScoreAndSyncPainPoints(User $user, array $answers): array
    {
        $result = $this->calculateScore($answers);
        $this->syncPainPointsFromTriggers($user, $result['pain_point_triggers'] ?? []);

        return $result;
    }

    public function syncPainPointsFromTriggers(User $user, array $painPointTriggers): void
    {
        if (empty($painPointTriggers)) {
            return;
        }

        // First, remove any existing pain points that are no longer triggered
        // This would require a pain_points table to map keys to IDs
        // For now, we'll work with the assumption that pain_point_key maps directly
        
        foreach ($painPointTriggers as $painPointKey => $score) {
            $severity = (6 - $score) * 2; // Score 1 -> Severity 10, Score 2 -> Severity 8
            
            // Create or update pain point record
            // This assumes a user_pain_points table with pain_point_key column
            DB::table('user_pain_points')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'pain_point_key' => $painPointKey,
                ],
                [
                    'severity' => $severity,
                    'updated_at' => now(),
                ]
            );
        }
    }
}
