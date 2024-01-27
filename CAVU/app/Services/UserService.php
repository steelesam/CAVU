<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Create a new user.
     */
    public function createUser(array $data): User
    {
        // Hash the password
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }
}
