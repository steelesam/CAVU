<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_booking()
    {
        // Given a user is authenticated
        $user = User::factory()->create();
        $this->actingAs($user);

        // When the user submits a request to create a booking
        $response = $this->post(route('create-booking'), [
            'from' => '2024-02-01',
            'to' => '2024-02-03',
        ]);

        // Then the booking should be created in the database
        $response->assertStatus(200);
        $this->assertDatabaseHas('bookings', [
            'from' => '2024-02-01',
            'to' => '2024-02-03',
            'user_id' => $user->id,
        ]);

        $response->assertJsonStructure(['booking']);
    }

    /** @test */
    public function a_user_can_cancel_a_booking()
    {
        // Given a user is authenticated
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a booking for the user
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        // When the user submits a request to cancel the booking
        $response = $this->delete(route('cancel-booking', ['id' => $booking->id]));

        // Then the booking should be cancelled in the database
        $response->assertStatus(200);
        $this->assertSoftDeleted('bookings', [
            'id' => $booking->id,
        ]);

        // Ensure associated parking space is made available again
        $this->assertDatabaseHas('parking_spaces', [
            'id' => $booking->parking_space_id,
            'available' => 1,
        ]);

        $response->assertJson(['message' => 'Booking cancelled successfully']);
    }

    /** @test */
    public function a_user_can_amend_a_booking()
    {
        // Given a user is authenticated
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a booking for the user
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        // When the user submits a request to amend the booking
        $response = $this->put(route('amend-booking', ['id' => $booking->id]), [
            'from' => '2024-02-05',
            'to' => '2024-02-07',
        ]);

        // Then the booking should be amended in the database
        $response->assertStatus(200);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'from' => '2024-02-05',
            'to' => '2024-02-07',
        ]);

        $response->assertJsonStructure(['booking']);
    }
}
