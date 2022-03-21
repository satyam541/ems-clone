<?php

namespace App\Policies;
use App\Models\EquipmentRequests;
use App\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class EquipmentRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */

    public function managerEntityRequestList(User $user,EquipmentRequests $EntityRequests)
    {
        return $user->hasPermission('EntityRequests','managerEntityRequestList');
    }
    public function itEntityRequestList(User $user,EquipmentRequests $EntityRequests)
    {
        return $user->hasPermission('EntityRequests','itEntityRequestList');
    }
    public function managerEquipmentList(User $user,EquipmentRequests $EntityRequests)
    {
        return $user->hasPermission('EntityRequests','managerEquipmentList');
    }

   
}
