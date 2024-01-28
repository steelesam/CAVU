<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Create a new user.
     *
     * @param  array  $data  The data for creating the user.
     * @return User The created User instance.
     *
     * @throws Exception
     */
    public function createUser(array $data): User
    {
        try {
            // Hash the password
            $data['password'] = Hash::make($data['password']);

            return User::create($data);
        } catch (\Exception $exception) {
            Log::error('Error creating user: '.$exception->getMessage());
            throw $exception;
        }
    }
}
