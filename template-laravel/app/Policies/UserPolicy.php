<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can show.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function showToAdmin(User $user)
    {
        return $user->isAdmin() && !$user->blocked;
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
        return ($user->isAdmin() || $user->id == $user2->id) && !$user->blocked;
    }

    /**
     * Determine whether the user can active the model.
     *
     * @param  User  $user
     * @param  User  $user2
     * @return Response|bool
     */
    public function active(User $user, User $user2)
    {
        return $user->isAdmin() && !$user->blocked;
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
        return $user->isAdmin() && !$user2->isAdmin() && !$user->blocked && ($user2->active == 1);
    }

    /**
     * Determine whether the user can follow the model.
     *
     * @param  User   $user
     * @param  User   $user2
     * @return Response|bool
     */
    public function follow(User $user, User $user2)
    {
        return $user->isStudent() && !$user->blocked && $user->id == $user2->id;
    }
}
