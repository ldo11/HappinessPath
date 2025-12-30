<?php

namespace App\Services;

use App\Models\AssessmentQuestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssessmentService
{
    public function calculateScore(array $answers): array
    {
        $heartScore = 0;
        $gritScore = 0;
        $wisdomScore = 0;

        foreach ($answers as $questionId => $score) {
            $question = AssessmentQuestion::query()->select(['id', 'pillar_group', 'is_negative'])->find($questionId);
            if (!$question) {
                continue;
            }

            $input = (int) $score;
            $adjusted = $this->adjustAnswerScore($input, (bool) ($question->is_negative ?? false));

            switch ($question->pillar_group) {
                case 'heart':
                    $heartScore += $adjusted;
                    break;
                case 'grit':
                    $gritScore += $adjusted;
                    break;
                case 'wisdom':
                    $wisdomScore += $adjusted;
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

    public function calculateScoreAndSyncPainPoints(User $user, array $answers): array
    {
        $result = $this->calculateScore($answers);
        $this->syncPainPointsFromAnswers($user, $answers);

        return $result;
    }

    private function adjustAnswerScore(int $input, bool $isNegative): int
    {
        $input = max(1, min(5, $input));

        if ($isNegative) {
            return 6 - $input;
        }

        return $input;
    }

    private function mapPainSeverity(int $input): int
    {
        return match (max(1, min(5, $input))) {
            5 => 10,
            4 => 8,
            3 => 5,
            2 => 2,
            default => 0,
        };
    }

    private function syncPainPointsFromAnswers(User $user, array $answers): void
    {
        if (!Schema::hasTable('user_pain_points') || !Schema::hasTable('assessment_questions')) {
            return;
        }

        if (!Schema::hasColumn('assessment_questions', 'related_pain_id') || !Schema::hasColumn('assessment_questions', 'is_negative')) {
            return;
        }

        $painSeverityById = [];

        foreach ($answers as $questionId => $score) {
            $question = AssessmentQuestion::query()->select(['id', 'is_negative', 'related_pain_id'])->find($questionId);
            if (!$question) {
                continue;
            }

            if (!(bool) $question->is_negative) {
                continue;
            }

            $input = (int) $score;
            $severity = $this->mapPainSeverity($input);
            if ($severity <= 0) {
                continue;
            }

            $related = $question->related_pain_id;
            if (is_string($related)) {
                $decoded = json_decode($related, true);
                $related = is_array($decoded) ? $decoded : [];
            }

            if (!is_array($related)) {
                continue;
            }

            foreach ($related as $painId) {
                $painId = (int) $painId;
                if ($painId <= 0) {
                    continue;
                }

                $painSeverityById[$painId] = max($painSeverityById[$painId] ?? 0, $severity);
            }
        }

        if (empty($painSeverityById)) {
            return;
        }

        $now = now();
        $rows = [];
        foreach ($painSeverityById as $painId => $severity) {
            $rows[] = [
                'user_id' => $user->id,
                'pain_point_id' => $painId,
                'severity' => $severity,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('user_pain_points')->upsert(
            $rows,
            ['user_id', 'pain_point_id'],
            ['severity', 'updated_at']
        );
    }
}
