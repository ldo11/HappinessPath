<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Assessment extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = [
        'title',
        'description',
    ];

    protected $fillable = [
        'title',
        'description',
        'status',
        'created_by',
        'score_ranges',
    ];

    protected $casts = [
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
}
