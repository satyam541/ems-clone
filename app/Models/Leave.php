<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Str;
class Leave extends Model
{
    protected $table = 'leave';
    protected $guarded = ['id'];
    private static $status = ['Pending', 'Cancel', 'Rejected', 'Approved'];
    //consist of type => duration 
    private static $leaveNature = ['Casual'=>'Casual', 'Sick'=>'Sick', 'Emergency'=>'Emergency',
     'Marriage'=>'Marriage','Medical'=>'Medical','Exam'=>'Exam'];
    private static $leaveTypes = ['Full Day' => ['Full Day'], 'Half Day' => ['First Half', 'Second Half']];
    private static $shortLeaveTimings=['9:00-11:00'=>'9:00-11:00','11:00-1:00'=>'11:00-1:00','2:00-4:00'=>'2:00-4:00','4:00-6:00'=>'4:00-6:00'];
    protected $appends=['duration'];
    protected $additional_attributes=['duration'];
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id')->with(['department'])->withoutGlobalScopes();
    }

    public static function getTypes()
    {        
        return self :: $leaveTypes;
    }

    public static function getStatus()
    {
        return self :: $status;
    }
    public static function getLeaveNature()
    {
        return self :: $leaveNature;
    }
    public static function getshortLeaveTimings()
    {
        return self :: $shortLeaveTimings;
    }
    public function activity()
    {  
       return  $this->morphMany('App\Models\ActivityLog','module');
    }
    public function leaveCancellation()
    {
        $date   =   $this->from_date;
        $time   =   Str::before($this->timing,'-');
        $today=Carbon::now();
        if($this->leave_type!='Half day(Second half)')
        {
        $time   =   Carbon::parse($time)->addHour()->format('H:i');
        }
        if($today->format('Y-m-d')<=$date)
        {
            if($today->format('Y-m-d')==$date)
            {
                return strtotime($today->format('H:i:s'))>strtotime($time);
            }
            return false;
        }
        else
        {
            return true;
        }
    }

    public function getDurationAttribute()
    {
        $from_date  = Carbon::parse($this->from_date);
        $to_date    = Carbon::parse($this->to_date);
        $days = $from_date->diffInDays($to_date);
        $sundays = intval($days / 7) + ($from_date->format('N') + $days % 7 >= 7);
        return $days+1-$sundays;
    }
    public function forwardedLeave()
    {
          
        if((in_array($this->leave_nature,['Marriage','Exam','Medical']) && $this->duration >2 ) ||  ($this->duration>2) )
        {
          
            return false;
        }
        return true;
    }

}