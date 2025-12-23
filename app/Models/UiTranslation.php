<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UiTranslation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'key',
        'locale',
        'value',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class, 'locale', 'code');
    }
}
