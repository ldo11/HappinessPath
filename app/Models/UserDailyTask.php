<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDailyTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'daily_task_id',
        'report_content',
        'completed_at',
        'xp_awarded',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'xp_awarded' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dailyTask(): BelongsTo
    {
        return $this->belongsTo(DailyTask::class);
    }
}
