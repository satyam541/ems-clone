<?php

namespace App\Policies;

use App\Models\Module;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModulePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the module.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Module  $module
     * @return mixed
     */
    public function view(User $user, Module $module)
    {
        return $user->hasPermission("Module","view");
    }

    /**
     * Determine whether the user can create modules.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function insert(User $user)
    {
        return $user->hasPermission("Module","insert");
    }

    /**
     * Determine whether the user can update the module.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Module  $module
     * @return mixed
     */
    public function update(User $user, Module $module)
    {
        return $user->hasPermission("Module","update");
    }

    /**
     * Determine whether the user can delete the module.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Module  $module
     * @return mixed
     */
    public function delete(User $user, Module $module)
    {
        return $user->hasPermission("Module","delete");
    }

    /**
     * Determine whether the user can restore the module.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Module  $module
     * @return mixed
     */
    public function restore(User $user, Module $module)
    {
        return $user->hasPermission("Module","restore");
    }

    /**
     * Determine whether the user can permanently delete the module.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Module  $module
     * @return mixed
     */
    public function destroy(User $user, Module $module)
    {
        return $user->hasPermission("Module","destroy");
    }

}
