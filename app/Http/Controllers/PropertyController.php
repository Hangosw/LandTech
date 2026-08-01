<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    /**
     * Store a newly created property in storage.
     */
    public function store(Request $request)
    {
        $sessionUser = session('user');
        $userId = $sessionUser ? ($sessionUser['id'] ?? $sessionUser->id) : (Auth::id() ?? 1);

        $draftId = $request->input('draft_id');
        $draftProperty = null;
        if ($draftId) {
            $draftProperty = Property::withoutGlobalScope('active')
                ->where('id', $draftId)
                ->where('user_id', $userId)
                ->first();
        }

        $hasExistingCover = $draftProperty && !empty($draftProperty->cover_image_url);
        $keptExistingImages = collect($request->input('existing_media', []))->filter()->count();
        $hasImages = $keptExistingImages > 0 || $request->hasFile('images');

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'property_type' => 'required|string|in:apartment,house,villa,office,commercial,land,shophouse',
            'district' => 'required|string|max:100',
            'project' => 'nullable|string|max:255',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'area' => 'required|numeric|min:0',
            'monthly_price' => 'required|numeric|min:0',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'utilities' => 'nullable|array',
            'images' => ($hasImages ? 'nullable' : 'required') . '|array|min:' . ($hasImages ? '0' : '1'),
            'images.*' => 'file|mimes:jpeg,png,jpg,webp|max:10240',
            'videos' => 'nullable|array',
            'videos.*' => 'file|mimes:mp4,mov,avi,webm|max:51200',
            'cover_image' => ($hasExistingCover ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Create or update the property
        $property = $draftProperty ?: new Property();
        $property->user_id = $userId;
        $property->title = $request->title;
        if (!$property->slug) {
            $property->slug = Str::slug($request->title) . '-' . uniqid();
        }
        $property->description = $request->description;
        $property->property_type = $request->property_type;
        $property->address = $request->district . ', Nha Trang'; // Simplification
        $property->district = $request->district;
        $property->project = $request->project;
        $property->bedrooms = $request->bedrooms;
        $property->bathrooms = $request->bathrooms;
        $property->area = $request->area;
        $property->monthly_price = $request->monthly_price;
        $property->price = $request->monthly_price; // Assuming monthly price is the main price for rent
        $property->transaction_type = 'rent';
        $property->status = 'choduyet';
        $property->lat = $request->filled('lat') ? $request->lat : null;
        $property->lng = $request->filled('lng') ? $request->lng : null;

        $property->save();

        if ($request->has('utilities')) {
            $property->utilities()->sync($request->utilities);
        }

        // Handle Cover Image
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = uniqid('cover_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('AnhDuAn'), $filename);
            $property->cover_image_url = '/AnhDuAn/' . $filename;
            $property->save(); // Save again to update cover_image_url
        }

        $newImageIds = $this->storeUploadedMedia($property, $request->file('images', []), 'image');
        $newVideoIds = $this->storeUploadedMedia($property, $request->file('videos', []), 'video');

        if ($draftProperty) {
            $keepIds = collect($request->input('existing_media', []))
                ->merge($request->input('existing_videos', []))
                ->merge($newImageIds)
                ->merge($newVideoIds)
                ->filter()
                ->values()
                ->all();
            if (!empty($keepIds)) {
                PropertyMedia::where('property_id', $property->id)->whereNotIn('id', $keepIds)->delete();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Gửi tin thành công!']);
        }

        return redirect()->route('home')->with('success', 'Gửi tin thành công! Tin của bạn đang chờ duyệt.');
    }

    /**
     * Remove the specified property (soft delete by setting active = 0).
     */
    public function destroy(Request $request, $id)
    {
        if (!session()->has('user')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $sessionUser = session('user');
        $userId = $sessionUser['id'] ?? $sessionUser->id;

        $property = Property::where('id', $id)->where('user_id', $userId)->first();

        if (!$property) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài đăng hoặc bạn không có quyền xóa.'], 404);
        }

        $property->active = 0;
        $property->save();

        return response()->json(['success' => true, 'message' => 'Đã xóa bài đăng thành công.']);
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit($id)
    {
        if (!session()->has('user')) {
            session()->put('url.intended', url()->current());
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để sửa tin.');
        }

        $sessionUser = session('user');
        $userId = $sessionUser['id'] ?? $sessionUser->id;

        $property = Property::withoutGlobalScope('active')
            ->with(['media', 'utilities'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
        $utilities = \App\Models\Utility::where('is_active', true)->orderBy('sort_order')->get();
        try {
            $projectOptions = \Illuminate\Support\Facades\Schema::hasTable('projects')
                ? \App\Models\Project::query()->active()->ordered()->get(['slug', 'label'])
                : collect();
        } catch (\Throwable $e) {
            $projectOptions = collect();
        }

        return view('pages.post-property', compact('property', 'utilities', 'projectOptions'));
    }

    /**
     * Update the specified property in storage.
     */
    public function update(Request $request, $id)
    {
        if (!session()->has('user')) {
            return response()->json(['success' => false, 'errors' => ['auth' => 'Bạn cần đăng nhập để sửa tin.']], 401);
        }

        $sessionUser = session('user');
        $userId = $sessionUser['id'] ?? $sessionUser->id;

        $property = Property::withoutGlobalScope('active')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'property_type' => 'required|string|in:apartment,house,villa,office,commercial,land,shophouse',
            'district' => 'required|string|max:100',
            'project' => 'nullable|string|max:255',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'area' => 'required|numeric|min:0',
            'monthly_price' => 'required|numeric|min:0',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'utilities' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,png,jpg,webp|max:10240',
            'videos' => 'nullable|array',
            'videos.*' => 'file|mimes:mp4,mov,avi,webm|max:51200',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $property->title = $request->title;
        if ($property->isDirty('title')) {
            $property->slug = Str::slug($request->title) . '-' . uniqid();
        }
        $property->description = $request->description;
        $property->property_type = $request->property_type;
        $property->address = $request->district . ', Nha Trang';
        $property->district = $request->district;
        $property->project = $request->project;
        $property->bedrooms = $request->bedrooms;
        $property->bathrooms = $request->bathrooms;
        $property->area = $request->area;
        $property->monthly_price = $request->monthly_price;
        $property->price = $request->monthly_price;
        $property->status = 'choduyet'; // Require re-approval on edit
        $property->lat = $request->filled('lat') ? $request->lat : null;
        $property->lng = $request->filled('lng') ? $request->lng : null;

        $property->save();

        // Update utilities
        if ($request->has('utilities')) {
            $property->utilities()->sync($request->utilities);
        } else {
            $property->utilities()->detach();
        }

        // Cover image
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = uniqid('cover_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('AnhDuAn'), $filename);
            $property->cover_image_url = '/AnhDuAn/' . $filename;
            $property->save();
        } elseif ($request->has('remove_cover') && $request->remove_cover == '1') {
             // reserved
        }

        // Upload new media first, then prune removed ones (keeps new uploads)
        $keepIds = collect($request->input('existing_media', []))
            ->merge($request->input('existing_videos', []))
            ->filter()
            ->values()
            ->all();

        $newImageIds = $this->storeUploadedMedia($property, $request->file('images', []), 'image');
        $newVideoIds = $this->storeUploadedMedia($property, $request->file('videos', []), 'video');
        $keepIds = array_values(array_unique(array_merge($keepIds, $newImageIds, $newVideoIds)));

        // JS always sends existing_* arrays on edit submit — sync to that list
        if ($request->has('existing_media') || $request->has('existing_videos') || !empty($newImageIds) || !empty($newVideoIds)) {
            if (!empty($keepIds)) {
                PropertyMedia::where('property_id', $property->id)->whereNotIn('id', $keepIds)->delete();
            } else {
                PropertyMedia::where('property_id', $property->id)->delete();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Cập nhật tin thành công!']);
        }

        return redirect()->route('my-properties')->with('success', 'Cập nhật tin thành công!');
    }

    /**
     * Auto-save draft property (status = 'nhap').
     */
    public function saveDraft(Request $request)
    {
        if (!session()->has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $sessionUser = session('user');
        $userId = is_array($sessionUser) ? ($sessionUser['id'] ?? null) : ($sessionUser->id ?? null);
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $draftId = $request->input('draft_id');
        $property = null;

        if ($draftId) {
            $property = \App\Models\Property::withoutGlobalScope('active')
                ->where('id', $draftId)
                ->where('user_id', $userId)
                ->first();
        }

        if (!$property) {
            $property = \App\Models\Property::withoutGlobalScope('active')
                ->where('user_id', $userId)
                ->where('status', 'nhap')
                ->latest()
                ->first();
        }

        if (!$property) {
            $property = new \App\Models\Property();
            $property->user_id = $userId;
        }

        $title = $request->input('title');
        if (empty($title)) {
            $title = 'Bản nháp tin đăng';
        }

        $property->title = $title;
        $property->slug = \Illuminate\Support\Str::slug($title) . '-' . time() . '-' . rand(100, 999);
        $property->property_type = $request->input('property_type', 'apartment');
        $property->district = $request->input('district', 'Phường Nha Trang');
        $property->address = $request->input('address') ?: ($property->district ? $property->district . ', Nha Trang' : 'Nha Trang');
        $property->project = $request->input('project');
        $property->bedrooms = $request->input('bedrooms', 1);
        $property->bathrooms = $request->input('bathrooms', 1);
        $property->area = $request->input('area');
        $property->monthly_price = $request->input('monthly_price');
        $property->price = $request->input('monthly_price', 0);
        $property->transaction_type = 'rent';
        $property->description = $request->input('description');
        $property->status = 'nhap';
        if ($request->filled('lat') && $request->filled('lng')) {
            $property->lat = $request->input('lat');
            $property->lng = $request->input('lng');
        }

        $property->save();

        if ($request->has('utilities')) {
            $utilities = $request->input('utilities');
            if (is_string($utilities)) {
                $utilities = json_decode($utilities, true);
            }
            if (is_array($utilities)) {
                $property->utilities()->sync($utilities);
            }
        }

        return response()->json([
            'success' => true,
            'draft_id' => $property->id,
            'message' => 'Đã lưu bản nháp'
        ]);
    }

    /**
     * Delete draft property.
     */
    public function deleteDraft($id)
    {
        if (!session()->has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $sessionUser = session('user');
        $userId = is_array($sessionUser) ? ($sessionUser['id'] ?? null) : ($sessionUser->id ?? null);

        $property = \App\Models\Property::withoutGlobalScope('active')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->where('status', 'nhap')
            ->first();

        if ($property) {
            $property->delete();
        }

        return response()->json(['success' => true, 'message' => 'Đã xóa bản nháp']);
    }

    /**
     * Persist uploaded image/video files for a property.
     *
     * @param  array<int, \Illuminate\Http\UploadedFile>|null  $files
     * @return array<int, int>
     */
    private function storeUploadedMedia(Property $property, ?array $files, string $mediaType): array
    {
        $createdIds = [];
        if (empty($files)) {
            return $createdIds;
        }

        $displayOrder = PropertyMedia::where('property_id', $property->id)->max('display_order') ?? 0;

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }
            $displayOrder++;
            $filename = uniqid($mediaType === 'video' ? 'video_' : 'media_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('AnhDuAn'), $filename);

            $media = PropertyMedia::create([
                'property_id' => $property->id,
                'media_type' => $mediaType,
                'file_url' => '/AnhDuAn/' . $filename,
                'display_order' => $displayOrder,
            ]);
            $createdIds[] = $media->id;
        }

        return $createdIds;
    }
}
