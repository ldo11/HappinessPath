<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PainPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'icon',
        'description',
        'icon_url',
        'status',
        'created_by_user_id',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pain_point_user')
            ->withPivot(['score'])
            ->withTimestamps();
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function consultants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'consultant_pain_point')
            ->withTimestamps();
    }

    public function getTranslatedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $name = $this->name;
        
        if (!is_array($name)) {
            return (string) $name;
        }

        return $name[$locale] ?? $name['vi'] ?? $name['en'] ?? reset($name) ?? '';
    }

    public function getTranslatedDescription(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $description = $this->description;

        if (!is_array($description)) {
            return (string) $description;
        }

        return $description[$locale] ?? $description['vi'] ?? $description['en'] ?? reset($description) ?? null;
    }
}
