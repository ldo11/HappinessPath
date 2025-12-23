<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'url',
        'author_name',
        'pillar_tag',
        'locale',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function translations()
    {
        return $this->hasMany(SolutionTranslation::class);
    }

    public function vietnameseTranslation()
    {
        return $this->hasOne(SolutionTranslation::class)->where('locale', 'vi');
    }

    public function getTranslation($locale)
    {
        return $this->translations()->where('locale', $locale)->first();
    }
}
