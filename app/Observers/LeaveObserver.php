<?php

namespace App\Observers;

use App\Models\Leave;
use App\Models\LeaveLogs;

class LeaveObserver
{
    /**
     * Handle the leave "created" event.
     *
     * @param  \App\Models\Leave  $leave
     * @return void
     */
    public function created(Leave $leave)
    {
        $action = "Leave Create: ".$leave->user->name;
        saveLogs( $action,$leave);
    //     $action         = "Leave Created: ". $leave->type;
    //     saveLogs($action, $leave);
    //     $leaveLog                   =  new LeaveLogs();
    //     $leaveLog->leave_id         =  $leave->id;
    //     $leaveLog->user_id          =   auth()->user()->id;
    //     if(auth()->user()->id!=$leave->user_id)
    //     {
    //         $leaveLog->remarks =   "Leave added by ".auth()->user()->name;
    //     }
    //     else
    //     {
    //         $leaveLog->remarks =   "Leave applied by ".auth()->user()->name;
    //     }
    //     $leaveLog->action      =    'created';
    //     $leaveLog->save();
    }

    /**
     * Handle the leave "updated" event.
     *
     * @param  \App\Models\Leave  $leave
     * @return void
     */
    public function updated(Leave $leave)
    {
        $action = "Leave Updated: ". $leave->type . ". Status: " . $leave->status;
        saveLogs($action, $leave);
        // $leaveLog                   =  new LeaveLogs();
        // $leaveLog->leave_id         =  $leave->leave_id;
        // $leaveLog->user_id          =   auth()->user()->id;
        // $leaveLog->remarks          =   "Leave updated by".auth()->user()->name;
        // $leaveLog->action           =    $leave->status;
        // $leaveLog->save();
    }

}
