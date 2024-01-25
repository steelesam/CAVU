<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Public Routes
 */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/create-user', [UserController::class, 'createUser']);
Route::get('/parking-spaces/availability', [BookingController::class, 'getParkingSpacesAvailability']);

/**
 * Authenticated routes
 */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/create-booking', [BookingController::class, 'createBooking']);
    Route::get('/user/bookings', [BookingController::class, 'getUserBookings']);
    Route::put('/bookings/{id}', [BookingController::class, 'amendBooking']);
    Route::delete('/bookings/{id}', [BookingController::class, 'cancelBooking']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

