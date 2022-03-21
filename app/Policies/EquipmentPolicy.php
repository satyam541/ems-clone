<?php

namespace App\Policies;

use App\User;
use App\Models\Equipment;
use Illuminate\Auth\Access\HandlesAuthorization;

class EquipmentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function managerEquipmentList(User $user,Equipment $equipment)
    {
        return $user->hasPermission('Equipment','managerEquipmentList');
    }
    public function it(User $user,Equipment $equipment)
    {
        return $user->hasPermission('Equipment','it');
    }
    public function action(User $user,Equipment $equipment)
    {
        return $user->hasPermission('Equipment','action');
    }
    public function destroy(User $user,Equipment $equipment)
    {
        return $user->hasPermission('Equipment','destroy');
    }
    public function restore(User $user,Equipment $equipment)
    {
        return $user->hasPermission('Equipment','restore');
    }
    
}
