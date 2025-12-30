<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AssessmentAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_thread_id',
        'assessment_id',
        'user_id',
        'assigned_by',
        'access_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($assignment) {
            if (empty($assignment->access_token)) {
                $assignment->access_token = Str::random(64);
            }
        });
    }

    public function consultationThread(): BelongsTo
    {
        return $this->belongsTo(ConsultationThread::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    public function getAccessUrl(): string
    {
        return route('assessments.signed', [
            'assessment' => $this->assessment_id,
            'token' => $this->access_token
        ]);
    }
}
