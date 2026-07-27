<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropertiesController extends Controller
{
    public function index(Request $request)
    {
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

    public function edit($id)
    {
        $property = \App\Models\Property::with('media')->findOrFail($id);
        return view('properties.edit', compact('property'));
    }

    public function update(Request $request, $id)
    {
        $property = \App\Models\Property::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'property_type' => 'required|string',
            'transaction_type' => 'required|string',
            'district' => 'required|string',
            'address' => 'required|string',
            'price' => 'required|numeric',
            'area' => 'required|numeric',
            'status' => 'required|string|in:active,pending,sold,rented,hidden,draft',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240'
        ]);

        $data = $request->except('cover_image');

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = uniqid('cover_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('AnhDuAn'), $filename);
            $data['cover_image_url'] = '/AnhDuAn/' . $filename;
        }

        $property->update($data);

        return redirect()->route('admin.properties.index')->with('success', 'Đã cập nhật bài đăng thành công!');
    }

    public function updateStatus(Request $request, $id)
    {
        $property = \App\Models\Property::findOrFail($id);
        
        $request->validate([
            'status' => 'required|string|in:active,pending,sold,rented,hidden,draft',
        ]);

        $property->update(['status' => $request->status]);

        return redirect()->route('admin.properties.index')->with('success', 'Đã thay đổi trạng thái bài đăng!');
    }

    public function destroy($id)
    {
        $property = \App\Models\Property::withoutGlobalScope('active')->findOrFail($id);
        
        // Kiểm tra xem có dữ liệu liên quan không
        $hasRelatedData = $property->bookings()->exists() || 
                          $property->contacts()->exists() || 
                          $property->transactions()->exists() || 
                          $property->rentals()->exists();

        if ($hasRelatedData) {
            // Ẩn bài đăng (Soft delete logic của hệ thống)
            $property->update(['active' => 0]);
        } else {
            // Xóa thẳng (Hard delete) vì chưa có dữ liệu quan trọng liên quan
            $property->media()->delete();
            $property->utilities()->detach();
            $property->calendar()->delete();
            $property->delete();
        }

        return response()->json(['success' => true, 'message' => 'Đã xóa bài đăng thành công!']);
    }
}
