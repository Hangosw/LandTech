<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropertiesController extends Controller
{
    private function checkPermission(Request $request)
    {
        $sessionUser = session('user');
        $user = $sessionUser ? \App\Models\User::find($sessionUser->id ?? $sessionUser['id'] ?? null) : auth()->user();
        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để tiếp tục.'], 401);
            }
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        if ($user->user_type !== 'admin' && !$user->hasAnyPermission(['Quản Lý Tin Đăng', 'Quản Lý Bất Động Sản'])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tài khoản của bạn không có quyền Quản Lý Tin Đăng.'], 403);
            }
            abort(403, 'Tài khoản của bạn không có quyền Quản Lý Tin Đăng.');
        }
        return null;
    }

    public function index(Request $request)
    {
        if ($check = $this->checkPermission($request)) return $check;
        // Lấy danh sách các bài đăng (có thể lọc theo user_id nếu cần: where('user_id', auth()->id()))
        $properties = \App\Models\Property::withoutGlobalScope('active')->with(['media', 'user'])->latest()->get();
        
        if ($request->ajax()) {
            $data = $properties->map(function($property) {
                $imageUrl = '';
                if ($property->media && $property->media->count() > 0) {
                    $imageUrl = $property->media->first()->file_url;
                } elseif ($property->cover_image_url) {
                    $imageUrl = $property->cover_image_url;
                }

                $allMedia = [];
                if ($property->cover_image_url) {
                    $allMedia[] = [
                        'url' => $property->cover_image_url,
                        'type' => 'image',
                        'is_cover' => true
                    ];
                }
                
                if ($property->media && $property->media->count() > 0) {
                    foreach($property->media as $m) {
                        $allMedia[] = [
                            'url' => $m->file_url,
                            'type' => $m->media_type,
                            'is_cover' => false
                        ];
                    }
                }

                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'slug' => $property->slug,
                    'address' => $property->address,
                    'district' => $property->district,
                    'project' => $property->project,
                    'property_type' => $property->property_type,
                    'image' => $imageUrl,
                    'all_media' => $allMedia,
                    'bedrooms' => $property->bedrooms ?? 0,
                    'bathrooms' => $property->bathrooms ?? 0,
                    'area' => $property->area ?? 0,
                    'price' => $property->price,
                    'transaction_type' => $property->transaction_type,
                    'status' => $property->status,
                    'user_name' => $property->user->name ?? 'Người dùng ẩn',
                    'user_type' => $property->user->user_type ?? null,
                    'agent_tier' => $property->user->agent_tier ?? null,
                    'route_detail' => route('rent.detail', $property->slug),
                    'route_edit' => route('admin.properties.edit', $property->id),
                    'route_status' => route('admin.properties.status', $property->id),
                    'route_delete' => route('admin.properties.destroy', $property->id),
                    'active' => $property->active,
                ];
            });
            return response()->json(['data' => $data]);
        }
        
        return view('properties.list-properties');
    }

    public function edit(Request $request, $id)
    {
        if ($check = $this->checkPermission($request)) return $check;
        $property = \App\Models\Property::withoutGlobalScope('active')->with(['media', 'revisions.user'])->findOrFail($id);
        $neighbors = $property->detailNeighbors(false);
        $prevProperty = $neighbors['prev'];
        $nextProperty = $neighbors['next'];

        return view('properties.edit', compact('property', 'prevProperty', 'nextProperty'));
    }

    public function update(Request $request, $id)
    {
        if ($check = $this->checkPermission($request)) return $check;
        $property = \App\Models\Property::withoutGlobalScope('active')->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'property_type' => 'required|string',
            'transaction_type' => 'required|string',
            'district' => 'required|string',
            'address' => 'required|string',
            'price' => 'required|numeric',
            'area' => 'required|numeric',
            'status' => 'required|string|in:nhap,choduyet,sansangchothue,dachothue,taman,hethantin,ngungkhaithac,bigovipham',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,png,jpg,webp|max:10240',
            'videos' => 'nullable|array',
            'videos.*' => 'file|mimes:mp4,mov,avi,webm|max:51200',
        ]);

        $data = $request->only([
            'title', 'description', 'property_type', 'transaction_type',
            'district', 'address', 'price', 'area', 'status',
        ]);
        $data['lat'] = $request->filled('lat') ? $request->lat : null;
        $data['lng'] = $request->filled('lng') ? $request->lng : null;
        if ($request->transaction_type === 'rent') {
            $data['monthly_price'] = $request->price;
        }

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = uniqid('cover_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('AnhDuAn'), $filename);
            $data['cover_image_url'] = '/AnhDuAn/' . $filename;
        }

        $property->update($data);

        $keepIds = collect($request->input('existing_media', []))
            ->merge($request->input('existing_videos', []))
            ->filter()
            ->values()
            ->all();

        $newImageIds = $this->storeUploadedMedia($property, $request->file('images', []), 'image');
        $newVideoIds = $this->storeUploadedMedia($property, $request->file('videos', []), 'video');
        $keepIds = array_values(array_unique(array_merge($keepIds, $newImageIds, $newVideoIds)));

        if ($request->has('existing_media') || $request->has('existing_videos') || !empty($newImageIds) || !empty($newVideoIds)) {
            if (!empty($keepIds)) {
                \App\Models\PropertyMedia::where('property_id', $property->id)->whereNotIn('id', $keepIds)->delete();
            } else {
                \App\Models\PropertyMedia::where('property_id', $property->id)->delete();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã cập nhật bài đăng thành công!']);
        }

        return redirect()->route('admin.properties.index')->with('success', 'Đã cập nhật bài đăng thành công!');
    }

    /**
     * @param  array<int, \Illuminate\Http\UploadedFile>|null  $files
     * @return array<int, int>
     */
    private function storeUploadedMedia(\App\Models\Property $property, ?array $files, string $mediaType): array
    {
        $createdIds = [];
        if (empty($files)) {
            return $createdIds;
        }

        $displayOrder = \App\Models\PropertyMedia::where('property_id', $property->id)->max('display_order') ?? 0;

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }
            $displayOrder++;
            $filename = uniqid($mediaType === 'video' ? 'video_' : 'media_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('AnhDuAn'), $filename);

            $media = \App\Models\PropertyMedia::create([
                'property_id' => $property->id,
                'media_type' => $mediaType,
                'file_url' => '/AnhDuAn/' . $filename,
                'display_order' => $displayOrder,
            ]);
            $createdIds[] = $media->id;
        }

        return $createdIds;
    }

    public function updateStatus(Request $request, $id)
    {
        if ($check = $this->checkPermission($request)) return $check;
        $property = \App\Models\Property::findOrFail($id);
        
        $request->validate([
            'status' => 'required|string|in:nhap,choduyet,sansangchothue,dachothue,taman,hethantin,ngungkhaithac,bigovipham',
        ]);

        $property->update(['status' => $request->status]);

        return redirect()->route('admin.properties.index')->with('success', 'Đã thay đổi trạng thái bài đăng!');
    }

    public function destroy(Request $request, $id)
    {
        if ($check = $this->checkPermission($request)) return $check;
        $property = \App\Models\Property::withoutGlobalScope('active')->findOrFail($id);
        
        // Luôn ẩn bài đăng (Soft delete logic của hệ thống)
        $property->update(['active' => 0]);

        return response()->json(['success' => true, 'message' => 'Đã xóa (ẩn) bài đăng thành công!']);
    }
}
