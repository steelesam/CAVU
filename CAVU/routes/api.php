<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ParkingSpaceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/**
 * Get generic user information
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Public Routes
 */
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/create-user', [UserController::class, 'createUser'])->name('create-user');
Route::get('/parking-space-availability', [ParkingSpaceController::class, 'checkParkingSpaceAvailability'])->name('parking-space-availability');
Route::get('/parking-space-availability-single-day', [ParkingSpaceController::class, 'checkParkingSpaceAvailabilityForSingleDay'])->name('parking-space-availability-single-day');
Route::get('/calculate-price-per-day', [ParkingSpaceController::class, 'calculatePricePerDay'])->name('calculate-price-per-day');
Route::get('/calculate-total-price-for-range', [ParkingSpaceController::class, 'calculateTotalPriceForDateRange'])->name('calculate-total-price-for-range');

/**
 * Can extend functionality here so "Manchester Airport" can add new spaces and either make them available to the system or not as they are built.
 * Would lock this endpoint behind auth that validates the User performing the action is an "admin". Out of scope here though.
 */
Route::post('/create-parking-space', [ParkingSpaceController::class, 'createParkingSpace'])->name('create-parking-space');

/**
 * Authenticated routes. A Bearer token needs to be generated through login (through postman or whichever tool you're using to test) and the passed in the auth headers
 */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/create-booking', [BookingController::class, 'createBooking'])->name('create-booking');
    Route::put('/amend-booking/{id}', [BookingController::class, 'amendBooking'])->name('amend-booking');
    Route::delete('/cancel-booking/{id}', [BookingController::class, 'cancelBooking'])->name('cancel-booking');
    Route::get('/user/bookings', [BookingController::class, 'getUserBookings'])->name('user-bookings');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

