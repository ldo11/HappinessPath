<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'pillar_group',
        'order',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class)->orderBy('order');
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
