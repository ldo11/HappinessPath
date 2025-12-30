<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'category',
        'language',
        'pillar_tag',
        'source_tag',
        'pillar_tags',
        'source_tags',
        'is_active',
        'xp_reward',
    ];

    protected $casts = [
        'pillar_tags' => 'array',
        'source_tags' => 'array',
        'is_active' => 'boolean',
        'xp_reward' => 'integer',
    ];

    public function getYoutubeIdAttribute(): ?string
    {
        $url = (string) ($this->url ?? '');
        if ($url === '') {
            return null;
        }

        if (preg_match('~youtube\.com/embed/([^?&/]+)~', $url, $m)) {
            return $m[1] ?? null;
        }
        if (preg_match('~youtu\.be/([^?&/]+)~', $url, $m)) {
            return $m[1] ?? null;
        }
        if (preg_match('~v=([^?&/]+)~', $url, $m)) {
            return $m[1] ?? null;
        }

        return null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        $id = $this->youtube_id;
        if (!$id) {
            return null;
        }

        return "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
    }

    public function getEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_id;
        if (!$id) {
            return $this->url ? (string) $this->url : null;
        }

        return "https://www.youtube.com/embed/{$id}";
    }

    public function userLogs(): HasMany
    {
        return $this->hasMany(UserVideoLog::class);
    }
}
