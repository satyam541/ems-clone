<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemoteAttendance extends Model
{
    protected $table = 'remote_attendance';
  
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }

    public function getLeaveNature()
    {
        $date   = $this->date;
        
        $leave  = Leave::where('employee_id',$this->employee_id)->where(function($subQuery) use($date){

                    $subQuery->where(function($query1) use($date)
                    {
                        $query1->where('from_Date','<=',$date)->where('to_Date','>=',$date);

                    })->orWhere(function($query2) use($date){

                        $query2->whereBetween('from_Date',[$date,$date]);
                    });

                })->where('status','Approved')->first();

        if(!empty($leave))
        {
            return $leave->leave_session;
        }

        return "Present";
    }
}
