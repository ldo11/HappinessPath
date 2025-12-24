<?php

namespace App\Services;

use App\Models\AssessmentQuestion;

class AssessmentService
{
    public function calculateScore(array $answers): array
    {
        $heartScore = 0;
        $gritScore = 0;
        $wisdomScore = 0;

        foreach ($answers as $questionId => $score) {
            $question = AssessmentQuestion::query()->select(['id', 'pillar_group'])->find($questionId);
            if (!$question) {
                continue;
            }

            $score = (int) $score;

            switch ($question->pillar_group) {
                case 'heart':
                    $heartScore += $score;
                    break;
                case 'grit':
                    $gritScore += $score;
                    break;
                case 'wisdom':
                    $wisdomScore += $score;
                    break;
            }
        }

        $scores = [
            'heart' => $heartScore,
            'grit' => $gritScore,
            'wisdom' => $wisdomScore,
        ];

        $customFocus = array_keys($scores, min($scores))[0];

        return [
            'heart_score' => $heartScore,
            'grit_score' => $gritScore,
            'wisdom_score' => $wisdomScore,
            'custom_focus' => $customFocus,
        ];
    }
}
