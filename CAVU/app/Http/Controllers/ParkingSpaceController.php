<?php

namespace App\Http\Controllers;

use App\Services\ParkingSpaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $validatedData = $request->validate([
            'available' => 'required|int',
        ]);

        $parkingSpace = $this->parkingSpaceService->createParkingSpace($validatedData);

        return response()->json(['parking_space' => $parkingSpace, 'message' => 'Parking space created successfully'], 201);
    }

    /**
     * Checks the availability of parking spaces for the given date range.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @return JsonResponse JSON response containing the count of available parking spaces.
     */
    public function checkParkingSpaceAvailability(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after:from',
        ]);

        $from = $validatedData['from'];
        $to = $validatedData['to'];

        $availableSpacesCount = $this->parkingSpaceService->checkParkingSpaceAvailability($from, $to);

        return response()->json(['available_spaces_count' => $availableSpacesCount]);
    }
}
