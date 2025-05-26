<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ratings')->insert([
            [
                'user_id' => 1,
                'location_id' => 1,
                'rating' => 5,
                'comment' => 'Amazing place!',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'location_id' => 1,
                'rating' => 4,
                'comment' => 'Great experience!',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'location_id' => 2,
                'rating' => 3,
                'comment' => 'It was okay.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}