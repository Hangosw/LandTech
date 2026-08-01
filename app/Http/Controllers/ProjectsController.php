<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectsController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::ordered()->get();

        if ($request->ajax()) {
            $data = $projects->map(function (Project $project) {
                return [
                    'id' => $project->id,
                    'slug' => $project->slug,
                    'label' => $project->label,
                    'image' => $project->image ?: '/images/hero-nhatrang.jpg',
                    'district' => $project->district,
                    'listing_count' => $project->listing_count,
                    'live_listing_count' => $project->liveListingCount(),
                    'price_from' => $project->price_from,
                    'tagline' => $project->tagline,
                    'sort_order' => $project->sort_order,
                    'is_active' => $project->is_active,
                    'route_edit' => route('admin.projects.edit', $project->id),
                    'route_toggle' => route('admin.projects.toggle', $project->id),
                    'route_delete' => route('admin.projects.destroy', $project->id),
                    'route_public' => route('rent.list', ['project' => $project->slug]),
                ];
            });

            return response()->json(['data' => $data]);
        }

        return view('projects.list-projects');
    }

    public function create()
    {
        return view('projects.edit', ['project' => new Project()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        unset($data['highlights_text'], $data['image_file']);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['label']);
        $data['highlights'] = $this->parseHighlights($request->input('highlights_text'));
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['listing_count'] = (int) ($data['listing_count'] ?? 0);

        if ($request->hasFile('image_file')) {
            $data['image'] = $this->storeImage($request->file('image_file'));
        }

        $project = Project::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã tạo dự án.', 'id' => $project->id]);
        }

        return redirect()->route('admin.projects.index')->with('success', 'Đã tạo dự án.');
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $data = $this->validated($request);
        unset($data['highlights_text'], $data['image_file']);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['label'], $project->id);
        $data['highlights'] = $this->parseHighlights($request->input('highlights_text'));
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['listing_count'] = (int) ($data['listing_count'] ?? 0);

        if ($request->hasFile('image_file')) {
            $data['image'] = $this->storeImage($request->file('image_file'));
        }

        $project->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã cập nhật dự án.']);
        }

        return redirect()->route('admin.projects.index')->with('success', 'Đã cập nhật dự án.');
    }

    public function toggle($id)
    {
        $project = Project::findOrFail($id);
        $project->is_active = ! $project->is_active;
        $project->save();

        return response()->json([
            'success' => true,
            'is_active' => $project->is_active,
            'message' => $project->is_active ? 'Đã hiện dự án.' : 'Đã ẩn dự án.',
        ]);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa dự án.']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9\-]*$/'],
            'district' => 'nullable|string|max:255',
            'listing_count' => 'nullable|integer|min:0',
            'price_from' => 'nullable|string|max:100',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'highlights_text' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ]);
    }

    private function parseHighlights(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $text) ?: [])
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'du-an';
        $slug = $base;
        $i = 2;
        while (
            Project::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    private function storeImage($file): string
    {
        $filename = uniqid('project_').'.'.$file->getClientOriginalExtension();
        $file->move(public_path('AnhDuAn'), $filename);

        return '/AnhDuAn/'.$filename;
    }
}
