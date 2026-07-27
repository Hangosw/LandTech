<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Show the home page
     */
    public function home(): View
    {
        $dbProperties = \App\Models\Property::with(['user', 'utilities'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();
            
        $userWishlists = [];
        if (session()->has('user')) {
            $user = session('user');
            $userId = $user['id'] ?? $user->id;
            $userWishlists = \App\Models\Wishlist::where('user_id', $userId)->pluck('property_id')->toArray();
        }
            
        $properties = $dbProperties->map(function ($prop) {
            return [
                'id' => $prop->id,
                'title' => $prop->title,
                'slug' => $prop->slug,
                'type' => $prop->property_type,
                'type_label' => $prop->type_label,
                'price' => $prop->monthly_price ?? $prop->price,
                'price_label' => number_format($prop->monthly_price ?? $prop->price) . ' đ',
                'location' => ($prop->district ? $prop->district . ', ' : '') . 'Nha Trang',
                'area' => $prop->area,
                'bedrooms' => $prop->bedrooms,
                'bathrooms' => $prop->bathrooms,
                'project' => $prop->project,
                'verified' => true,
                'distance_sea' => $prop->distance_to_beach ? $prop->distance_to_beach . 'm tới biển' : null,
                'utilities' => $prop->utilities->map(fn($u) => [
                    'name' => $u->name,
                    'icon' => $u->icon_name,
                ])->toArray(),
                'is_gold_agent' => false,
                'images' => [$prop->cover_image_url ?? '/images/hero-nhatrang.jpg'],
                'description' => $prop->description,
                'agent_name' => $prop->user ? $prop->user->name : 'Môi giới',
            ];
        })->toArray();
        $projects = $this->getProjects();
        
        return view('pages.home', compact('properties', 'projects', 'userWishlists'));
    }
    
    /**
     * Show rental properties list
     */
    public function rentList(\Illuminate\Http\Request $request): View
    {
        $query = \App\Models\Property::with(['user', 'utilities'])
            ->where('status', 'active');
            
        // Filters
        if ($request->filled('q')) {
            $q = strtolower(trim($request->q));
            $query->where(function($sq) use ($q) {
                $sq->where('title', 'like', "%{$q}%")
                   ->orWhere('district', 'like', "%{$q}%")
                   ->orWhereHas('utilities', function($uq) use ($q) {
                       $uq->where('name', 'like', "%{$q}%");
                   });
            });
        }
        
        if ($request->filled('type')) {
            $types = (array) $request->type;
            $query->whereIn('property_type', $types);
        }
        
        if ($request->filled('area')) {
            $query->where('district', $request->area);
        }
        
        if ($request->filled('project')) {
            $query->where('project', $request->project);
        }
        
        if ($request->filled('priceMax')) {
            $max = (int) $request->priceMax;
            if ($max < 30000000) {
                $query->where(function($q) use ($max) {
                    $q->where('monthly_price', '<=', $max)
                      ->orWhere(function($sq) use ($max) {
                          $sq->whereNull('monthly_price')->where('price', '<=', $max);
                      });
                });
            }
        }
        
        if ($request->filled('beds')) {
            $query->where('bedrooms', '>=', (int) $request->beds);
        }
        
        if ($request->filled('amenities')) {
            $amenities = (array) $request->amenities;
            foreach ($amenities as $amenity) {
                $query->whereHas('utilities', function($q) use ($amenity) {
                    $q->where('utilities.id', $amenity);
                });
            }
        }
        
        // Sorting
        $sort = $request->sort;
        if ($sort === 'price-asc') {
            $query->orderByRaw('COALESCE(monthly_price, price) asc');
        } elseif ($sort === 'price-desc') {
            $query->orderByRaw('COALESCE(monthly_price, price) desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $dbProperties = $query->get();
        
        $properties = $dbProperties->map(function ($prop) {
            return [
                'id' => $prop->id,
                'title' => $prop->title,
                'slug' => $prop->slug,
                'type' => $prop->property_type,
                'type_label' => $prop->type_label,
                'price' => $prop->monthly_price ?? $prop->price,
                'price_label' => number_format($prop->monthly_price ?? $prop->price) . ' đ/th',
                'location' => ($prop->district ? $prop->district . ', ' : '') . 'Nha Trang',
                'area' => $prop->area,
                'bedrooms' => $prop->bedrooms,
                'bathrooms' => $prop->bathrooms,
                'project' => $prop->project,
                'verified' => true,
                'distance_sea' => $prop->distance_to_beach ? $prop->distance_to_beach . 'm tới biển' : null,
                'utilities' => $prop->utilities->map(fn($u) => [
                    'name' => $u->name,
                    'icon' => $u->icon_name,
                ])->toArray(),
                'is_gold_agent' => false,
                'images' => [$prop->cover_image_url ?? '/images/hero-nhatrang.jpg'],
                'description' => $prop->description,
                'agent_name' => $prop->user ? $prop->user->name : 'Môi giới',
            ];
        })->toArray();
        
        // Filter options mapping what the React page used
        $types = [
            (object)['slug' => 'apartment', 'label' => 'Căn hộ'],
            (object)['slug' => 'house', 'label' => 'Nhà phố'],
            (object)['slug' => 'villa', 'label' => 'Biệt thự'],
            (object)['slug' => 'office', 'label' => 'Văn phòng'],
            (object)['slug' => 'commercial', 'label' => 'Mặt bằng KD'],
        ];
        
        $areas = [
            (object)['slug' => 'Lộc Thọ', 'label' => 'Lộc Thọ'],
            (object)['slug' => 'Phước Hải', 'label' => 'Phước Hải'],
            (object)['slug' => 'Phước Long', 'label' => 'Phước Long'],
            (object)['slug' => 'Vĩnh Hòa', 'label' => 'Vĩnh Hòa'],
            (object)['slug' => 'Vĩnh Nguyên', 'label' => 'Vĩnh Nguyên'],
            (object)['slug' => 'Vĩnh Trường', 'label' => 'Vĩnh Trường'],
            (object)['slug' => 'Tân Lập', 'label' => 'Tân Lập'],
        ];
        
        $projects = json_decode(json_encode($this->getProjects()));
        $amenitiesList = \App\Models\Utility::where('is_active', true)->orderBy('sort_order')->get();
        
        $userWishlists = [];
        if (session()->has('user')) {
            $user = session('user');
            $userId = $user['id'] ?? $user->id;
            $userWishlists = \App\Models\Wishlist::where('user_id', $userId)->pluck('property_id')->toArray();
        }

        if ($request->ajax()) {
            return view('partials.rent-list-results', compact('properties', 'userWishlists'));
        }

        return view('pages.rent-list', compact('properties', 'types', 'areas', 'projects', 'amenitiesList', 'userWishlists'));
    }
    
    /**
     * Show rental property details
     */
    public function rentDetail(string $slug, \Illuminate\Http\Request $request): View
    {
        $property = \App\Models\Property::with(['user', 'media', 'utilities'])->where('slug', $slug)->firstOrFail();
        
        try {
            $inserted = \App\Models\PropertyView::insertOrIgnore([
                'property_id' => $property->id,
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($inserted) {
                $property->increment('view_count');
            }
        } catch (\Exception $e) {
            // Ignore in case of concurrent unique constraint violation
        }
        
        // Similar properties: same type, excluding current, max 4
        $similar = \App\Models\Property::with(['utilities'])
            ->where('status', 'active')
            ->where('property_type', $property->property_type)
            ->where('id', '!=', $property->id)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $userBooking = null;
        if (session()->has('last_booking_' . $property->id)) {
            $userBooking = \App\Models\Booking::find(session('last_booking_' . $property->id));
        } elseif (session()->has('user') && !empty(session('user')->phone)) {
            $userBooking = \App\Models\Booking::where('property_id', $property->id)
                ->where('renter_phone', session('user')->phone)
                ->latest('id')
                ->first();
        }

        return view('pages.rent-detail', compact('property', 'similar', 'userBooking'));
    }
    
    /**
     * Show post property page
     */
    public function postProperty()
    {
        if (!session()->has('user')) {
            session()->put('url.intended', url()->current());
            return redirect()->route('login');
        }
        
        $utilities = \App\Models\Utility::where('is_active', true)->orderBy('sort_order')->get();
        return view('pages.post-property', compact('utilities'));
    }
    
    public function myProperties(\Illuminate\Http\Request $request): View|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        if (!session()->has('user')) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem trang này.');
        }

        $user = session('user');
        $userId = $user['id'] ?? $user->id;

        $properties = \App\Models\Property::where('user_id', $userId)->with('media')->latest()->get();

        if ($request->ajax()) {
            $data = $properties->map(function($property) {
                $imageUrl = '';
                if ($property->media && $property->media->count() > 0) {
                    $imageUrl = $property->media->first()->file_url;
                } elseif ($property->cover_image_url) {
                    $imageUrl = $property->cover_image_url;
                }
                
                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'slug' => $property->slug,
                    'address' => \Illuminate\Support\Str::limit($property->address, 30),
                    'image' => $imageUrl,
                    'status' => $property->status,
                    'created_at' => $property->created_at->format('d/m/Y'),
                    'bedrooms' => $property->bedrooms ?? 0,
                    'bathrooms' => $property->bathrooms ?? 0,
                    'area' => $property->area ?? 0,
                    'price' => number_format($property->price, 0, ',', '.'),
                    'transaction_type' => $property->transaction_type,
                    'route_detail' => route('rent.detail', $property->slug)
                ];
            });
            return response()->json(['data' => $data]);
        }

        return view('pages.my-properties');
    }

    public function wishlist(): View
    {
        $properties = [];

        if (session()->has('user')) {
            $user = session('user');
            $userId = $user['id'] ?? $user->id;

            $wishlists = \App\Models\Wishlist::where('user_id', $userId)->pluck('property_id')->toArray();
            $properties = \App\Models\Property::whereIn('id', $wishlists)->with('media')->latest()->get();
        }

        return view('pages.wishlist', compact('properties'));
    }
    
    /**
     * Show agents page
     */
    public function agents(): View
    {
        $agentsData = [
            (object)['name' => 'Nguyễn Minh Tuấn', 'avatar' => 'https://ui-avatars.com/api/?name=Nguyen+Minh+Tuan&background=random', 'tier' => 'gold', 'listings' => 24],
            (object)['name' => 'Trần Lan Ngọc', 'avatar' => 'https://ui-avatars.com/api/?name=Tran+Lan+Ngoc&background=random', 'tier' => 'gold', 'listings' => 18],
            (object)['name' => 'Lê Hoàng Vũ', 'avatar' => 'https://ui-avatars.com/api/?name=Le+Hoang+Vu&background=random', 'tier' => 'gold', 'listings' => 15],
            (object)['name' => 'Phạm Thị Hương', 'avatar' => 'https://ui-avatars.com/api/?name=Pham+Thi+Huong&background=random', 'tier' => 'silver', 'listings' => 12],
            (object)['name' => 'Hoàng Ngọc Dũng', 'avatar' => 'https://ui-avatars.com/api/?name=Hoang+Ngoc+Dung&background=random', 'tier' => 'silver', 'listings' => 8],
            (object)['name' => 'Vũ Đức Phát', 'avatar' => 'https://ui-avatars.com/api/?name=Vu+Duc+Phat&background=random', 'tier' => 'silver', 'listings' => 5],
        ];

        $topAgents = collect($agentsData)->map(function ($a, $i) {
            $a->views = 1200 - $i * 130 + $a->listings * 40;
            $a->bookings = 60 - $i * 7;
            return $a;
        })->sortByDesc('views')->values()->all();

        return view('pages.agents', compact('topAgents'));
    }
    
    /**
     * Show projects page
     */
    public function projects(): View
    {
        $projects = $this->getProjects();
        return view('pages.projects', compact('projects'));
    }
    
    /**
     * Get properties data
     */
    private function getProperties(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Nhà phố nguyên căn 2 tầng Vĩnh Hòa',
                'slug' => 'nha-pho-nguyen-can-2-tang-vinh-hoa',
                'type' => 'house',
                'type_label' => 'Nhà phố',
                'price_label' => '8,5 triệu/tháng',
                'price' => 8500000,
                'location' => 'Vĩnh Hòa, Nha Trang',
                'area' => 57,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'project' => 'muong-thanh',
                'verified' => true,
                'distance_sea' => '370m tới biển',
                'tags' => ['Yên tĩnh', 'Phòng gym'],
                'is_gold_agent' => false,
                'images' => ['https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80'],
                'description' => 'Nhà phố nguyên căn sạch sẽ, thoáng mát, khu dân cư an ninh tại Vĩnh Hòa.',
            ],
            [
                'id' => 2,
                'title' => 'Văn phòng hạng A trung tâm thành phố',
                'slug' => 'van-phong-hang-a-trung-tam-thanh-pho',
                'type' => 'office',
                'type_label' => 'Văn phòng',
                'price_label' => '11 triệu/tháng',
                'price' => 11000000,
                'location' => 'Phước Hải, Nha Trang',
                'area' => 69,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'project' => 'vinpearl',
                'verified' => true,
                'distance_sea' => '590m tới biển',
                'tags' => ['Wifi mạnh', 'Hồ bơi'],
                'is_gold_agent' => false,
                'images' => ['https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=600&q=80'],
                'description' => 'Văn phòng làm việc sang trọng đầy đủ tiện ích hiện đại.',
            ],
            [
                'id' => 3,
                'title' => 'Mặt bằng kinh doanh mặt tiền Vĩnh Nguyên',
                'slug' => 'mat-bang-kinh-doanh-mat-tien-vinh-nguyen',
                'type' => 'commercial',
                'type_label' => 'Mặt bằng KD',
                'price_label' => '13,5 triệu/tháng',
                'price' => 13500000,
                'location' => 'Vĩnh Nguyên, Nha Trang',
                'area' => 81,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'project' => 'sun-group',
                'verified' => true,
                'distance_sea' => '810m tới biển',
                'tags' => ['View biển', 'Chỗ đậu xe'],
                'is_gold_agent' => true,
                'images' => ['https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=600&q=80'],
                'description' => 'Mặt bằng thương mại ngay mặt đường kinh doanh sầm uất tại Vĩnh Nguyên.',
            ],
            [
                'id' => 4,
                'title' => 'Nhà phố nguyên căn 3 tầng Tân Lập',
                'slug' => 'nha-pho-nguyen-can-3-tang-tan-lap',
                'type' => 'house',
                'type_label' => 'Nhà phố',
                'price_label' => '18,5 triệu/tháng',
                'price' => 18500000,
                'location' => 'Tân Lập, Nha Trang',
                'area' => 105,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'project' => 'gold-coast',
                'verified' => true,
                'distance_sea' => '1.3km tới biển',
                'tags' => ['Wifi mạnh', 'Hồ bơi'],
                'is_gold_agent' => false,
                'images' => ['https://images.unsplash.com/photo-1605276374104-dee2a0ed3cd6?auto=format&fit=crop&w=600&q=80'],
                'description' => 'Nhà phố 3 tầng rộng rãi, thiết kế hiện đại thích hợp hộ gia đình thuê lâu dài.',
            ],
            [
                'id' => 5,
                'title' => 'Căn hộ Penthouse view vịnh Nha Trang',
                'slug' => 'can-ho-penthouse-view-vinh-nha-trang',
                'type' => 'apartment',
                'type_label' => 'Căn hộ',
                'price_label' => '25 triệu/tháng',
                'price' => 25000000,
                'location' => 'Lộc Thọ, Nha Trang',
                'area' => 150,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'project' => 'scenia-bay',
                'verified' => true,
                'distance_sea' => '100m tới biển',
                'tags' => ['Sang trọng', 'Bồn tắm'],
                'is_gold_agent' => true,
                'images' => ['https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=600&q=80'],
                'description' => 'Căn hộ Penthouse đẳng cấp 5 sao view trực diện biển Trần Phú.',
            ],
            [
                'id' => 6,
                'title' => 'Biệt thự nghỉ dưỡng cao cấp Anh Nguyễn',
                'slug' => 'biet-thu-nghi-duong-cao-cap-anh-nguyen',
                'type' => 'villa',
                'type_label' => 'Biệt thự',
                'price_label' => '45 triệu/tháng',
                'price' => 45000000,
                'location' => 'Vĩnh Trường, Nha Trang',
                'area' => 320,
                'bedrooms' => 4,
                'bathrooms' => 5,
                'project' => 'vinpearl',
                'verified' => true,
                'distance_sea' => '150m tới biển',
                'tags' => ['Sân vườn', 'Hồ bơi riêng'],
                'is_gold_agent' => true,
                'images' => ['https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=600&q=80'],
                'description' => 'Biệt thự đồi biển Anh Nguyễn có hồ bơi tràn bờ biệt lập.',
            ],
            [
                'id' => 7,
                'title' => 'Căn hộ Studio tiện nghi phố Tây Hùng Vương',
                'slug' => 'can-ho-studio-tien-nghi-pho-tay-hung-vuong',
                'type' => 'apartment',
                'type_label' => 'Căn hộ',
                'price_label' => '6 triệu/tháng',
                'price' => 6000000,
                'location' => 'Lộc Thọ, Nha Trang',
                'area' => 35,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'project' => 'sun-group',
                'verified' => false,
                'distance_sea' => '450m tới biển',
                'tags' => ['Trung tâm', 'Giá tốt'],
                'is_gold_agent' => false,
                'images' => ['https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=600&q=80'],
                'description' => 'Căn hộ studio đầy đủ đồ đạc, gần biển và khu ăn uống sầm uất.',
            ],
            [
                'id' => 8,
                'title' => 'Nhà phố khu đô thị mới VCN Phước Long',
                'slug' => 'nha-pho-khu-do-thi-moi-vcn-phuoc-long',
                'type' => 'house',
                'type_label' => 'Nhà phố',
                'price_label' => '15 triệu/tháng',
                'price' => 15000000,
                'location' => 'Phước Long, Nha Trang',
                'area' => 90,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'project' => 'muong-thanh',
                'verified' => true,
                'distance_sea' => '2.5km tới biển',
                'tags' => ['An ninh 24/7', 'Đường rộng'],
                'is_gold_agent' => false,
                'images' => ['https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=600&q=80'],
                'description' => 'Nhà mới hoàn thiện khu đô thị VCN Phước Long thoáng mát văn minh.',
            ],
        ];
    }

    /**
     * Get projects data
     */
    private function getProjects(): array
    {
        return [
            [
                'slug' => 'muong-thanh',
                'label' => 'Mường Thanh',
                'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'slug' => 'vinpearl',
                'label' => 'Vinpearl',
                'image' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'slug' => 'sun-group',
                'label' => 'Sun Group',
                'image' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'slug' => 'scenia-bay',
                'label' => 'Scenia Bay',
                'image' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'slug' => 'gold-coast',
                'label' => 'Gold Coast',
                'image' => 'https://images.unsplash.com/photo-1605276374104-dee2a0ed3cd6?auto=format&fit=crop&w=600&q=80'
            ],
        ];
    }
}
