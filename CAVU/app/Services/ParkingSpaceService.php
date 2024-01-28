<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ParkingSpace;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ParkingSpaceService
{
    /**
     * Creates a new parking space.
     *
     * @param  array  $data  The data for creating the parking space.
     * @return ParkingSpace The created ParkingSpace instance.
     *
     * @throws Exception
     */
    public function createParkingSpace(array $data): ParkingSpace
    {
        try {
            $parkingSpace = ParkingSpace::create($data);

            return $parkingSpace;
        } catch (\Exception $exception) {
            Log::error('Error creating parking space: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Checks the availability of parking spaces for the given date range.
     *
     * @param  string  $from  The starting date for checking availability.
     * @param  string  $to  The ending date for checking availability.
     * @return JsonResponse A JSON response of count of available parking spaces.
     *
     * @throws Exception
     */
    public function checkParkingSpaceAvailability(string $from, string $to): int
    {
        try {
            $currentBookingsForDateCount = Booking::whereBetween('from', [$from, $to])
                ->orWhereBetween('to', [$from, $to])
                ->count();

            // Spec only mentions 10 spaces, but due to having the createParkingSpace function there this may change
            $totalNumberOfParkingSpaces = ParkingSpace::count();
            $availableSpaces = $totalNumberOfParkingSpaces - $currentBookingsForDateCount;

            return $availableSpaces;
        } catch (\Exception $exception) {
            Log::error('Error checking parking space availability: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Checks the availability of parking spaces for the given date.
     *
     * @param  string  $date  The date for checking availability.
     * @return JsonResponse A JSON response of count of available parking spaces.
     *
     * @throws Exception
     */
    public function checkParkingSpaceAvailabilityForSingleDay(string $date): int
    {
        try {
            $currentBookingsForDateCount = Booking::whereDate('from', '<=', $date)
                ->whereDate('to', '>=', $date)
                ->count();

            // Spec only mentions 10 spaces, but due to having the createParkingSpace function there this may change
            $totalNumberOfParkingSpaces = ParkingSpace::count();
            $availableSpaces = $totalNumberOfParkingSpaces - $currentBookingsForDateCount;

            return $availableSpaces;
        } catch (\Exception $exception) {
            Log::error('Error checking parking space availability for single day: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Associate a parking space with a booking.
     *
     * @param  Booking  $booking  The booking to associate with a parking space.
     * @return void
     *
     * @throws Exception
     */
    public function associateParkingSpace(Booking $booking)
    {
        try {
            $parkingSpace = ParkingSpace::where('available', true)->first();

            if ($parkingSpace) {
                $booking->parking_space_id = $parkingSpace->id;
                $booking->save();

                $parkingSpace->update(['available' => false]);
            }
        } catch (\Exception $exception) {
            Log::error('Error associating parking space with booking: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Calculates the price of any given parking space based on the day of the week and time of year.
     *
     * @param  string  $date  The date for which to calculate the price.
     * @return int The calculated price per day.
     *
     * @throws Exception
     */
    public function calculatePricePerDay(string $date): int
    {
        try {
            $carbonDate = Carbon::parse($date);
            $isWeekend = $carbonDate->isWeekend();
            $isWinter = $carbonDate->month >= 9 && $carbonDate->month <= 3; // September to March

            if ($isWinter) {
                return $isWeekend ? 70 : 50;
            } else {
                return $isWeekend ? 80 : 60;
            }
        } catch (\Exception $exception) {
            Log::error('Error calculating price per day: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Calculates the total price for a given date range based on the pricing logic.
     *
     * @param  string  $from  The starting date of the date range.
     * @param  string  $to  The ending date of the date range.
     * @return int The calculated total price for the date range.
     *
     * @throws Exception
     */
    public function calculateTotalPriceForDateRange(string $from, string $to): int
    {
        try {
            $totalPrice = 0;
            $fromDate = Carbon::parse($from);
            $toDate = Carbon::parse($to);

            while ($fromDate <= $toDate) {
                $totalPrice += $this->calculatePricePerDay($fromDate->toDateString());
                $fromDate->addDay();
            }

            return $totalPrice;
        } catch (\Exception $exception) {
            Log::error('Error calculating total price for date range: '.$exception->getMessage());
            throw $exception;
        }
    }
}
