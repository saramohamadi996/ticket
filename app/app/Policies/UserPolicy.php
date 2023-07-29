<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    public function view(User $user)
    {
        return $user->hasPermissionTo('view users');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create users');
    }

    public function update(User $user)
    {
        return $user->hasPermissionTo('edit users');
    }

    public function delete(User $user)
    {
        return $user->hasPermissionTo('delete users');
    }
}
