<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return User::all();
    }

    /**
     * Create a new user.
     *
     * @param array $data The user data to create.
     * @return User The created user instance.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Find a user by ID.
     *
     * @param int $id The ID of the user to find.
     * @return User The found user instance.
     * @throws ModelNotFoundException
     */
    public function find(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Update a user.
     *
     * @param User $user The user instance to update.
     * @param array $data The user data to update.
     * @return User The updated user instance.
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    /**
     * Delete a user.
     *
     * @param User $user The user instance to delete.
     * @return void
     */
    public function delete(User $user): void
    {
        $user->delete();
    }

}
