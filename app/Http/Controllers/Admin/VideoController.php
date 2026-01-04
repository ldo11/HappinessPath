<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    private function redirectToIndex(Request $request)
    {
        $routeName = (string) optional($request->route())->getName();

        if (str_starts_with($routeName, 'consultant.')) {
            return redirect()->route('consultant.videos.index', ['locale' => app()->getLocale()]);
        }

        return redirect()->route('admin.videos.index');
    }

    public function index(Request $request)
    {
        $videos = Video::query()->orderByDesc('id')->paginate(20);

        $routeName = (string) optional($request->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.videos.index', compact('videos'));
        }

        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.videos.create');
        }

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

        if ($pillarTags === []) {
            $pillarTags = ['mind'];
        }

        if ($sourceTags === []) {
            $sourceTags = ['science'];
        }

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

        return $this->redirectToIndex($request)
            ->with('success', 'Video created successfully.');
    }

    public function edit()
    {
        $video = $this->resolveVideo();

        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.videos.edit', compact('video'));
        }

        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request)
    {
        $video = $this->resolveVideo();

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

        if ($pillarTags === []) {
            $pillarTags = ['mind'];
        }

        if ($sourceTags === []) {
            $sourceTags = ['science'];
        }

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

        $routeName = (string) optional($request->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return redirect()->route('consultant.videos.edit', ['locale' => app()->getLocale(), 'video' => $video->id])
                ->with('success', 'Video updated successfully.');
        }

        return redirect()->route('admin.videos.edit', $video)
            ->with('success', 'Video updated successfully.');
    }

    public function destroy()
    {
        $video = $this->resolveVideo();
        $video->delete();

        return $this->redirectToIndex(request())
            ->with('success', 'Video deleted successfully.');
    }

    private function resolveVideo()
    {
        $id = request()->route('video');
        
        if ($id instanceof Video) {
            return $id;
        }
        
        return Video::findOrFail($id);
    }
}
