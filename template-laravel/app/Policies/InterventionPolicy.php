<?php

namespace App\Policies;

use App\Models\Intervention;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InterventionPolicy
{
    use HandlesAuthorization;


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
     * @param  Intervention  $intervention
     * @return Response|bool
     */
    public function create(User $user, Intervention $intervention)
    {
        return $user->type != 'Admin' && !$user->blocked;
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
        return $user->id == $intervention->id_author && !$user->blocked;
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
        return ($user->type == 'Admin' || $user->id == $intervention->id_author) && !$user->blocked;
    }

    /**
     * Determine whether the user can vote the intervention.
     *
     * @param  User  $user
     * @param  Intervention  $intervention
     * @return Response|bool
     */
    public function vote(User $user, Intervention $intervention)
    {
        return $user->type != 'Admin' && $user->id != $intervention->id_author && !$user->blocked && $intervention->type != 'comment';
    }

    /**
     * Determine whether the user can validate the intervention.
     *
     * @param  User  $user
     * @param  Intervention  $intervention
     * @return Response|bool
     */
    public function validate(User $user, Intervention $intervention)
    {
        return $user->type == 'Teacher' && $user->id != $intervention->id_author 
                && !$user->blocked && $intervention->type == 'answer';
    }
}
