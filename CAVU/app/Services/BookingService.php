<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Models\ParkingSpace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BookingService
{
    protected $parkingSpaceService;

    public function __construct(ParkingSpaceService $parkingSpaceService)
    {
        $this->parkingSpaceService = $parkingSpaceService;
    }

    /**
     * Creates a booking for the user
     */
    public function createBooking(array $data, User $user): Booking
    {
        // Check there are actually available parking spaces or not
        $bookedSpacesCount = Booking::where(function (Builder $query) use ($data) {
        $query->where('from', '>=', $data['from'])
            ->where('from', '<', $data['to'])
            ->orWhere(function (Builder $query) use ($data) {
                $query->where('to', '>', $data['from'])
                    ->where('to', '<=', $data['to']);
            });
        })->count();

        if ($bookedSpacesCount >= 10) {
            throw \Illuminate\Validation\ValidationException::withMessages(['error' => 'No parking spaces currently available.']);
        }

        $booking = $user->bookings()->create($data);

        return $booking;
    }

    /**
     * Cancels an existing booking
     */
    public function cancelBooking(int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->delete();

        return response()->json(['message' => 'Booking cancelled successfully']);
        
    }

    /**
     * Amends the details of an existing booking
     */
    public function amendBooking(int $bookingId, array $data): Booking
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->update($data);

        return response()->json(['booking' => $booking]);
    }

    /**
     * Get all bookings by a customer
     */
    public function getUserBookings()
    {
        $user = auth()->user();
        $bookings = $user->bookings;

        return response()->json(['bookings' => $bookings]);
    }

    /**
     * Retrieves the spaces that are available currently. 
     */
    public function getParkingSpacesAvailability(string $from, string $to): Collection
    {
        $availableSpaces = ParkingSpace::whereDoesntHave('bookings', function (Builder $query) use ($from, $to) {
            $query->where(function (Builder $query) use ($from, $to) {
                $query->where('from', '>=', $from)
                    ->where('from', '<', $to)
                    ->orWhere(function (Builder $query) use ($from, $to) {
                        $query->where('to', '>', $from)
                            ->where('to', '<=', $to);
                    });
            });
        })->get();

        return response()->json(['available_spaces' => $availableSpaces]);
    }
}
