<?php

namespace App\Policies;

use App\Models\Stock;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any stocks.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->hasPermission("Stock","view");
    }

    public function create(User $user)
    {
        return $user->hasPermission("Stock","insert");
    }

    public function update(User $user, Stock $stock)
    {
        return $user->hasPermission("Stock","edit");
    }


    public function delete(User $user, Stock $stock)
    {
        return $user->hasPermission("Stock","delete");
    }

    public function detailView(User $user, Stock $stock)
    {
        return $user->hasPermission("Stock","detailView");
    }

    public function quotationAction(User $user)
    {
        return $user->hasPermission("Quotation","action");
    }

    public function quotationView(User $user)
    {
        return $user->hasPermission("Quotation","view");
    }

    public function quotationCreate(User $user)
    {
        return $user->hasPermission("Quotation","create");
    }

    public function quotationEdit(User $user)
    {
        return $user->hasPermission("Quotation","edit");
    }


    public function quotationDelete(User $user)
    {
        return $user->hasPermission("Quotation","delete");
    }
    
    public function equipmentAssign(User $user)
    {
        return $user->hasPermission('Equipment','assign');
    }

    /**
     * Determine whether the user can view the stock.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Stock  $stock
     * @return mixed
     */
    /**
     * Determine whether the user can create stocks.
     *
     * @param  \App\User  $user
     * @return mixed
     */

    /**
     * Determine whether the user can update the stock.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Stock  $stock
     * @return mixed
     */

}
