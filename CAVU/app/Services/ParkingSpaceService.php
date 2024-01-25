<?php

namespace App\Services;

use App\Models\ParkingSpace;
use Carbon\Carbon;

class ParkingSpaceService
{
    /**
     * Calculates the price of any given parking space based on the day of the week and time of year
     */
    public function calculatePricePerDay($date)
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
