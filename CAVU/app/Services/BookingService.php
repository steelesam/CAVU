<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class BookingService
{
    protected $parkingSpaceService;

    /**
     * Constructor for the BookingService class.
     */
    public function __construct(ParkingSpaceService $parkingSpaceService)
    {
        $this->parkingSpaceService = $parkingSpaceService;
    }

    /**
     * Creates a booking for the user.
     *
     * @param  array  $data  The data for creating the booking.
     * @param  User  $user  The user for whom the booking is being created.
     * @return Booking The created Booking instance.
     *
     * @throws NoAvailableParkingSpacesException
     */
    public function createBooking(array $data, User $user): Booking
    {
        // $availableSpacesCount = $this->parkingSpaceService->checkParkingSpaceAvailability($data['from'], $data['to']);

        //if ($availableSpacesCount < 10) {
        try {
            $booking = new Booking($data);
            $booking->user()->associate($user);

            // Set parking space later in associateParkingSpace
            $this->parkingSpaceService->associateParkingSpace($booking);

            $booking->save();

            return $booking;
        } catch (\Exception $exception) {
            throw $exception;
        }
        //}
    }

    /**
     * Amends the details of an existing booking.
     *
     * @param  int  $bookingId  The ID of the booking to be amended.
     * @param  array  $data  The data containing the amendments to be applied to the booking.
     * @return JsonResponse JSON response containing the amended booking details.
     */
    public function amendBooking(int $bookingId, array $data): JsonResponse
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->update($data);

        return response()->json(['booking' => $booking]);
    }

    /**
     * Cancels an existing booking.
     *
     * @param  int  $bookingId  The ID of the booking to be canceled.
     * @return JsonResponse JSON response indicating the success of the cancellation.
     */
    public function cancelBooking(int $bookingId): JsonResponse
    {
        $booking = Booking::with('parkingSpace')->findOrFail($bookingId);

        // Soft deletes being performed so we can still view cancelled bookings after the cancellation has occured, useful for analytics, customer srevice etc
        $booking->delete();
    
        // Make the associated parking space available again
        if ($booking->parkingSpace) {
            $booking->parkingSpace->update(['available' => 1]);
        }

        return response()->json(['message' => 'Booking cancelled successfully']);
    }

    /**
     * Get all bookings by a customer.
     *
     * @return JsonResponse JSON response containing the user details and their bookings.
     */
    public function getUserBookings(): JsonResponse
    {
        $user = auth()->user();
        $bookings = $user->bookings;

        return response()->json(['user' => $user, 'bookings' => $bookings]);
    }
}
