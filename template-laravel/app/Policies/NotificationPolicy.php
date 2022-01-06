<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  notification  $notification
     * @return Response|bool
     */
    public function delete(User $user, Notification $notification)
    {
        return $user->isAdmin();
    }

    
}
