<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsultationThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'related_pain_point_id',
        'status',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedPainPoint(): BelongsTo
    {
        return $this->belongsTo(PainPoint::class, 'related_pain_point_id');
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
