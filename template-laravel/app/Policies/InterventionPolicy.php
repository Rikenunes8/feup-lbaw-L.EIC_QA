<?php

namespace App\Policies;

use App\Models\Intervention;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InterventionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @param  Intervention  $intervention
     * @return Response|bool
     */
    public function showCreate(User $user)
    {
        return !$user->isAdmin() && !$user->blocked;
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
        return !$user->isAdmin() && !$user->blocked;
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
        return ($user->isAdmin() || $user->id == $intervention->id_author) && !$user->blocked;
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
        return !$user->isAdmin() && $user->id != $intervention->id_author && !$user->blocked && !$intervention->isComment();
    }

    /**
     * Determine whether the user can validate the intervention.
     *
     * @param  User  $user
     * @param  Intervention  $intervention
     * @return Response|bool
     */
    public function valid(User $user, Intervention $intervention)
    {
        return $user->isTeacher() && $user->id != $intervention->id_author 
                && !$user->blocked && $intervention->isAnswer();
    }
}
