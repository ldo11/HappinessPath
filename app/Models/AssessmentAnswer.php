<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'content',
        'score',
        'order',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class);
    }

    public function getContentAttribute($value): array
    {
        return $value ?? [];
    }

    public function getLocalizedContent(string $locale = 'vi'): string
    {
        return $this->content[$locale] ?? $this->content['vi'] ?? '';
    }
}
