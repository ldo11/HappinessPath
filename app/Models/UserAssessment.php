<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assessment_id',
        'answers',
        'total_score',
        'result_label',
        'submission_mode',
        'consultation_thread_id',
        'pillar_scores',
    ];

    protected $casts = [
        'answers' => 'array',
        'pillar_scores' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function consultationThread(): BelongsTo
    {
        return $this->belongsTo(ConsultationThread::class);
    }

    public function isSubmittedForConsultation(): bool
    {
        return $this->submission_mode === 'submitted_for_consultation';
    }

    public function isSelfReview(): bool
    {
        return $this->submission_mode === 'self_review';
    }

    public function hasConsultationThread(): bool
    {
        return !is_null($this->consultation_thread_id);
    }

    public function canBeConvertedToConsultation(): bool
    {
        return $this->isSelfReview() && !$this->hasConsultationThread();
    }

    public function getSubmissionModeLabelAttribute(): string
    {
        return match($this->submission_mode) {
            'self_review' => 'Self Review',
            'submitted_for_consultation' => 'Consultation',
            default => 'Unknown'
        };
    }

    public function getSubmissionModeColorAttribute(): string
    {
        return match($this->submission_mode) {
            'self_review' => 'green',
            'submitted_for_consultation' => 'amber',
            default => 'gray'
        };
    }
}
