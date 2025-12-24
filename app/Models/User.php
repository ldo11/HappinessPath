<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use \Illuminate\Auth\MustVerifyEmail;

    protected $fillable = [
        'name',
        'email',
        'password',
        'dob',
        'disc_type',
        'role',
        'spiritual_preference',
        'onboarding_status',
        'start_pain_level',
        'city',
        'district',
        'country',
        'geo_privacy',
        'locale',
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

    public function userTree()
    {
        return $this->hasOne(UserTree::class);
    }

    public function userJourney()
    {
        return $this->hasOne(UserJourney::class);
    }

    public function numerology()
    {
        return $this->hasOne(UserNumerology::class);
    }
}
