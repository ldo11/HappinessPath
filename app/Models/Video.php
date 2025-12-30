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

    /**
     * Scope to filter videos by various criteria
     */
    public function scopeFilter($query, array $filters)
    {
        // Filter by pillar tags (JSON column)
        if (isset($filters['pillar_tags']) && is_array($filters['pillar_tags'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['pillar_tags'] as $tag) {
                    $q->orWhereJsonContains('pillar_tags', $tag);
                }
            });
        }

        // Filter by source tags (JSON column)
        if (isset($filters['source_tags']) && is_array($filters['source_tags'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['source_tags'] as $tag) {
                    $q->orWhereJsonContains('source_tags', $tag);
                }
            });
        }

        // Filter by language
        if (isset($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        // Filter by active status
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query;
    }
}
