<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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
     * @throws Exception
     */
    public function createBooking(array $data, User $user): Booking
    {
        try {
            $booking = new Booking($data);
            $booking->user()->associate($user);
            $this->parkingSpaceService->associateParkingSpace($booking);
            $booking->save();

            return $booking;

        } catch (\Exception $exception) {
            Log::error('Error creating booking: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Amends the details of an existing booking.
     *
     * @param  int  $bookingId  The ID of the booking to be amended.
     * @param  array  $data  The data containing the amendments to be applied to the booking.
     * @return JsonResponse JSON response containing the amended booking details or an error message.
     *
     * @throws Exception
     */
    public function amendBooking(int $bookingId, array $data): JsonResponse
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            $availableSpacesCount = $this->parkingSpaceService->checkParkingSpaceAvailability($data['from'], $data['to']);

            if ($availableSpacesCount < 1) {
                return response()->json(['error' => 'Not enough available spaces for the amended dates.']);
            }
            $booking->update($data);

            return response()->json(['booking' => $booking]);

        } catch (\Exception $exception) {
            Log::error('Error amending booking: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Cancels an existing booking.
     *
     * @param  int  $bookingId  The ID of the booking to be canceled.
     * @return JsonResponse JSON response indicating the success of the cancellation.
     *
     * @throws Exception
     */
    public function cancelBooking(int $bookingId): JsonResponse
    {
        try {
            $booking = Booking::with('parkingSpace')->findOrFail($bookingId);

            // Soft deletes being performed so we can still view cancelled bookings after the cancellation has occurred, useful for analytics, customer service, etc.
            $booking->delete();

            // Make the associated parking space available again
            if ($booking->parkingSpace) {
                $booking->parkingSpace->update(['available' => 1]);
            }

            return response()->json(['message' => 'Booking canceled successfully']);

        } catch (\Exception $exception) {
            Log::error('Error canceling booking: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Get all bookings by a customer.
     *
     * @return JsonResponse JSON response containing the user details and their bookings.
     *
     * @throws Exception
     */
    public function getUserBookings(): JsonResponse
    {
        try {
            $user = auth()->user();
            $bookings = $user->bookings;

            return response()->json(['user' => $user, 'bookings' => $bookings]);

        } catch (\Exception $exception) {
            Log::error('Error retrieving user bookings: '.$exception->getMessage());
            throw $exception;
        }
    }
}
