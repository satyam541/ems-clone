<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Announcement;
use App\User;

class AnnouncementPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any announcements.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the announcement.
     *
     * @param  \App\User  $user
     * @param  \App\Announcement  $announcement
     * @return mixed
     */
    public function view(User $user, Announcement $announcement)
    {
        return $user->hasPermission("Announcement","view");
    }

    /**
     * Determine whether the user can create announcements.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission("Announcement","create");
    }

    /**
     * Determine whether the user can update the announcement.
     *
     * @param  \App\User  $user
     * @param  \App\Announcement  $announcement
     * @return mixed
     */
    public function update(User $user, Announcement $announcement)
    {
        return $user->hasPermission("Announcement","update");
    }

    /**
     * Determine whether the user can delete the announcement.
     *
     * @param  \App\User  $user
     * @param  \App\Announcement  $announcement
     * @return mixed
     */
    public function delete(User $user, Announcement $announcement)
    {
        return $user->hasPermission("Announcement","delete");
    }

    /**
     * Determine whether the user can restore the announcement.
     *
     * @param  \App\User  $user
     * @param  \App\Announcement  $announcement
     * @return mixed
     */
    public function restore(User $user, Announcement $announcement)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the announcement.
     *
     * @param  \App\User  $user
     * @param  \App\Announcement  $announcement
     * @return mixed
     */
    public function forceDelete(User $user, Announcement $announcement)
    {
        //
    }
}
