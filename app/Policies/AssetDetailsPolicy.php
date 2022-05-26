<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\AssetDetails;
use App\User;

class AssetDetailsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any asset details.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the asset detail.
     *
     * @param  \App\User  $user
     * @param  \App\AssetDetail  $assetDetail
     * @return mixed
     */
    public function view(User $user, AssetDetails $assetDetail)
    {
        return $user->hasPermission("AssetDetails","view");
    }

    /**
     * Determine whether the user can create asset details.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission("AssetDetails","create");
    }

    /**
     * Determine whether the user can update the asset detail.
     *
     * @param  \App\User  $user
     * @param  \App\AssetDetail  $assetDetail
     * @return mixed
     */
    public function update(User $user, AssetDetails $assetDetail)
    {
        return $user->hasPermission("AssetDetails","update");
    }

    /**
     * Determine whether the user can delete the asset detail.
     *
     * @param  \App\User  $user
     * @param  \App\AssetDetail  $assetDetail
     * @return mixed
     */
    public function delete(User $user, AssetDetails $assetDetail)
    {
        return $user->hasPermission("AssetDetails","delete");
    }

    /**
     * Determine whether the user can restore the asset detail.
     *
     * @param  \App\User  $user
     * @param  \App\AssetDetail  $assetDetail
     * @return mixed
     */
    public function restore(User $user, AssetDetails $assetDetail)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the asset detail.
     *
     * @param  \App\User  $user
     * @param  \App\AssetDetail  $assetDetail
     * @return mixed
     */
    public function forceDelete(User $user, AssetDetails $assetDetail)
    {
        //
    }
}
