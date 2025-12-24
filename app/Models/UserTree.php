<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTree extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'season',
        'health',
        'exp',
        'fruits_balance',
        'total_fruits_given',
    ];

    protected $casts = [
        'health' => 'integer',
        'exp' => 'integer',
        'fruits_balance' => 'integer',
        'total_fruits_given' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLevelAttribute()
    {
        return floor($this->exp / 100) + 1;
    }

    public function getSeasonLabelAttribute()
    {
        return match($this->season) {
            'spring' => __('dashboard.spring'),
            'summer' => __('dashboard.summer'),
            'autumn' => __('dashboard.autumn'),
            'winter' => __('dashboard.winter'),
            default => 'Unknown',
        };
    }
}
