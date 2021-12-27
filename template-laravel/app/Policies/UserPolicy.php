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
     * @param  User  $userAuth
     * @param  User  $user
     * @return Response|bool
     */
    public function show(User $userAuth, User $user)
    {
        return !$userAuth->blocked;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $userAuth
     * @param  User  $user
     * @return Response|bool
     */
    public function update(User $userAuth, User $user)
    {
        return $userAuth->id == $user->id && !$userAuth->blocked;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $userAuth
     * @param  User  $user
     * @return Response|bool
     */
    public function delete(User $userAuth, User $user)
    {
        return $userAuth->isAdmin();
    }

    /**
     * Determine whether the user can block the model.
     *
     * @param  User  $userAuth
     * @param  User  $user
     * @return Response|bool
     */
    public function block(User $userAuth, User $user)
    {
        return $userAuth->isAdmin();
    }
}
