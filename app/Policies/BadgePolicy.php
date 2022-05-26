<?php

namespace App\Policies;

use App\Models\Badge;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BadgePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any badges.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the badge.
     *
     * @param  \App\User  $user
     * @param  \App\Badge  $badge
     * @return mixed
     */
    public function view(User $user, Badge $badge)
    {
        return $user->hasPermission('Badge','view');
    }

    /**
     * Determine whether the user can create badges.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user,Badge $badge)
    {
        return $user->hasPermission('Badge','create');
    }

    /**
     * Determine whether the user can update the badge.
     *
     * @param  \App\User  $user
     * @param  \App\Badge  $badge
     * @return mixed
     */
    public function update(User $user, Badge $badge)
    {
        return $user->hasPermission('Badge','update');
    }

    /**
     * Determine whether the user can delete the badge.
     *
     * @param  \App\User  $user
     * @param  \App\Badge  $badge
     * @return mixed
     */
    public function delete(User $user, Badge $badge)
    {
        return $user->hasPermission('Badge','delete');
    }

    /**
     * Determine whether the user can restore the badge.
     *
     * @param  \App\User  $user
     * @param  \App\Badge  $badge
     * @return mixed
     */
    public function restore(User $user, Badge $badge)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the badge.
     *
     * @param  \App\User  $user
     * @param  \App\Badge  $badge
     * @return mixed
     */
    public function forceDelete(User $user, Badge $badge)
    {
        //
    }
}
