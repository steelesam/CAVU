<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkingSpace extends Model
{
    use HasFactory;

    protected $fillable = ['available'];

    /**
     * Get the bookings associated with the parking space.
     * A parking space can have many bookings.
     *
     * @return HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
