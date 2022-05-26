<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\AssetSubType;
use App\User;

class AssetSubTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any asset sub types.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the asset sub type.
     *
     * @param  \App\User  $user
     * @param  \App\AssetSubType  $assetSubType
     * @return mixed
     */
    public function view(User $user, AssetSubType $assetSubType)
    {
        return $user->hasPermission("AssetSubType","view");
    }

    /**
     * Determine whether the user can create asset sub types.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission("AssetSubType","create");
    }

    /**
     * Determine whether the user can update the asset sub type.
     *
     * @param  \App\User  $user
     * @param  \App\AssetSubType  $assetSubType
     * @return mixed
     */
    public function update(User $user, AssetSubType $assetSubType)
    {
        return $user->hasPermission("AssetSubType","update");
    }

    /**
     * Determine whether the user can delete the asset sub type.
     *
     * @param  \App\User  $user
     * @param  \App\AssetSubType  $assetSubType
     * @return mixed
     */
    public function delete(User $user, AssetSubType $assetSubType)
    {
        return $user->hasPermission("AssetSubType","delete");
    }

    /**
     * Determine whether the user can restore the asset sub type.
     *
     * @param  \App\User  $user
     * @param  \App\AssetSubType  $assetSubType
     * @return mixed
     */
    public function restore(User $user, AssetSubType $assetSubType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the asset sub type.
     *
     * @param  \App\User  $user
     * @param  \App\AssetSubType  $assetSubType
     * @return mixed
     */
    public function forceDelete(User $user, AssetSubType $assetSubType)
    {
        //
    }
}
