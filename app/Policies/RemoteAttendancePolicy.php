<?php

namespace App\Policies;

use App\Models\RemoteAttendance;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RemoteAttendancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any remote attendances.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the remote attendance.
     *
     * @param  \App\User  $user
     * @param  \App\Models\RemoteAttendance  $remoteAttendance
     * @return mixed
     */
    public function view(User $user, RemoteAttendance $remoteAttendance)
    {
       return $user->hasPermission('Attendance','Department List');
    }

    public function submit(User $user, RemoteAttendance $remoteAttendance)
    {
        return $user->hasPermission('Attendance','Punch Attendance');
    }
    /**
     * Determine whether the user can create remote attendances.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the remote attendance.
     *
     * @param  \App\User  $user
     * @param  \App\Models\RemoteAttendance  $remoteAttendance
     * @return mixed
     */
    public function update(User $user, RemoteAttendance $remoteAttendance)
    {
        //
    }

    /**
     * Determine whether the user can delete the remote attendance.
     *
     * @param  \App\User  $user
     * @param  \App\Models\RemoteAttendance  $remoteAttendance
     * @return mixed
     */
    public function delete(User $user, RemoteAttendance $remoteAttendance)
    {
        //
    }

    /**
     * Determine whether the user can restore the remote attendance.
     *
     * @param  \App\User  $user
     * @param  \App\Models\RemoteAttendance  $remoteAttendance
     * @return mixed
     */
    public function restore(User $user, RemoteAttendance $remoteAttendance)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the remote attendance.
     *
     * @param  \App\User  $user
     * @param  \App\Models\RemoteAttendance  $remoteAttendance
     * @return mixed
     */
    public function forceDelete(User $user, RemoteAttendance $remoteAttendance)
    {
        //
    }
}
