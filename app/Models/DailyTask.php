<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'day_number',
        'content',
        'pillar_tag',
        'title',
        'description',
        'type',
        'difficulty',
        'difficulty_level_int',
        'estimated_minutes',
        'solution_id',
        'instructions',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'content' => 'array',
        'instructions' => 'array',
        'estimated_minutes' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the solution associated with this task.
     */
    public function solution()
    {
        return $this->belongsTo(Solution::class);
    }

    /**
     * Scope to get only active tasks.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get tasks for a specific day.
     */
    public function scopeForDay($query, int $day)
    {
        return $query->where('day_number', $day);
    }

    /**
     * Check if the task is completed.
     */
    public function isCompleted()
    {
        return !is_null($this->completed_at);
    }

    /**
     * Mark the task as completed.
     */
    public function markAsCompleted()
    {
        $this->completed_at = now();
        $this->save();
    }
}
