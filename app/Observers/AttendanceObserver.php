<?php

namespace App\Observers;

use App\Models\Attendance;

class AttendanceObserver
{
    /**
     * Handle the attendance "created" event.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return void
     */
    public function created(Attendance $attendance)
    {
        $action = "Attendance Created: ".$attendance->employee->name;
        saveLogs($action,$attendance);
    }

    /**
     * Handle the attendance "updated" event.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return void
     */
    public function updated(Attendance $attendance)
    {
        $action = "Attendance Updated: ".$attendance->employee->name;
        saveLogs($action,$attendance);
     
    }

    /**
     * Handle the attendance "deleted" event.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return void
     */
    public function deleted(Attendance $attendance)
    {
        $action = "Attendance Deleted: ".$attendance->employee->name;
        saveLogs( $action,$attendance);
    }

    /**
     * Handle the attendance "restored" event.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return void
     */
    public function restored(Attendance $attendance)
    {
        $action = "Attendance Restored: ".$attendance->employee->name;
        saveLogs( $action,$attendance);
    }

    /**
     * Handle the attendance "force deleted" event.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return void
     */
    public function forceDeleted(Attendance $attendance)
    {
        $action = "Attendance Permanently Deleted: ".$attendance->employee->name;
        saveLogs($action,$attendance);
    }
}
