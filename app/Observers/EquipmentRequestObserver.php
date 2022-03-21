<?php

namespace App\Observers;

use App\Models\EquipmentRequests;

class EquipmentRequestObserver
{
    /**
     * Handle the entity requests "created" event.
     *
     * @param  \App\EntityRequests  $entityRequests
     * @return void
     */
    public function created(EquipmentRequests $equipmentRequests)
    {
        $action = "Entity Request Created, Entity Request Id-".$equipmentRequests->id;
        saveLogs($action,$equipmentRequests);
    }

    /**
     * Handle the entity requests "updated" event.
     *
     * @param  \App\EntityRequests  $entityRequests
     * @return void
     */
    public function updated(EquipmentRequests $equipmentRequests)
    {  
        $action = "Entity Request Updated, Entity Request Id-".$equipmentRequests->id;
        if(!empty($equipmentRequests->equipment_id))
       {
        $action = "Entity Request Approval by IT, Entity Request Id-: ".$equipmentRequests->id;
       }
        saveLogs($action,$equipmentRequests);
    }

    /**
     * Handle the entity requests "deleted" event.
     *
     * @param  \App\EntityRequests  $entityRequests
     * @return void
     */
    public function deleted(EquipmentRequests $equipmentRequests)
    {
        $action = "Entity Request SoftDeleted, Entity Request-".$equipmentRequests->id;
        saveLogs($action,$equipmentRequests);
    }

    /**
     * Handle the entity requests "restored" event.
     *
     * @param  \App\EntityRequests  $entityRequests
     * @return void
     */
    public function restored(EquipmentRequests $equipmentRequests)
    {
        $action = "Entity Request Restored, Entity Request-".$equipmentRequests->id;
        saveLogs($action,$equipmentRequests);
    }

    /**
     * Handle the entity requests "force deleted" event.
     *
     * @param  \App\EntityRequests  $entityRequests
     * @return void
     */
    public function forceDeleted(EquipmentRequests $equipmentRequests)
    {
        $action = "Entity Request forceDeleted, Entity Request-".$equipmentRequests->id;
        saveLogs($action,$equipmentRequests);
    }
}
