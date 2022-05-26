<?php

namespace App\Policies;

use App\Models\Attendance;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any attendances.
     *
     * @param  \App\User  $user
     * @return mixed
     */
   
    /**
     * Determine whether the user can view the attendance.
     *
     * @param  \App\User  $user
     * @param  \App\Attendance  $attendance
     * @return mixed
     */
    public function import(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","import");
    }

    /**
     * Determine whether the user can create attendances.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function export(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","export");
    }
    public function hrView(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","hrView");
    }
    public function managerView(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","managerView");
    }
    public function viewAttendance(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","Punch Attendance");
    }
    public function create(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","create");
    }
    public function dashboard(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","dashboard");
    }

  
  
}
