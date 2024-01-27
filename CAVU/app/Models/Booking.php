<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['from', 'to', 'user_id', 'parking_space_id'];

    /**
     * User association
     * Each booking belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parkingSpace()
    {
        return $this->belongsTo(ParkingSpace::class);
    }
}
