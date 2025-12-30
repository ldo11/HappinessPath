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
        'role_v2',
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
            'password' => 'hashed',
        ];
    }

    public function getRoleAttribute($value)
    {
        $roleV2 = $this->attributes['role_v2'] ?? null;
        if (is_string($roleV2) && $roleV2 !== '') {
            return strtolower($roleV2);
        }

        $legacy = $value;
        $legacyLower = is_string($legacy) ? strtolower($legacy) : $legacy;

        return match ($legacyLower) {
            null, '' => 'user',
            'member' => 'user',
            'volunteer' => 'translator',
            default => $legacyLower,
        };
    }

    public function setRoleAttribute($value): void
    {
        $roleLower = is_string($value) ? strtolower(trim($value)) : $value;

        $canonical = match ($roleLower) {
            null, '' => 'user',
            'member' => 'user',
            'volunteer' => 'translator',
            default => $roleLower,
        };

        $this->attributes['role_v2'] = $canonical;

        // Keep legacy enum column compatible.
        $this->attributes['role'] = match ($canonical) {
            'admin' => 'admin',
            'translator' => 'volunteer',
            default => 'member',
        };
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
        return $this->belongsToMany(PainPoint::class, 'user_pain_points')
            ->withPivot(['severity'])
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
}
