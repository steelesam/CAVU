<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Calls UserService to create a new user.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @return JsonResponse JSON response containing the created user.
     */
    public function createUser(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
            ]);

            $user = $this->userService->createUser($validatedData);

            return response()->json(['user' => $user, 'message' => 'User created successfully'], 201);

        } catch (ValidationException $validationException) {
            return response()->json(['error' => $validationException->errors()], 422);

        } catch (Exception $exception) {
            Log::error('Error creating user: '.$exception->getMessage());

            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
