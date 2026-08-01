<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'slug' => 'muong-thanh',
                'label' => 'Mường Thanh',
                'image' => '/AnhDuAn/cover_6a6b54aca0831.png',
                'district' => 'Lộc Thọ / Trần Phú',
                'listing_count' => 32,
                'price_from' => '8 triệu/tháng',
                'tagline' => 'Căn hộ trung tâm, gần biển',
                'description' => 'Cụm căn hộ Mường Thanh tại trung tâm Nha Trang — tiện nghi đầy đủ, phù hợp khách thuê dài hạn và chuyên gia.',
                'highlights' => ['Gần biển & trung tâm', 'Nội thất cơ bản / full', 'An ninh 24/7'],
                'sort_order' => 1,
            ],
            [
                'slug' => 'vinpearl',
                'label' => 'Vinpearl',
                'image' => '/AnhDuAn/cover_6a6b58892d71c.jpg',
                'district' => 'Đảo Hòn Tre / ven biển',
                'listing_count' => 18,
                'price_from' => '15 triệu/tháng',
                'tagline' => 'Resort sống — view biển cao cấp',
                'description' => 'Phân khúc căn hộ / villa gắn với hệ sinh thái Vinpearl — hướng tới khách thuê cao cấp, lưu trú dài ngày.',
                'highlights' => ['View biển / resort', 'Tiện ích nội khu', 'Phù hợp chuyên gia nước ngoài'],
                'sort_order' => 2,
            ],
            [
                'slug' => 'sun-group',
                'label' => 'Sun Group',
                'image' => '/AnhDuAn/cover_6a6b5864e1e31.png',
                'district' => 'Nha Trang / Bãi Dài',
                'listing_count' => 24,
                'price_from' => '10 triệu/tháng',
                'tagline' => 'Chuẩn hóa theo dự án Sun',
                'description' => 'Các căn thuộc hệ sinh thái Sun Group — thiết kế hiện đại, vận hành rõ ràng, dễ cho thuê và quản lý.',
                'highlights' => ['Thiết kế hiện đại', 'Quản lý chuyên nghiệp', 'Đa dạng diện tích'],
                'sort_order' => 3,
            ],
            [
                'slug' => 'scenia-bay',
                'label' => 'Scenia Bay',
                'image' => '/AnhDuAn/cover_6a682bee4415a.jpg',
                'district' => 'Trần Phú, Nha Trang',
                'listing_count' => 15,
                'price_from' => '11 triệu/tháng',
                'tagline' => 'Căn hộ mặt tiền biển Trần Phú',
                'description' => 'Scenia Bay nằm trên trục Trần Phú — view biển trực diện, phù hợp khách thuê muốn sống gần phố đi bộ và bãi biển.',
                'highlights' => ['Mặt tiền biển', '2–3 PN phổ biến', 'Full nội thất'],
                'sort_order' => 4,
            ],
            [
                'slug' => 'gold-coast',
                'label' => 'Gold Coast',
                'image' => '/AnhDuAn/media_6a6b54aca5331.png',
                'district' => 'Trần Phú, Nha Trang',
                'listing_count' => 9,
                'price_from' => '13 triệu/tháng',
                'tagline' => 'Căn hộ biển cao tầng',
                'description' => 'Gold Coast — lựa chọn căn hộ biển với tầm nhìn mở, tiện ích nội khu và vị trí thuận tiện di chuyển trung tâm.',
                'highlights' => ['Tầng cao view đẹp', 'Tiện ích đầy đủ', 'Gần trung tâm'],
                'sort_order' => 5,
            ],
        ];

        foreach ($rows as $row) {
            Project::updateOrCreate(
                ['slug' => $row['slug']],
                array_merge($row, ['is_active' => true])
            );
        }
    }
}
