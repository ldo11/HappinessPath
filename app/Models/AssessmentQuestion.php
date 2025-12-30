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
        'pillar_group',
        'pillar_group_new',
        'is_negative',
        'is_reversed',
        'related_pain_id',
        'related_pain_point_key',
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
        return $this->hasMany(AssessmentOption::class, 'question_id');
    }

    public function getContentAttribute($value)
    {
        $locale = app()->getLocale();
        $content = json_decode($value, true);
        
        return $content[$locale] ?? $content['vi'] ?? $content['en'] ?? array_values($content)[0] ?? '';
    }

    public function getPillarGroupAttribute($value)
    {
        // Use new pillar_group_new if available, otherwise fall back to old pillar_group
        $newPillarGroup = $this->attributes['pillar_group_new'] ?? null;
        if ($newPillarGroup) {
            return $newPillarGroup;
        }
        
        // Map old pillar groups to new ones
        $oldToNewMap = [
            'heart' => 'body',
            'grit' => 'mind', 
            'wisdom' => 'wisdom',
        ];
        
        return $oldToNewMap[$value] ?? $value;
    }

    public function getIsReversedAttribute($value)
    {
        // Use new is_reversed if available, otherwise fall back to old is_negative
        $newIsReversed = $this->attributes['is_reversed'] ?? null;
        if ($newIsReversed !== null) {
            return (bool) $newIsReversed;
        }
        
        // Map old is_negative to new is_reversed
        $oldIsNegative = $this->attributes['is_negative'] ?? null;
        return (bool) ($oldIsNegative ?? false);
    }

    public function getRelatedPainPointKeyAttribute($value)
    {
        // Use new related_pain_point_key if available, otherwise fall back to old related_pain_id
        $newKey = $this->attributes['related_pain_point_key'] ?? null;
        if ($newKey) {
            return $newKey;
        }
        
        // Extract first pain point from old JSON array
        $oldPainIds = $this->attributes['related_pain_id'] ?? null;
        if ($oldPainIds) {
            $decoded = json_decode($oldPainIds, true);
            if (is_array($decoded) && !empty($decoded)) {
                // For now, just return the first ID as a string
                // In a real implementation, you'd map pain_point_id to pain_point_key
                return (string) $decoded[0];
            }
        }
        
        return null;
    }
}
