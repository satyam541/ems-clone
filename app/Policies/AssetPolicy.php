<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Asset;
use App\User;

class AssetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any assets.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        
    }

    /**
     * Determine whether the user can view the asset.
     *
     * @param  \App\User  $user
     * @param  \App\Asset  $asset
     * @return mixed
     */
    public function view(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","view");
    }

    /**
     * Determine whether the user can create assets.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","create");
    }

    /**
     * Determine whether the user can update the asset.
     *
     * @param  \App\User  $user
     * @param  \App\Asset  $asset
     * @return mixed
     */
    public function update(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","update");
    }

    /**
     * Determine whether the user can delete the asset.
     *
     * @param  \App\User  $user
     * @param  \App\Asset  $asset
     * @return mixed
     */
    public function delete(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","delete");
    }

    /**
     * Determine whether the user can restore the asset.
     *
     * @param  \App\User  $user
     * @param  \App\Asset  $asset
     * @return mixed
     */
    public function restore(User $user, Asset $asset)
    {
        
    }

    /**
     * Determine whether the user can permanently delete the asset.
     *
     * @param  \App\User  $user
     * @param  \App\Asset  $asset
     * @return mixed
     */
    public function forceDelete(User $user, Asset $asset)
    {
        //
    }
    
    public function assignEquipments(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","assignEquipments");
    }

    public function assignmentList(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","assignmentList");
    }

    public function assignAsset(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","assignAsset");
    }

    public function dashboard(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","dashboard");
    }
    public function modify(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","modify");
    }
}
