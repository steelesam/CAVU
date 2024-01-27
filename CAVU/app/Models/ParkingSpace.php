<?php

namespace App\Models;

use App\Services\ParkingSpaceService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingSpace extends Model
{
    use HasFactory;

    protected $fillable = ['available'];

    protected $parkingSpaceService;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->parkingSpaceService = app(ParkingSpaceService::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Uses ParkingSpaceService logic to get price per day
     */
    public function calculatePricePerDay($date)
    {
        return $this->parkingSpaceService->calculatePricePerDay($date);
    }
}
