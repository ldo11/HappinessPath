<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolutionTranslation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'solution_id',
        'locale',
        'title',
        'content',
        'is_auto_generated',
        'reviewed_at',
        'reviewed_by',
        'ai_provider',
    ];

    protected $casts = [
        'is_auto_generated' => 'boolean',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function solution()
    {
        return $this->belongsTo(Solution::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'locale', 'code');
    }
}
