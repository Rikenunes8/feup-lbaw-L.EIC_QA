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
     * Determine whether the user can follow the model.
     *
     * @param  User   $user
     * @param  Uc   $uc
     * @return Response|bool
     */
    public function follow(User $user, Uc $uc)
    {
        return $user->isStudent() && !$user->blocked;
    }

    /**
     * Determine whether the user can view model's create form.
     *
     * @param  User   $user
     * @return Response|bool
     */
    public function showCreate(User $user)
    {
        return $user->isAdmin() && !$user->blocked;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User   $user
     * @param  Uc   $uc
     * @return Response|bool
     */
    public function create(User $user, Uc $uc)
    {
        return $user->isAdmin() && !$user->blocked;
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
        return $user->isAdmin() && !$user->blocked;
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
        return $user->isAdmin() && !$user->blocked;
    }

    /**
     * Determine whether the user can add or delete an association between a uc and a teacher.
     *
     * @param  User  $user
     * @param  Uc  $uc
     * @param  User  $teacher
     * @return Response|bool
     */
    public function teacher(User $user, Uc $uc, User $teacher)
    {
        return $user->isAdmin() && $teacher->isTeacher();
    }
}
