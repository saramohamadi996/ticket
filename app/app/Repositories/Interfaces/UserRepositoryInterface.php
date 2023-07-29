<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Get all users.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Create a new user.
     *
     * @param array $data The user data to create.
     * @return User The created user instance.
     */
    public function create(array $data): User;

    /**
     * Find a user by ID.
     *
     * @param int $id The ID of the user to find.
     * @return User The found user instance.
     * @throws ModelNotFoundException
     */
    public function find(int $id): User;

    /**
     * Update a user.
     *
     * @param User $user The user instance to update.
     * @param array $data The user data to update.
     * @return User The updated user instance.
     */
    public function update(User $user, array $data): User;

    /**
     * Delete a user.
     *
     * @param User $user The user instance to delete.
     * @return void
     */
    public function delete(User $user): void;

}

