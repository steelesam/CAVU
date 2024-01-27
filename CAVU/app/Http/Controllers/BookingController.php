<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;

    /**
     * Constructor for the BookingController.
     *
     * @param  BookingService  $bookingService  An instance of the BookingService.
     */
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Calls BookingService to get all User bookings.
     *
     * @return JsonResponse JSON response containing the user's bookings.
     */
    public function getUserBookings(): JsonResponse
    {
        $bookings = $this->bookingService->getUserBookings();

        return response()->json(['bookings' => $bookings]);
    }

    /**
     * Calls BookingService to create booking.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @return JsonResponse JSON response containing the created booking.
     */
    public function createBooking(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'from' => 'required|date',
                'to' => 'required|date|after:from',
            ]);

            $user = auth()->user();
            $booking = $this->bookingService->createBooking($data, $user);

            return response()->json(['booking' => $booking]);

        } catch (\Exception $exception) {

            return response()->json(['error' => $exception->getMessage()]);

        }
    }

    /**
     * Calls BookingService to amend booking.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @param  int  $id  The ID of the booking to be amended.
     * @return JsonResponse JSON response containing the amended booking.
     */
    public function amendBooking(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'from' => 'date',
            'to' => 'date|after:from',
        ]);

        $booking = $this->bookingService->amendBooking($id, $data);

        return response()->json(['booking' => $booking]);
    }

    /**
     * Calls BookingService to cancel booking.
     *
     * @param  int  $id  The ID of the booking to be canceled.
     * @return JsonResponse JSON response indicating the success of the cancellation.
     */
    public function cancelBooking(int $id): JsonResponse
    {
        $this->bookingService->cancelBooking($id);

        return response()->json(['message' => 'Booking cancelled successfully']);
    }
}
