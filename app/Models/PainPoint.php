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
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_pain_points')
            ->withPivot(['severity'])
            ->withTimestamps();
    }
}
