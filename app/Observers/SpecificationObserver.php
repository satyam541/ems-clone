<?php

namespace App\Observers;

use App\Models\Specification;

class SpecificationObserver
{
    /**
     * Handle the specification "created" event.
     *
     * @param  \App\Models\Specification  $specification
     * @return void
     */
    public function created(Specification $specification)
    {
        
        $action="Specification Created: ";
        saveLogs($action,$specification);
    }

    /**
     * Handle the specification "updated" event.
     *
     * @param  \App\Models\Specification  $specification
     * @return void
     */
    public function updated(Specification $specification)
    {   
        $action="Specification Updated: ";
        saveLogs( $action,$specification);
    }

    /**
     * Handle the specification "deleted" event.
     *
     * @param  \App\Models\Specification  $specification
     * @return void
     */
    public function deleted(Specification $specification)
    {
        $action="Specification Deleted: ";
        saveLogs( $action,$specification);
    }

    /**
     * Handle the specification "restored" event.
     *
     * @param  \App\Models\Specification  $specification
     * @return void
     */
    public function restored(Specification $specification)
    {    
        $action="Specification Restored: ";
        saveLogs($action,$specification);
    }

    /**
     * Handle the specification "force deleted" event.
     *
     * @param  \App\Models\Specification  $specification
     * @return void
     */
    public function forceDeleted(Specification $specification)
    {  
        $action="Specification Force Deleted: ";
        saveLogs( $action,$specification);
    }
}
