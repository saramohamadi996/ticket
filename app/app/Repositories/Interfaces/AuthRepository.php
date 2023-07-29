<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Register a new user.
     *
     * @param array $userData
     *
     * @return User
     */
    public function register(array $userData): User
    {
        return User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
        ]);
    }

    /**
     * Authenticate user and generate access token.
     *
     * @param array $data
     *
     * @return User
     */
    public function login(array $data)
    {
        return $this->user->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
