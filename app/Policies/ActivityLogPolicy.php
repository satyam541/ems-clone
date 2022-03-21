<?php

namespace App\Policies;

use App\Models\ActivityLog;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityLogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any activity logs.
     *
     * @param  \App\User  $user
     * @return mixed
     */
 

    /**
     * Determine whether the user can view the activity log.
     *
     * @param  \App\User  $user
     * @param  \App\Models\ActivityLog  $activityLog
     * @return mixed
     */
    public function view(User $user, ActivityLog $activityLog)
    {
        return $user->hasPermission('Activity','view');
    }

    /**
     * Determine whether the user can create activity logs.
     *
     * @param  \App\User  $user
     * @return mixed
     */
   
}
