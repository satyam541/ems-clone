<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class DailyReport extends Model
{
    protected $table        = 'employee_daily_reports';
    protected $primaryKey   = 'id';
    protected $guarded      = ['id'];

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }

    function employeeLeaveNature()
    {   
        $leaveNature  =  $this->employee->leaves->where('from_date', '<=', $this->report_date)
                            ->where('to_date', '>=', $this->report_date)->where('status', 'Approved')
                            ->first()->leave_type ?? null;
    
        return empty($leaveNature) ? 'Present' : $leaveNature;
    }
}
