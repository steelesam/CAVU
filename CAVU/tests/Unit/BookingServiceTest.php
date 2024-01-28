<?php

use App\Models\Booking;
use App\Models\User;
use App\Services\BookingService;
use App\Services\ParkingSpaceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

/**
 * Class BookingServiceTest
 */
class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BookingService $bookingService;

    /**
     * Set up the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->bookingService = new BookingService(new ParkingSpaceService());
    }

    /**
     * Test creating a booking for the user.
     */
    public function testCreateBooking()
    {
        $user = User::factory()->create();
        $bookingData = [
            'from' => '2024-01-01',
            'to' => '2024-01-03',
        ];

        $booking = $this->bookingService->createBooking($bookingData, $user);

        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'from' => '2024-01-01',
            'to' => '2024-01-03',
            'user_id' => $user->id,
            'parking_space_id' => $booking->parking_space_id,
        ]);
    }

    /**
     * Test amending the details of an existing booking.
     */
    public function testAmendBooking()
    {
        $booking = Booking::factory()->create();
        $amendData = [
            'from' => '2024-01-10',
            'to' => '2024-01-15',
        ];

        $response = $this->bookingService->amendBooking($booking->id, $amendData);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'from' => '2024-01-10',
            'to' => '2024-01-15',
        ]);
    }

    /**
     * Test canceling an existing booking.
     */
    public function testCancelBooking()
    {
        $booking = Booking::factory()->create();
        $bookingId = $booking->id;

        $response = $this->bookingService->cancelBooking($bookingId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSoftDeleted('bookings', ['id' => $bookingId]);
    }
}
