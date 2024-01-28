<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
        try {
            $bookings = $this->bookingService->getUserBookings();

            return response()->json(['bookings' => $bookings]);

        } catch (Exception $exception) {
            Log::error('Error retrieving user bookings: '.$exception->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
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

        } catch (ValidationException $validationException) {
            return response()->json(['error' => $validationException->errors()], 422);

        } catch (Exception $exception) {
            Log::error('Error creating booking: '.$exception->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
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
        try {
            $data = $request->validate([
                'from' => 'date',
                'to' => 'date|after:from',
            ]);

            $booking = $this->bookingService->amendBooking($id, $data);

            return response()->json(['booking' => $booking]);

        } catch (ValidationException $validationException) {
            return response()->json(['error' => $validationException->errors()], 422);

        } catch (Exception $exception) {
            Log::error('Error amending booking: '.$exception->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Calls BookingService to cancel booking.
     *
     * @param  int  $id  The ID of the booking to be canceled.
     * @return JsonResponse JSON response indicating the success of the cancellation.
     */
    public function cancelBooking(int $id): JsonResponse
    {
        try {
            $this->bookingService->cancelBooking($id);

            return response()->json(['message' => 'Booking cancelled successfully']);

        } catch (Exception $exception) {
            Log::error('Error cancelling booking: '.$exception->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
