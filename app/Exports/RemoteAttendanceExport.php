<?php

namespace App\Exports;

use App\Models\RemoteAttendance;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RemoteAttendanceExport implements FromCollection,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $departments        = Department::query();

        if(!auth()->user()->hasRole('hr') && auth()->user()->employee->managerDepartments->isNotEmpty())
        {
            $departments    = $departments->where('manager_id',auth()->user()->employee->id);
        }
        
        $departments        =   $departments->pluck('id')->toArray();
        $date               =   Carbon::today()->format('Y-m-d');

        $attendances        =   RemoteAttendance::with('employee')
                                    ->whereHas('employee', function ($query) use($departments){
                                        $query->whereIn('department_id',$departments);
                                    });

        if(request()->has('dateFrom') &&  request()->has('dateTo'))
        {
            $attendances    = $attendances->where(function($subQuery){

                                $subQuery->where(function($query1) 
                                {
                                    $query1->whereDate('date',request()->dateFrom);

                                })->orWhere(function($query2){

                                    $query2->whereBetween('date',[request()->dateFrom,request()->dateTo]);
                                });
                            });
        }
        else
        {
            $attendances->where('date',$date);
        }
                            
        if(request()->has('employee_id'))
        {
            $attendances    = $attendances->where('employee_id',request()->employee_id);
        }

        if(request()->has('department_id'))
        {
            $attendances    = $attendances->whereHas('employee', function ($query) use($departments){
                                        $query->where('department_id',request()->department_id);
                                    });
        }

        return $attendances->orderBy('created_at','desc')->get();
    }

    public function map($attendance): array
    {
        return [
            $attendance->employee->name,
            $attendance->employee->department->name,
            $attendance->getLeaveNature(),
            getFormatedDate($attendance->date),
            $attendance->punch_in,
            $attendance->punch_out ?? 'N/A'
        ];
    }
    public function headings() : array{
        return [
                'Name',
                'Deparmtent',
                'Nature',
                'Date',
                'Punch In',          
                'Punch Out'      
        ];
    }
}
