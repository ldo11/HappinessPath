<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageLine extends Model
{
    protected $fillable = [
        'group',
        'key',
        'text',
    ];

    protected $casts = [
        'text' => 'array',
    ];
}
