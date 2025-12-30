<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationSystemMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'content',
        'type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(ConsultationThread::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'assessment_assignment' => 'Assessment Assignment',
            'system_notification' => 'System Notification',
            default => 'System Message',
        };
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'assessment_assignment' => 'fas fa-clipboard-list',
            'system_notification' => 'fas fa-info-circle',
            default => 'fas fa-cog',
        };
    }

    public function getColorAttribute(): string
    {
        return match($this->type) {
            'assessment_assignment' => 'blue',
            'system_notification' => 'gray',
            default => 'gray',
        };
    }
}
