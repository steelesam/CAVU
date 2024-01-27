<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Calls BookingService to get all User bookings
     */
    public function getUserBookings()
    {
        $bookings = $this->bookingService->getUserBookings();

        return response()->json(['bookings' => $bookings]);
    }

    /**
     * Calls BookingService to create booking
     */
    public function createBooking(Request $request)
    {
        try {

            $data = $request->validate([
                'from' => 'required|date',
                'to' => 'required|date|after:from',
            ]);

            $user = auth()->user();
            $booking = $this->bookingService->createBooking($data, $user);

            return response()->json(['booking' => $booking]);

        } catch (Exception $exception) {

            return response()->json(['error' => $exception->getMessage()]);

        }

    }

    /**
     * Calls BookingService to amend booking
     */
    public function amendBooking(Request $request, $id)
    {
        $request->validate([
            'from' => 'date',
            'to' => 'date|after:from',
            // Add more validation rules as needed
        ]);

        $data = $request->validated();
        $booking = $this->bookingService->amendBooking($id, $data);

        return response()->json(['booking' => $booking]);
    }

    /**
     * Calls BookingService to cancel booking
     */
    public function cancelBooking($id)
    {
        // No validation needed for this endpoint

        $this->bookingService->cancelBooking($id);

        return response()->json(['message' => 'Booking canceled successfully']);
    }
}
