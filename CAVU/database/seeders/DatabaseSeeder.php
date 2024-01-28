<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\BookingsTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\ParkingSpacesTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BookingsTableSeeder::class,
            UsersTableSeeder::class,
            ParkingSpacesTableSeeder::class,
        ]);
    }
}
