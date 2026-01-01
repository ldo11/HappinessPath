<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class AssessmentOption extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = [
        'content',
    ];

    protected $fillable = [
        'question_id',
        'content',
        'score',
    ];

    protected $casts = [];

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'question_id');
    }
}
