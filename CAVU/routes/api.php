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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Public Routes
 */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/create-user', [UserController::class, 'createUser']);
Route::get('/parking-space-availability', [ParkingSpaceController::class, 'checkParkingSpaceAvailability']);

/**
 * Authenticated routes. A Bearer token needs to be generated through login (through postman or whichever tool you're using to test) and the passed in the auth headers
 */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/create-booking', [BookingController::class, 'createBooking']);
    Route::put('/amend-booking/{id}', [BookingController::class, 'amendBooking']);
    Route::delete('/cancel-booking/{id}', [BookingController::class, 'cancelBooking']);
    Route::get('/user/bookings', [BookingController::class, 'getUserBookings']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

/**
 * Can extend functionality here so "Manchester Airport" can add new spaces and either make them available to the system or not as they are built.
 * Would lock this endpoint behind auth that validates the User performing the action is an "admin". Out of scope here though.
 */
Route::post('/create-parking-space', [ParkingSpaceController::class, 'createParkingSpace']);
