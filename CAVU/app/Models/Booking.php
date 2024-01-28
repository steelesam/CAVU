<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['from', 'to', 'user_id', 'parking_space_id'];

    /**
     * User association
     * Each booking belongs to a user.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parking space association
     * Each booking belongs to a parking space.
     *
     * @return BelongsTo
     */
    public function parkingSpace()
    {
        return $this->belongsTo(ParkingSpace::class);
    }
}
