<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\Utility;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class SamplePropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN');

        // Ensure at least one user
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Ensure utilities exist
        if (Utility::count() === 0) {
            $this->call(UtilitySeeder::class);
        }
        $utilities = Utility::all();

        $districts = ['Phường 1', 'Phường 2', 'Phường Thắng Tam', 'Phường 8', 'Phường 10', 'Phường Thắng Nhất'];
        $propertyTypes = ['apartment', 'house', 'villa', 'land'];
        $transactionTypes = ['buy_sell', 'rent'];
        $statuses = ['active', 'sold', 'rented'];

        for ($i = 0; $i < 15; $i++) {
            $title = $faker->sentence(6, true);
            $transactionType = $faker->randomElement($transactionTypes);
            $price = $faker->numberBetween(1000000000, 20000000000); // 1-20 billion
            $area = $faker->randomFloat(2, 40, 500);
            
            $property = Property::create([
                'user_id' => $user->id,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'description' => $faker->paragraphs(3, true),
                'cover_image_url' => 'https://picsum.photos/800/600?random=' . $faker->unique()->numberBetween(1, 1000),
                'property_type' => $faker->randomElement($propertyTypes),
                'project' => $faker->optional(0.7)->company,
                'address' => $faker->streetAddress,
                'district' => $faker->randomElement($districts),
                'unit_number' => $faker->optional(0.5)->bothify('A##'),
                'lat' => $faker->latitude(10.3, 10.5), // Vung tau lat approx
                'lng' => $faker->longitude(90.0, 99.9), // Reduced to fit decimal(10,8) limit in DB
                'bedrooms' => $faker->numberBetween(1, 5),
                'bathrooms' => $faker->numberBetween(1, 4),
                'area' => $area,
                'price' => $transactionType === 'buy_sell' ? $price : 0, // Using 0 as fallback or handle properly
                'price_per_sqm' => $transactionType === 'buy_sell' ? round($price / $area) : null,
                'monthly_price' => $transactionType === 'rent' ? $faker->numberBetween(5000000, 50000000) : null,
                'min_rent_period' => $transactionType === 'rent' ? $faker->randomElement([1, 3, 6, 12]) : null,
                'transaction_type' => $transactionType,
                'distance_to_beach' => $faker->numberBetween(50, 5000), // meters
                'status' => $faker->randomElement($statuses),
                'view_count' => $faker->numberBetween(0, 1000),
                'contact_count' => $faker->numberBetween(0, 50),
            ]);

            // Add 3-5 media items
            $mediaCount = random_int(3, 5);
            for ($j = 0; $j < $mediaCount; $j++) {
                PropertyMedia::create([
                    'property_id' => $property->id,
                    'media_type' => 'image',
                    'file_url' => 'https://picsum.photos/800/600?random=' . $faker->unique()->numberBetween(1001, 5000),
                    'display_order' => $j,
                ]);
            }

            // Attach 2-5 utilities
            if ($utilities->count() > 0) {
                $randomUtilities = $utilities->random(min(random_int(2, 5), $utilities->count()))->pluck('id');
                $property->utilities()->attach($randomUtilities);
            }
        }
    }
}
