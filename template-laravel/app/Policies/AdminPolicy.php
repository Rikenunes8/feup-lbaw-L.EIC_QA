<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can show.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function show(User $user)
    {
        return $user->isAdmin();
    }
}
