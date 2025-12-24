<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserJourney extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'current_day',
        'custom_focus',
        'last_activity_at',
    ];

    protected $casts = [
        'current_day' => 'integer',
        'last_activity_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFocusLabelAttribute()
    {
        return match($this->custom_focus) {
            'heart' => __('dashboard.heart'),
            'grit' => __('dashboard.grit'),
            'wisdom' => __('dashboard.wisdom'),
            default => __('dashboard.not_selected'),
        };
    }

    public function getProgressPercentageAttribute()
    {
        // Assuming 30-day journey
        return min(100, ($this->current_day / 30) * 100);
    }

    public function getJourneyStatusAttribute()
    {
        if (!$this->last_activity_at) {
            return __('dashboard.not_started');
        }

        $daysSinceLastActivity = now()->diffInDays($this->last_activity_at);
        
        if ($daysSinceLastActivity <= 1) {
            return __('dashboard.active');
        } elseif ($daysSinceLastActivity <= 7) {
            return __('dashboard.paused');
        } else {
            return __('dashboard.inactive');
        }
    }
}
