<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
     * 
     */
    public function getParkingSpacesAvailability(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after:from',
        ]);

        $from = $request->input('from');
        $to = $request->input('to');
        $availableSpaces = $this->bookingService->getParkingSpacesAvailability($from, $to);

        return response()->json(['available_spaces' => $availableSpaces]);
    }

    /**
     * 
     */
    public function getUserBookings()
    {
        $user = auth()->user();
        // No validation needed for this endpoint

        $bookings = $user->bookings;

        return response()->json(['bookings' => $bookings]);
    }

    /**
     * 
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

        } catch (\Illuminate\Validation\ValidationException $exception){

            return response()->json(['error' => $exception->getMessage()]);

        }

    }

    /**
     * 
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
     * 
     */
    public function cancelBooking($id)
    {
        // No validation needed for this endpoint

        $this->bookingService->cancelBooking($id);

        return response()->json(['message' => 'Booking canceled successfully']);
    }
}