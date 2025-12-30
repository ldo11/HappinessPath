<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserVideoLog;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $allowedPillars = ['body', 'mind', 'wisdom'];

        $user = $request->user();
        $currentLang = (string) ($user?->language ?? app()->getLocale());
        if (!in_array($currentLang, ['vi', 'en', 'de', 'kr'], true)) {
            $currentLang = 'vi';
        }

        $allowedSourcesByReligion = match ((string) ($user?->religion ?? 'none')) {
            'buddhism' => ['buddhism', 'science'],
            'christianity' => ['christianity', 'science'],
            'science' => ['science'],
            default => ['buddhism', 'christianity', 'science'],
        };

        $pillar = $request->query('pillar');
        $source = $request->query('source');

        $category = $request->query('category');
        if (!is_string($pillar) && is_string($category)) {
            $pillar = $category;
        }

        $query = Video::query()
            ->where('is_active', true)
            ->where('language', $currentLang)
            ->latest();

        if (is_string($pillar) && in_array($pillar, $allowedPillars, true)) {
            $query->whereJsonContains('pillar_tags', $pillar);
        } else {
            $pillar = null;
        }

        // Strict filtering by user's religion:
        // - If user selects a specific source, it must be within allowed set.
        // - Otherwise, constrain to allowed set.
        if (is_string($source) && in_array($source, $allowedSourcesByReligion, true)) {
            $query->whereJsonContains('source_tags', $source);
        } else {
            $source = null;
            $query->where(function ($q) use ($allowedSourcesByReligion) {
                foreach ($allowedSourcesByReligion as $tag) {
                    $q->orWhereJsonContains('source_tags', $tag);
                }
            });
        }

        $videos = $query->paginate(12)->withQueryString();

        return view('videos.index', [
            'videos' => $videos,
            'language' => $currentLang,
            'pillar' => $pillar,
            'source' => $source,
        ]);
    }

    public function show(Request $request, string $locale, $videoId)
    {
        $video = Video::query()->findOrFail($videoId);

        // Check if video is active
        if (!$video->is_active) {
            abort(404);
        }

        $user = $request->user();
        $lang = (string) ($user?->language ?? app()->getLocale());
        if (!in_array($lang, ['vi', 'en', 'de', 'kr'], true)) {
            $lang = 'vi';
        }

        if ((string) ($video->language ?? 'vi') !== $lang) {
            abort(404);
        }

        $allowedSourcesByReligion = match ((string) ($user?->religion ?? 'none')) {
            'buddhism' => ['buddhism', 'science'],
            'christianity' => ['christianity', 'science'],
            'science' => ['science'],
            default => ['buddhism', 'christianity', 'science'],
        };

        $videoSources = (array) ($video->source_tags ?? []);
        $allowed = count(array_intersect($videoSources, $allowedSourcesByReligion)) > 0;
        if (!$allowed) {
            abort(404);
        }

        $log = null;
        if ($request->user()) {
            $log = UserVideoLog::query()
                ->where('user_id', $request->user()->id)
                ->where('video_id', $video->id)
                ->first();
        }

        $relatedVideos = Video::query()
            ->where('is_active', true)
            ->where('id', '!=', $video->id)
            ->where('language', $lang)
            ->where(function ($q) use ($allowedSourcesByReligion) {
                foreach ($allowedSourcesByReligion as $tag) {
                    $q->orWhereJsonContains('source_tags', $tag);
                }
            })
            ->latest()
            ->limit(8)
            ->get();

        return view('videos.show', compact('video', 'log', 'relatedVideos'));
    }

    public function claim(Request $request, string $locale, $videoId)
    {
        $user = $request->user();

        $video = Video::query()->findOrFail($videoId);

        $result = DB::transaction(function () use ($user, $video) {
            $log = UserVideoLog::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'video_id' => $video->id,
                ],
                [
                    'claimed_at' => null,
                    'xp_awarded' => null,
                ]
            );

            if ($log->claimed_at) {
                return [
                    'claimed' => false,
                    'xp_awarded' => (int) ($log->xp_awarded ?? 0),
                ];
            }

            $xp = (int) ($video->xp_reward ?? 50);

            // For now, we'll just store the XP in the log
            // In the future, we might add a simple xp column to users table
            // or implement a different progress tracking system

            $log->claimed_at = now();
            $log->xp_awarded = $xp;
            $log->save();

            return [
                'claimed' => true,
                'xp_awarded' => $xp,
            ];
        });

        return response()->json($result);
    }
}
