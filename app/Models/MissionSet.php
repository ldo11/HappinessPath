<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class MissionSet extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'type',
        'created_by',
        'is_default',
    ];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'created_by' => 'integer',
        'is_default' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function missions(): HasMany
    {
        return $this->hasMany(DailyMission::class)->orderBy('day_number');
    }

    public function activeUsers(): HasMany
    {
        return $this->hasMany(User::class, 'active_mission_set_id');
    }
}
