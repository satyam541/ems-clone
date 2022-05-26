<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Interview;
use App\User;

class InterviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any interviews.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the interview.
     *
     * @param  \App\User  $user
     * @param  \App\Interview  $interview
     * @return mixed
     */
    public function view(User $user, Interview $interview)
    {
        return $user->hasPermission("Interview","view");
    }

    /**
     * Determine whether the user can create interviews.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission("Interview","create");
    }

    /**
     * Determine whether the user can update the interview.
     *
     * @param  \App\User  $user
     * @param  \App\Interview  $interview
     * @return mixed
     */
    public function update(User $user, Interview $interview)
    {
        return $user->hasPermission("Interview","update");
    }

    /**
     * Determine whether the user can delete the interview.
     *
     * @param  \App\User  $user
     * @param  \App\Interview  $interview
     * @return mixed
     */
    public function delete(User $user, Interview $interview)
    {
        return $user->hasPermission("Interview","delete");
    }

    /**
     * Determine whether the user can restore the interview.
     *
     * @param  \App\User  $user
     * @param  \App\Interview  $interview
     * @return mixed
     */
    public function createCredentials(User $user, Interview $interview)
    {
        return $user->hasPermission("Interview","createCredentials");
    }

    /**
     * Determine whether the user can permanently delete the interview.
     *
     * @param  \App\User  $user
     * @param  \App\Interview  $interview
     * @return mixed
     */
    public function forceDelete(User $user, Interview $interview)
    {
        //
    }
}
