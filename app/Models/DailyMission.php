<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class DailyMission extends Model
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
        'points',
        'is_body',
        'is_mind',
        'is_wisdom',
        'created_by_id',
    ];

    protected $casts = [
        'points' => 'integer',
        'is_body' => 'boolean',
        'is_mind' => 'boolean',
        'is_wisdom' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
