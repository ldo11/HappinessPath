<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'created_by',
        'score_ranges',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'score_ranges' => 'array',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class)->orderBy('order');
    }

    public function userAssessments(): HasMany
    {
        return $this->hasMany(UserAssessment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSpecial($query)
    {
        return $query->where('status', 'special');
    }

    public function getResultLabel(int $totalScore): ?string
    {
        if (empty($this->score_ranges)) {
            return null;
        }

        foreach ($this->score_ranges as $range) {
            if ($totalScore >= $range['min'] && $totalScore <= $range['max']) {
                return $range['label'];
            }
        }

        return null;
    }

    public function getTitleAttribute($value)
    {
        $locale = app()->getLocale();
        $title = json_decode($value, true);
        
        if (!is_array($title)) {
            return $value; // Return raw value if not JSON
        }
        
        return $title[$locale] ?? $title['vi'] ?? $title['en'] ?? array_values($title)[0] ?? '';
    }

    public function getDescriptionAttribute($value)
    {
        $locale = app()->getLocale();
        $description = json_decode($value, true);
        
        return $description[$locale] ?? $description['vi'] ?? $description['en'] ?? array_values($description)[0] ?? '';
    }
}
