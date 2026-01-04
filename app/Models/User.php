<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserVideoLog;
use App\Models\Video;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use \Illuminate\Auth\MustVerifyEmail;

    protected $fillable = [
        'buddy_id',
        'name',
        'email',
        'password',
        'dob',
        'disc_type',
        'role',
        'is_available',
        'spiritual_preference',
        'onboarding_status',
        'start_pain_level',
        'city',
        'district',
        'country',
        'geo_privacy',
        'locale',
        'language',
        'religion',
        'nickname',
        'display_language',
        'introduction',
        'location',
        'xp_body',
        'xp_mind',
        'xp_wisdom',
        'active_mission_set_id',
        'mission_started_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'dob' => 'date',
            'geo_privacy' => 'boolean',
            'is_available' => 'boolean',
            'password' => 'hashed',
            'mission_started_at' => 'date',
        ];
    }

    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    public function completedMissions(): BelongsToMany
    {
        return $this->belongsToMany(DailyMission::class, 'mission_completions')
            ->withPivot(['completed_at', 'xp_earned'])
            ->withTimestamps();
    }

    public function buddy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buddy_id');
    }

    public function userJourney(): HasOne
    {
        return $this->hasOne(UserJourney::class);
    }

    public function quizResult(): HasOne
    {
        return $this->hasOne(UserQuizResult::class);
    }

    public function painPoints(): BelongsToMany
    {
        return $this->belongsToMany(PainPoint::class, 'pain_point_user')
            ->withPivot(['score'])
            ->withTimestamps();
    }

    public function consultantPainPoints(): BelongsToMany
    {
        return $this->belongsToMany(PainPoint::class, 'consultant_pain_point')
            ->withTimestamps();
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function numerology(): HasOne
    {
        return $this->hasOne(UserNumerology::class);
    }

    public function userVideoLogs(): HasMany
    {
        return $this->hasMany(UserVideoLog::class);
    }

    public function watchedVideos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'user_video_logs')
            ->withPivot(['claimed_at', 'xp_awarded'])
            ->withTimestamps();
    }

    public function activeMissionSet(): BelongsTo
    {
        return $this->belongsTo(MissionSet::class, 'active_mission_set_id');
    }
}
