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
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
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

    public function getTitleAttribute($value)
    {
        $locale = app()->getLocale();
        $title = json_decode($value, true);
        
        return $title[$locale] ?? $title['vi'] ?? $title['en'] ?? array_values($title)[0] ?? '';
    }

    public function getDescriptionAttribute($value)
    {
        $locale = app()->getLocale();
        $description = json_decode($value, true);
        
        return $description[$locale] ?? $description['vi'] ?? $description['en'] ?? array_values($description)[0] ?? '';
    }
}
