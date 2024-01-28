<?php

namespace App\Http\Controllers;

use App\Services\ParkingSpaceService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ParkingSpaceController extends Controller
{
    protected $parkingSpaceService;

    /**
     * Constructor for the ParkingController.
     *
     * @param  ParkingSpaceService  $parkingSpaceService  An instance of the ParkingSpaceService.
     */
    public function __construct(ParkingSpaceService $parkingSpaceService)
    {
        $this->parkingSpaceService = $parkingSpaceService;
    }

    /**
     * Creates a new parking space.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @return JsonResponse JSON response containing the created parking space and success message.
     */
    public function createParkingSpace(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'available' => 'required|int',
            ]);

            $parkingSpace = $this->parkingSpaceService->createParkingSpace($validatedData);

            return response()->json(['parking_space' => $parkingSpace, 'message' => 'Parking space created successfully'], 201);

        } catch (ValidationException $validationException) {
            return response()->json(['error' => $validationException->errors()], 422);

        } catch (Exception $exception) {
            Log::error('Error creating parking space: '.$exception->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Checks the availability of parking spaces for the given date range.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @return JsonResponse JSON response containing the count of available parking spaces.
     */
    public function checkParkingSpaceAvailability(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'from' => 'required|date',
                'to' => 'required|date|after:from',
            ]);

            $from = $validatedData['from'];
            $to = $validatedData['to'];

            $availableSpacesCount = $this->parkingSpaceService->checkParkingSpaceAvailability($from, $to);

            return response()->json(['available_spaces_count' => $availableSpacesCount]);

        } catch (ValidationException $validationException) {
            return response()->json(['error' => $validationException->errors()], 422);

        } catch (Exception $exception) {
            Log::error('Error checking parking space availability: '.$exception->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Checks the availability of parking spaces for the given date.
     *
     * @param  Request  $request  The HTTP request containing the date parameter.
     * @return JsonResponse A JSON response of count of available parking spaces.
     */
    public function checkParkingSpaceAvailabilityForSingleDay(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'date' => 'required|date',
            ]);

            $date = $validatedData['date'];

            $availableSpacesCount = $this->parkingSpaceService->checkParkingSpaceAvailabilityForSingleDay($date);

            return response()->json(['available_spaces_count' => $availableSpacesCount]);

        } catch (ValidationException $validationException) {
            return response()->json(['error' => $validationException->errors()], 422);

        } catch (Exception $exception) {
            Log::error('Error checking parking space availability for single day: '.$exception->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Calculate the price per day based on the date using ParkingSpaceService logic.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @return JsonResponse The JSON response containing the calculated price per day.
     */
    public function calculatePricePerDay(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'date' => 'required|date',
            ]);

            $date = $validatedData['date'];

            $pricePerDay = $this->parkingSpaceService->calculatePricePerDay($date);

            return response()->json(['price_per_day' => $pricePerDay]);

        } catch (ValidationException $validationException) {
            return response()->json(['error' => $validationException->errors()], 422);

        } catch (Exception $exception) {
            Log::error('Error calculating price per day: '.$exception->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Calculates the total price for a parking space based on the date range.
     *
     * @param  Request  $request  The HTTP request containing the date range.
     * @return JsonResponse A JSON response containing the calculated total price.
     */
    public function calculateTotalPriceForDateRange(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'from' => 'required|date',
                'to' => 'required|date|after:from',
            ]);

            $from = $validatedData['from'];
            $to = $validatedData['to'];

            $totalPrice = $this->parkingSpaceService->calculateTotalPriceForDateRange($from, $to);

            return response()->json(['total_price' => $totalPrice]);

        } catch (ValidationException $validationException) {
            return response()->json(['error' => $validationException->errors()], 422);

        } catch (Exception $exception) {
            Log::error('Error calculating total price for date range: '.$exception->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
