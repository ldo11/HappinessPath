<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    protected $middleware = [
        'admin'
    ];

    public function index(Request $request)
    {
        $videos = Video::query()->orderByDesc('id')->paginate(20);

        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.videos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:2048'],
            'language' => ['required', 'in:vi,en,de,kr'],
            'pillar_tags' => ['nullable', 'array'],
            'pillar_tags.*' => ['in:body,mind,wisdom'],
            'source_tags' => ['nullable', 'array'],
            'source_tags.*' => ['in:buddhism,christianity,science'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $isActive = (bool) ($data['is_active'] ?? false);

        $pillarTags = array_values(array_unique(array_filter($data['pillar_tags'] ?? [])));
        $sourceTags = array_values(array_unique(array_filter($data['source_tags'] ?? [])));

        Video::create([
            'title' => $data['title'],
            'url' => $data['url'],
            'language' => $data['language'],
            'pillar_tags' => $pillarTags,
            'source_tags' => $sourceTags,
            'pillar_tag' => $pillarTags[0] ?? null,
            'source_tag' => $sourceTags[0] ?? null,
            'category' => $pillarTags[0] ?? 'mind',
            'is_active' => $isActive,
        ]);

        return redirect()->route('admin.videos.index')
            ->with('success', 'Video created successfully.');
    }

    public function edit(Video $video)
    {
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:2048'],
            'language' => ['required', 'in:vi,en,de,kr'],
            'pillar_tags' => ['nullable', 'array'],
            'pillar_tags.*' => ['in:body,mind,wisdom'],
            'source_tags' => ['nullable', 'array'],
            'source_tags.*' => ['in:buddhism,christianity,science'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $pillarTags = array_values(array_unique(array_filter($data['pillar_tags'] ?? [])));
        $sourceTags = array_values(array_unique(array_filter($data['source_tags'] ?? [])));

        $video->update([
            'title' => $data['title'],
            'url' => $data['url'],
            'language' => $data['language'],
            'pillar_tags' => $pillarTags,
            'source_tags' => $sourceTags,
            'pillar_tag' => $pillarTags[0] ?? $video->pillar_tag,
            'source_tag' => $sourceTags[0] ?? $video->source_tag,
            'category' => $pillarTags[0] ?? $video->category ?? 'mind',
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return redirect()->route('admin.videos.edit', $video)
            ->with('success', 'Video updated successfully.');
    }

    public function destroy(Video $video)
    {
        $video->delete();

        return redirect()->route('admin.videos.index')
            ->with('success', 'Video deleted successfully.');
    }
}
