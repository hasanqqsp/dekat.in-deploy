<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Kopi Jakarta',
                'category' => 'Cafe',
                'coords' => [-6.201, 106.820],
                'image' => '/assets/content/prolog-kopi.jpg',
                'alamat_lengkap' => 'Jl. Thamrin No.5, Jakarta',
                'open_hour' => '08:00',
                'close_hour' => '22:00',
                'start_price' => 15000,
                'end_price' => 50000,
            ],
            [
                'name' => 'Cafe Santuy',
                'category' => 'Cafe',
                'coords' => [-6.210, 106.832],
                'image' => '/assets/content/prolog-kopi.jpg',
                'alamat_lengkap' => 'Jl. Sudirman No.10, Jakarta',
                'open_hour' => '09:00',
                'close_hour' => '23:00',
                'start_price' => 20000,
                'end_price' => 60000,
            ],
            // Add the remaining locations here...
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
