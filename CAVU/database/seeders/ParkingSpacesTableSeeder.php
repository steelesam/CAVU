<?php

namespace Database\Seeders;

use App\Models\ParkingSpace;
use Illuminate\Database\Seeder;

class ParkingSpacesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ParkingSpace::factory()->count(10)->create();
    }
}
