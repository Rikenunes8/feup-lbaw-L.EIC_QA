<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;


    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  User  $user2
     * @return Response|bool
     */
    public function show(User $user)
    {
        return !$user->blocked;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  User  $user2
     * @return Response|bool
     */
    public function update(User $user, User $user2)
    {
        return ($user->id == $user2->id || $user->isAdmin()) && !$user->blocked;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  User  $user2
     * @return Response|bool
     */
    public function delete(User $user, User $user2)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can block the model.
     *
     * @param  User  $user
     * @param  User  $user2
     * @return Response|bool
     */
    public function block(User $user, User $user2)
    {
        return $user->isAdmin();
    }
}
