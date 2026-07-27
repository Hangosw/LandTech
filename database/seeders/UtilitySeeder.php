<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utility;

class UtilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $utilities = [
            ['name' => 'View biển', 'category' => 'location', 'icon_name' => 'fas fa-water'],
            ['name' => 'Wifi mạnh', 'category' => 'amenity', 'icon_name' => 'fas fa-wifi'],
            ['name' => 'Yên tĩnh', 'category' => 'amenity', 'icon_name' => 'fas fa-volume-mute'],
            ['name' => 'Chỗ đậu xe', 'category' => 'facility', 'icon_name' => 'fas fa-parking'],
            ['name' => 'Hồ bơi', 'category' => 'facility', 'icon_name' => 'fas fa-swimming-pool'],
            ['name' => 'Phòng gym', 'category' => 'facility', 'icon_name' => 'fas fa-dumbbell'],
            ['name' => 'Thang máy', 'category' => 'facility', 'icon_name' => 'fas fa-elevator'],
            ['name' => 'Bếp đầy đủ', 'category' => 'amenity', 'icon_name' => 'fas fa-utensils'],
        ];

        foreach ($utilities as $index => $utility) {
            Utility::firstOrCreate(
                ['name' => $utility['name']],
                [
                    'category' => $utility['category'],
                    'icon_name' => $utility['icon_name'],
                    'is_active' => true,
                    'sort_order' => $index,
                ]
            );
        }
    }
}
