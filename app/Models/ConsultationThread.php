<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationThread extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'related_pain_point_id',
        'pain_point_id',
        'assigned_consultant_id',
        'status',
        'closed_at',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'closed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedPainPoint(): BelongsTo
    {
        return $this->belongsTo(PainPoint::class, 'related_pain_point_id');
    }

    public function painPoint(): BelongsTo
    {
        return $this->belongsTo(PainPoint::class, 'pain_point_id');
    }

    public function assignedConsultant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_consultant_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ConsultationReply::class, 'thread_id');
    }

    public function systemMessages(): HasMany
    {
        return $this->hasMany(ConsultationSystemMessage::class, 'thread_id');
    }
}
