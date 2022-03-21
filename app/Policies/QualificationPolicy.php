<?php

namespace App\Policies;

use App\Models\Qualification;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QualificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any qualifications.
     *
     * @param  \App\User  $user
     * @return mixed
     */
 
    /**
     * Determine whether the user can view the qualification.
     *
     * @param  \App\User  $user
     * @param  \App\Qualification  $qualification
     * @return mixed
     */
    public function view(User $user, Qualification $qualification)
    {
        return $user->hasPermission("Qualification","view");
    }

    /**
     * Determine whether the user can create qualifications.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function insert(User $user)
    {
        return $user->hasPermission("Qualification","insert");
    }

    /**
     * Determine whether the user can update the qualification.
     *
     * @param  \App\User  $user
     * @param  \App\Qualification  $qualification
     * @return mixed
     */
    public function update(User $user, Qualification $qualification)
    {
        return $user->hasPermission("Qualification","update");
    }

    /**
     * Determine whether the user can delete the qualification.
     *
     * @param  \App\User  $user
     * @param  \App\Qualification  $qualification
     * @return mixed
     */
    public function delete(User $user, Qualification $qualification)
    {
        return $user->hasPermission("Qualification","delete");
    }

    /**
     * Determine whether the user can restore the qualification.
     *
     * @param  \App\User  $user
     * @param  \App\Qualification  $qualification
     * @return mixed
     */
    public function restore(User $user, Qualification $qualification)
    {
        return $user->hasPermission("Qualification","restore");
    }

    /**
     * Determine whether the user can permanently delete the qualification.
     *
     * @param  \App\User  $user
     * @param  \App\Qualification  $qualification
     * @return mixed
     */
    public function destroy(User $user, Qualification $qualification)
    {
        return $user->hasPermission("Qualification","destroy");
    }
}
