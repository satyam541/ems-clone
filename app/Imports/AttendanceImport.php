<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToArray;
use Carbon\Carbon;

class AttendanceImport implements ToArray
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function array(Array $rows)
    {
        ini_set('max_execution_time', "-1");
        ini_set("memory_limit", "-1");
        $employee = null;
        $employee_name = $employee_code = null;
        foreach ($rows as $index => $row) 
        {
            if(empty(array_filter($row)) || $index < 10)
            {
                continue;
            }
            if(in_array('Employee Code',$row) || in_array('Employee Name',$row))
            {
                $index = array_search('Employee Code',$row);
                $employee_code = $row[$index+2];
                $index = array_search('Employee Name',$row);
                $employee_name = $row[$index+3];
                if(is_numeric($employee_name) && is_string($employee_code))
                {
                    $code = $employee_code;
                    $employee_code = $employee_name;
                    $employee_name = $code;
                }
            }
            if(!empty($employee_code) && !empty($employee_name))
            {
                $employee = Employee::where('name',$employee_name)
                                        ->where('registration_id',$employee_code)
                                        ->first();
            }

            if(preg_grep('/^[0-9]{2}-(([a-zA-Z]{1})[a-z]{2,3})-[0-9]{4}$/', $row))
            {
                $present   = ['P', '½P', '½P(WO)', 'WOP'];
                $absent    = ['A'];
                $weekoff   = ['WO'];
                $status    = $row[17];
                
                if(in_array($status, $present))
                {
                    $status = "Present";
                }
                else if(in_array($status, $absent))
                {
                    $status = "Absent";
                }
                else if(in_array($status, $weekoff))
                {
                    $status = "WeekEnd";
                }
                $attendance                     =   new Attendance();
                if(empty($employee->id))
                    continue;
                $attendance->employee_id        =   $employee->id;
                $attendance->status             =   $status;
                if($status == "present")
                {
                    $attendance->entry_time     =   Carbon::parse($row[7])->format('h:i:s');
                    $pattern                    =   "/\(SE\)$/";
                    if(!preg_match($pattern,$row[9]))
                    {
                        $exit_time = preg_replace($pattern, '', $row[9]);
                        $attendance->exit_time  =   Carbon::parse($exit_time)->format('h:i:s');
                    }
                }
                $attendance->attendance_date    =   Carbon::parse($row[1])->format('Y-m-d');
                $attendance->punch_status       =   $row[18];
                $attendance->uploaded_by        =   auth()->user()->id;
                $attendance->save();
            }
        }
    }
}
