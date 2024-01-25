<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ParkingSpaceService;

class ParkingSpace extends Model
{
    use HasFactory;

    protected $fillable = ['price_per_day'];

    protected $parkingSpaceService;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->parkingSpaceService = app(ParkingSpaceService::class);
    }

    /**
     * Uses ParkingSpaceService logic to get price per day
     */
    public function calculatePricePerDay($date)
    {
        return $this->parkingSpaceService->calculatePricePerDay($date);
    }
}
