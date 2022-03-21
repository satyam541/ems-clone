<?php

namespace App\Observers;

use App\Models\Repair;

class RepairObserver
{
    /**
     * Handle the repair "created" event.
     *
     * @param  \App\Models\Repair  $repair
     * @return void
     */
    public function created(Repair $repair)
    {
      
        $action="Repair Created: ";
        saveLogs( $action,$repair);
    }

    /**
     * Handle the repair "updated" event.
     *
     * @param  \App\Models\Repair  $repair
     * @return void
     */
    public function updated(Repair $repair)
    {
        $action="Repair Updated: ";
        saveLogs( $action,$repair);
    }

    /**
     * Handle the repair "deleted" event.
     *
     * @param  \App\Models\Repair  $repair
     * @return void
     */
    public function deleted(Repair $repair)
    {   
        $action="Repair Deleted: ";
        saveLogs( $action,$repair);
    }

    /**
     * Handle the repair "restored" event.
     *
     * @param  \App\Models\Repair  $repair
     * @return void
     */
    public function restored(Repair $repair)
    {
        $action="Repair Restored: ";
        saveLogs( $action,$repair);
    }

    /**
     * Handle the repair "force deleted" event.
     *
     * @param  \App\Models\Repair  $repair
     * @return void
     */
    public function forceDeleted(Repair $repair)
    {    
        $action="Repair Force Deleted: ";
        saveLogs( $action,$repair);
    }
}
