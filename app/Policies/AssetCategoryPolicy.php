<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\AssetCategory;
use App\User;

class AssetCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any asset categories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the asset category.
     *
     * @param  \App\User  $user
     * @param  \App\AssetCategory  $assetCategory
     * @return mixed
     */
    public function view(User $user, AssetCategory $assetCategory)
    {
        return $user->hasPermission("AssetCategory","view");
    }

    /**
     * Determine whether the user can create asset categories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission("AssetCategory","create");
    }

    /**
     * Determine whether the user can update the asset category.
     *
     * @param  \App\User  $user
     * @param  \App\AssetCategory  $assetCategory
     * @return mixed
     */
    public function update(User $user, AssetCategory $assetCategory)
    {
        return $user->hasPermission("AssetCategory","update");
    }

    /**
     * Determine whether the user can delete the asset category.
     *
     * @param  \App\User  $user
     * @param  \App\AssetCategory  $assetCategory
     * @return mixed
     */
    public function delete(User $user, AssetCategory $assetCategory)
    {
        return $user->hasPermission("AssetCategory","delete");
    }

    /**
     * Determine whether the user can restore the asset category.
     *
     * @param  \App\User  $user
     * @param  \App\AssetCategory  $assetCategory
     * @return mixed
     */
    public function restore(User $user, AssetCategory $assetCategory)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the asset category.
     *
     * @param  \App\User  $user
     * @param  \App\AssetCategory  $assetCategory
     * @return mixed
     */
    public function forceDelete(User $user, AssetCategory $assetCategory)
    {
        //
    }
}
