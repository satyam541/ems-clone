<?php

namespace App\Observers;

use App\Models\Interviewee;

class IntervieweeObserver
{
    /**
     * Handle the interviewee "created" event.
     *
     * @param  \App\Interviewee  $interviewee
     * @return void
     */
    public function created(Interviewee $interviewee)
    {
        //
    }

    /**
     * Handle the interviewee "updated" event.
     *
     * @param  \App\Interviewee  $interviewee
     * @return void
     */
    public function updated(Interviewee $interviewee)
    {
        $action='Interviewee Updated '.$interviewee->name;
       saveLogs($action,$interviewee);
    }

    /**
     * Handle the interviewee "deleted" event.
     *
     * @param  \App\Interviewee  $interviewee
     * @return void
     */
    public function deleted(Interviewee $interviewee)
    {
        saveLogs('Interviewee Deleted '.$interviewee->first_name,$interviewee);
    }

    /**
     * Handle the interviewee "restored" event.
     *
     * @param  \App\Interviewee  $interviewee
     * @return void
     */
    public function restored(Interviewee $interviewee)
    {
        saveLogs('Interviewee Restored '.$interviewee->first_name,$interviewee);
    }

    /**
     * Handle the interviewee "force deleted" event.
     *
     * @param  \App\Interviewee  $interviewee
     * @return void
     */
    public function forceDeleted(Interviewee $interviewee)
    {
        saveLogs('Interviewee Permanently Deleted '.$interviewee->first_name,$interviewee);
    }
}
