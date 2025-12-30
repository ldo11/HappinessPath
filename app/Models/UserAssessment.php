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
        'submission_mode',
        'consultation_thread_id',
    ];

    protected $casts = [
        'answers' => 'array',
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
}
