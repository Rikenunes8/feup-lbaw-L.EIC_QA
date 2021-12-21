<?php

namespace App\Policies;

use App\Models\Intervention;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InterventionPolicy
{
    use HandlesAuthorization;


    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Intervention  $intervention
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Intervention $intervention)
    {
        //
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Intervention  $intervention
     * @return Response|bool
     */
    public function show(User $user, Intervention $intervention)
    {
        return $intervention->type == 'question';
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        return $user->type != 'Admin' && !user->blocked;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Intervention  $intervention
     * @return Response|bool
     */
    public function update(User $user, Intervention $intervention)
    {
        return $user->id == $intervention->id_author && !user->blocked;
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Intervention  $intervention
     * @return Response|bool
     */
    public function delete(User $user, Intervention $intervention)
    {
        return $user->type == 'Admin';
    }
}
