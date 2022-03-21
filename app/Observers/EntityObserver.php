<?php

namespace App\Observers;

use App\Models\Entity;

class EntityObserver
{
    /**
     * Handle the entity "created" event.
     *
     * @param  \App\Models\Entity  $entity
     * @return void
     */
    public function created(Entity $entity)
    {
        
        $action="Entity Created: ".$entity->name;
        saveLogs( $action,$entity);
    }

    /**
     * Handle the entity "updated" event.
     *
     * @param  \App\Models\Entity  $entity
     * @return void
     */
    public function updated(Entity $entity)
    {
        $action="Entity Updated: ".$entity->name;
        saveLogs($action,$entity);
    }

    /**
     * Handle the entity "deleted" event.
     *
     * @param  \App\Models\Entity  $entity
     * @return void
     */
    public function deleted(Entity $entity)
    {
        $action="Entity Deleted: ".$entity->name;
        saveLogs( $action,$entity);
    }

    /**
     * Handle the entity "restored" event.
     *
     * @param  \App\Models\Entity  $entity
     * @return void
     */
    public function restored(Entity $entity)
    {
        $action="Entity Restored: ".$entity->name;
        saveLogs( $action,$entity);
    }

    /**
     * Handle the entity "force deleted" event.
     *
     * @param  \App\Models\Entity  $entity
     * @return void
     */
    public function forceDeleted(Entity $entity)
    {
        $action="Entity Force Deleted: ".$entity->name;
        saveLogs( $action,$entity);
    }
}
