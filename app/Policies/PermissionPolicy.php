<?php

namespace App\Policies;

use App\Models\Permission;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the permission.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Permission  $permission
     * @return mixed
     */
    public function view(User $user, Permission $permission)
    {
        return $user->hasPermission("Permission","view");
    }

    /**
     * Determine whether the user can create permissions.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function insert(User $user)
    {
        return $user->hasPermission("Permission","insert");
    }

    /**
     * Determine whether the user can update the permission.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Permission  $permission
     * @return mixed
     */
    public function update(User $user, Permission $permission)
    {
        return $user->hasPermission("Permission","update");
    }

    /**
     * Determine whether the user can delete the permission.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Permission  $permission
     * @return mixed
     */
    public function delete(User $user, Permission $permission)
    {
        return $user->hasPermission("Permission","delete");
    }

    /**
     * Determine whether the user can restore the permission.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Permission  $permission
     * @return mixed
     */
    public function restore(User $user, Permission $permission)
    {
        return $user->hasPermission("Permission","restore");
    }

    /**
     * Determine whether the user can permanently delete the permission.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Permission  $permission
     * @return mixed
     */
    public function destroy(User $user, Permission $permission)
    {
        return $user->hasPermission("Permission","destroy");
    }

    public function assignPermission(User $user,Permission $permission)
    {
        return $user->hasPermission('Permission','assignPermission');
    }
}
