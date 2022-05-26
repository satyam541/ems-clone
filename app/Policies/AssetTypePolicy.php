<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\AssetType;
use App\User;

class AssetTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any asset types.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        
    }

    /**
     * Determine whether the user can view the asset type.
     *
     * @param  \App\User  $user
     * @param  \App\AssetType  $assetType
     * @return mixed
     */
    public function view(User $user, AssetType $assetType)
    {
        return $user->hasPermission('AssetType','view');
    }

    /**
     * Determine whether the user can create asset types.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('AssetType','create');
    }

    /**
     * Determine whether the user can update the asset type.
     *
     * @param  \App\User  $user
     * @param  \App\AssetType  $assetType
     * @return mixed
     */
    public function update(User $user, AssetType $assetType)
    {
        return $user->hasPermission('AssetType','update');
    }

    /**
     * Determine whether the user can delete the asset type.
     *
     * @param  \App\User  $user
     * @param  \App\AssetType  $assetType
     * @return mixed
     */
    public function delete(User $user, AssetType $assetType)
    {
        return $user->hasPermission('AssetType','delete');
    }

    /**
     * Determine whether the user can restore the asset type.
     *
     * @param  \App\User  $user
     * @param  \App\AssetType  $assetType
     * @return mixed
     */
    public function restore(User $user, AssetType $assetType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the asset type.
     *
     * @param  \App\User  $user
     * @param  \App\AssetType  $assetType
     * @return mixed
     */
    public function forceDelete(User $user, AssetType $assetType)
    {
        //
    }
}
