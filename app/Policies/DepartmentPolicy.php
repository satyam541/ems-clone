<?php

namespace App\Policies;

use App\Models\Department;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any departments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
  

    /**
     * Determine whether the user can view the department.
     *
     * @param  \App\User  $user
     * @param  \App\Department  $department
     * @return mixed
     */
    public function view(User $user, Department $department)
    {
        return $user->hasPermission("Department","view");
    }

    /**
     * Determine whether the user can create departments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function insert(User $user)
    {
      
        return $user->hasPermission("Department","insert");
    }

    /**
     * Determine whether the user can update the department.
     *
     * @param  \App\User  $user
     * @param  \App\Department  $department
     * @return mixed
     */
    public function update(User $user, Department $department)
    {
        return $user->hasPermission("Department","update");
    }

    /**
     * Determine whether the user can delete the department.
     *
     * @param  \App\User  $user
     * @param  \App\Department  $department
     * @return mixed
     */
    public function delete(User $user, Department $department)
    {
        return $user->hasPermission("Department","delete");
    }

    /**
     * Determine whether the user can restore the department.
     *
     * @param  \App\User  $user
     * @param  \App\Department  $department
     * @return mixed
     */
    public function restore(User $user, Department $department)
    {
        return $user->hasPermission("Department","restore");
    }

    /**
     * Determine whether the user can permanently delete the department.
     *
     * @param  \App\User  $user
     * @param  \App\Department  $department
     * @return mixed
     */
    public function destroy(User $user, Department $department)
    {
        return $user->hasPermission("Department","destroy");
    }

}
