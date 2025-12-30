<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'content',
        'score',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'question_id');
    }

    public function getContentAttribute($value)
    {
        $locale = app()->getLocale();
        $content = json_decode($value, true);
        
        return $content[$locale] ?? $content['vi'] ?? $content['en'] ?? array_values($content)[0] ?? '';
    }
}
