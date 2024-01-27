<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ParkingSpace;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ParkingSpaceService
{
    /**
     * Creates a new parking space.
     *
     * @param  array  $data  The data for creating the parking space.
     * @return ParkingSpace The created ParkingSpace instance.
     */
    public function createParkingSpace(array $data): ParkingSpace
    {
        $parkingSpace = ParkingSpace::create($data);

        return $parkingSpace;
    }

    /**
     * Checks the availability of parking spaces for the given date range.
     *
     * @param  string  $from  The starting date for checking availability.
     * @param  string  $to  The ending date for checking availability.
     * @return JsonResponse A JSON response of count of available parking spaces.
     */
    public function checkParkingSpaceAvailability(string $from, string $to): int
    {
        $currentBookingsForDateCount = Booking::whereBetween('from', [$from, $to])
            ->orWhereBetween('to', [$from, $to])
            ->count();

        // Spec only mentions 10 spaces, but due to having the createParkingSpace function there this may change
        $totalNumberOfParkingSpaces = ParkingSpace::count();
        $availableSpaces = $totalNumberOfParkingSpaces - $currentBookingsForDateCount;

        return $availableSpaces;
    }

    /**
     * Checks the availability of parking spaces for the given date.
     *
     * @param  string  $date  The date for checking availability.
     * @return JsonResponse A JSON response of count of available parking spaces.
     */
    public function checkParkingSpaceAvailabilityForSingleDay(string $date): int
    {
        $currentBookingsForDateCount = Booking::whereDate('from', '<=', $date)
            ->whereDate('to', '>=', $date)
            ->count();

        // Spec only mentions 10 spaces, but due to having the createParkingSpace function there this may change
        $totalNumberOfParkingSpaces = ParkingSpace::count();
        $availableSpaces = $totalNumberOfParkingSpaces - $currentBookingsForDateCount;

        return $availableSpaces;
    }

    /**
     * Associate a parking space with a booking.
     *
     * @param  Booking  $booking  The booking to associate with a parking space.
     * @return void
     */
    public function associateParkingSpace(Booking $booking)
    {
        $parkingSpace = ParkingSpace::where('available', true)->first();

        if ($parkingSpace) {
            $booking->parking_space_id = $parkingSpace->id;
            $booking->save();

            $parkingSpace->update(['available' => false]);
        }
    }

    /**
     * Calculates the price of any given parking space based on the day of the week and time of year.
     *
     * @param  string  $date  The date for which to calculate the price.
     * @return int The calculated price per day.
     */
    public function calculatePricePerDay(string $date): int
    {
        $carbonDate = Carbon::parse($date);

        $isWeekend = $carbonDate->isWeekend();
        $isWinter = $carbonDate->month >= 9 && $carbonDate->month <= 3; // September to March

        if ($isWinter) {
            return $isWeekend ? 70 : 50;
        } else {
            return $isWeekend ? 80 : 60;
        }
    }
}
