<?php

namespace App\Policies;

use App\Models\Uc;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UcPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Uc  $uc
     * @return Response|bool
     */
    public function show(User $user, Uc $uc)
    {
        return !$user->blocked;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        return $user->type == 'Admin';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Uc  $uc
     * @return Response|bool
     */
    public function update(User $user, Uc $uc)
    {
        return $user->type == 'Admin';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Uc  $uc
     * @return Response|bool
     */
    public function delete(User $user, Uc $uc)
    {
        return $user->type == 'Admin';
    }
}
