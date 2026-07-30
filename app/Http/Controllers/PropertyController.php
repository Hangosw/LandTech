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
        $hasExistingMedia = $draftProperty && ($draftProperty->media()->count() > 0 || $request->has('existing_media'));

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
            'utilities' => 'nullable|array',
            'images' => ($hasExistingMedia ? 'nullable' : 'required') . '|array',
            'images.*' => 'file|mimes:jpeg,png,jpg,webp,mp4,mov,avi|max:51200', // Max 50MB
            'cover_image' => ($hasExistingCover ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,webp|max:10240' // Max 10MB
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

        // Handle Images & Videos
        if ($request->hasFile('images')) {
            $displayOrder = PropertyMedia::where('property_id', $property->id)->max('display_order') ?? 0;
            $displayOrder++;

            foreach ($request->file('images') as $file) {
                $filename = uniqid('media_') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('AnhDuAn'), $filename);
                
                // Determine if it's a video
                $mimeType = $file->getClientMimeType();
                $mediaType = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
                
                PropertyMedia::create([
                    'property_id' => $property->id,
                    'media_type' => $mediaType,
                    'file_url' => '/AnhDuAn/' . $filename,
                    'display_order' => $displayOrder
                ]);
                $displayOrder++;
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

        $property = Property::with(['media', 'utilities'])->where('id', $id)->where('user_id', $userId)->firstOrFail();
        $utilities = \App\Models\Utility::where('is_active', true)->orderBy('sort_order')->get();

        return view('pages.post-property', compact('property', 'utilities'));
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

        $property = Property::where('id', $id)->where('user_id', $userId)->firstOrFail();

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
            'utilities' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,png,jpg,webp,mp4,mov,avi|max:51200', // Max 50MB
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240' // Max 10MB
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

        $property->save();

        // Update utilities
        if ($request->has('utilities')) {
            $property->utilities()->sync($request->utilities);
        } else {
            $property->utilities()->detach();
        }

        // Handle Cover Image
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = uniqid('cover_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('AnhDuAn'), $filename);
            $property->cover_image_url = '/AnhDuAn/' . $filename;
            $property->save();
        } elseif ($request->has('remove_cover') && $request->remove_cover == '1') {
             // If we support removing cover image without replacing
             // $property->cover_image_url = null;
             // $property->save();
        }

        // Handle existing media
        $existingMediaIds = $request->input('existing_media', []);
        
        // Delete media that are not in the existing_media array
        $mediaToDelete = PropertyMedia::where('property_id', $property->id)
            ->whereNotIn('id', $existingMediaIds)
            ->get();
            
        foreach ($mediaToDelete as $media) {
            // Optional: delete file from disk
            // $path = public_path($media->file_url);
            // if(file_exists($path)) { unlink($path); }
            $media->delete();
        }

        // Handle new Images & Videos
        if ($request->hasFile('images')) {
            $displayOrder = PropertyMedia::where('property_id', $property->id)->max('display_order') ?? 0;
            $displayOrder++;
            
            foreach ($request->file('images') as $file) {
                $filename = uniqid('media_') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('AnhDuAn'), $filename);
                
                $mimeType = $file->getClientMimeType();
                $mediaType = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
                
                PropertyMedia::create([
                    'property_id' => $property->id,
                    'media_type' => $mediaType,
                    'file_url' => '/AnhDuAn/' . $filename,
                    'display_order' => $displayOrder
                ]);
                $displayOrder++;
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
        $property->district = $request->input('district', 'Lộc Thọ');
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
}
