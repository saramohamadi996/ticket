<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface AuthRepositoryInterface
{
    /**
     * Register a new user.
     *
     * @param array $userData

     * @return User
     */
    public function register(array $userData): User;

    /**
     * Authenticate user and generate access token.
     *
     * @param array $data
     *
     * @return User
     */
    public function login(array $data);
}
