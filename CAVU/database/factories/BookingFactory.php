<?php

namespace Database\Factories;

use App\Models\ParkingSpace;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from' => $this->faker->dateTime(),
            'to' => $this->faker->dateTime(),
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'parking_space_id' => function () {
                return ParkingSpace::factory()->create()->id;
            },
        ];
    }
}
