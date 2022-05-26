<?php

namespace App\Policies;

use App\User;
use App\Models\Asset;
use App\Models\Badge;
use App\Models\Employee;
use App\Models\AssetType;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\AssetSubType;
use App\Models\AssetCategory;
use App\Models\Qualification;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */


    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        return $user->hasPermission("User", "view");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function insert(User $user)
    {
        // return false;
        return $user->hasPermission("User", "insert");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        return $user->hasPermission("User", "update");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return $user->hasPermission("User", "delete");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        return $user->hasPermission("User", "restore");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function itDashboard(User $user, User $model)
    {
        return $user->hasPermission("User", "itDashboard");
    }
    public function hrDashboard(User $user, User $model)
    {
        return $user->hasPermission("User", "hrDashboard");
    }
    public function managerDashboard(User $user, User $model)
    {
        return $user->hasPermission("User", "managerDashboard");
    }
    public function employeeDashboard(User $user, User $model)
    {
        return $user->hasPermission("User", "employeeDashboard");
    }
    public function trash(User $user, User $model)
    {
        return $user->hasPermission("User", "trash");
    }
    public function leaveDashboard(User $user, User $model)
    {
        return $user->hasPermission('User', 'leaveDashboard');
    }
    public function hr(User $user, User $model)
    {
        $user_id = User::havingRole(['HR', 'admin']);
        if (in_array(auth()->user()->id, $user_id)) {

            return true;
        }
        return false;
    }
    public function checkPermission()
    {

        if (
            auth()->user()->can('hrEmployeeList', new Employee())  ||  auth()->user()->can('view', new LeaveType()) ||
            auth()->user()->can('view', new  Department()) ||   auth()->user()->can('view', new Badge())     ||
            auth()->user()->can('view', new Qualification())   || auth()->user()->can('hrUpdateEmployee', new Employee())
        ) 
        {
            return true;
        } else
         {
            return false;
        }
    }

    public function assetPermission()
    {
        if (auth()->user()->can('view', new Asset()) || auth()->user()->can('view', new AssetSubType()) || 
            auth()->user()->can('view', new AssetType()) || auth()->user()->can('view', new AssetCategory()) || 
            auth()->user()->can('assignmentList', new Asset()) || auth()->user()->can('dashboard', new Asset()))
            {
                return true;
            }
            else{
                return false;
            }
    }

    public function powerUser(User $user, User $model)
    {
        return $user->hasPermission('User', 'powerUser');
    }
}
