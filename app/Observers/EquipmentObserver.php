<?php

namespace App\Observers;
use App\Models\Equipment;


class EquipmentObserver
{
    public function created(Equipment $equipment)
    {
    
        $action = "Equipment Created: ".$equipment->entity->name;
        saveLogs($action,$equipment);
    }

  
    public function updated(Equipment $equipment)
    {
        $action = "Equipment Updated: ".$equipment->entity->name;
        saveLogs($action,$equipment);
    }

    public function deleted(Equipment $equipment)
    {
        $action = "Equipment Deleted: ".$equipment->entity->name;
        saveLogs($action,$equipment);
    }

 
    public function restored(Equipment $equipment)
    {
        $action = "Equipment Restored: ".$equipment->entity->name;
        saveLogs($action,$equipment);
    }

 
    public function forceDeleted(Equipment $equipment)
    {
        $action = "Equipment Force Deleted: ".$equipment->entity->name;
        saveLogs( $action,$equipment);
    }
}
