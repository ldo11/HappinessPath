<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVideoLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_id',
        'claimed_at',
        'xp_awarded',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'xp_awarded' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}
