<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'content',
        'type',
        'order',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(AssessmentOption::class);
    }

    public function getContentAttribute($value)
    {
        $locale = app()->getLocale();
        $content = json_decode($value, true);
        
        return $content[$locale] ?? $content['vi'] ?? $content['en'] ?? array_values($content)[0] ?? '';
    }
}
