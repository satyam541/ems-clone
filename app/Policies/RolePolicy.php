<?php

namespace App\Policies;

use App\Models\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Role  $role
     * @return mixed
     */
    public function view(User $user, Role $role)
    {
        return $user->hasPermission("Role","view");
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function insert(User $user)
    {
        return $user->hasPermission("Role","insert");
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Role  $role
     * @return mixed
     */
    public function update(User $user, Role $role)
    {
        return $user->hasPermission("Role","update");
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Role  $role
     * @return mixed
     */
    public function delete(User $user, Role $role)
    {
        return $user->hasPermission("Role","delete");
    }

    /**
     * Determine whether the user can restore the role.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Role  $role
     * @return mixed
     */
    public function restore(User $user, Role $role)
    {
        return $user->hasPermission("Role","restore");
    }

    /**
     * Determine whether the user can permanently delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Role  $role
     * @return mixed
     */
    public function destroy(User $user, Role $role)
    {
        return $user->hasPermission("Role","destroy");
    }

    public function assignRole(User $user, Role $role)
    {
        return $user->hasPermission("Role", 'assignRole');
    }
    
    
}
