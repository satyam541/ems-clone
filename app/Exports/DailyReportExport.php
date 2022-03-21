<?php

namespace App\Exports;

use App\Models\DailyReport;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DailyReportExport implements FromCollection,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

    function __construct()
    {
       
    }

    public function collection()
    {
        $employees              = Employee::query();

        if (request()->has('date')) {
            $date               = request()->date;
        }else{
            $date               = Carbon::today()->format('Y-m-d');
        }
        if(request()->has('department_id') && !empty(request()->department_id))
        {  
            $employees      = $employees->whereHas('department',function($query){
                                    $query->where('id',request()->department_id);
                                });
        }else{
            $departments    = Department::query();

            if(!auth()->user()->hasRole('hr') && auth()->user()->employee->managerDepartments->isNotEmpty())
            {
                $departments    = $departments->where('manager_id',auth()->user()->employee->id);
            }

            $employees    = $employees->whereHas('department',function($query) use($departments){
                $departmentsIds = $departments->pluck('id','id');
                $query->whereIn('id',$departmentsIds);
            });
        }
        
        $employees          = $employees->with(['workReports' => function($query) use($date) {
                                    $query->where('report_date',$date);
                                }]);
        

        $employees          = $employees->with(['leaves' => function($query) use($date) {
                                    $query->whereDate('from_date', '<=',$date)
                                            ->whereDate('to_date', '>=',$date)
                                            ->where('status', 'Approved');
                                }]);

        return $employees->orderBy('department_id')->get();
    }

    public function map($employee): array
    {
        return [

            $employee->name,
            $employee->department->name,
            optional($employee->leaves)->first()->leave_type ?? 'Present',
            optional($employee->workReports)->first()->task1 ?? '' ,
            optional($employee->workReports)->first()->task2 ?? '' ,
            optional($employee->workReports)->first()->task3 ?? '' ,
            optional($employee->workReports)->first()->task4 ?? '' ,
            optional($employee->workReports)->first()->task5 ?? '' ,
            optional($employee->workReports)->first()->task6 ?? '' 
            
        ];
    }
    public function headings() : array{
        return [
                'Name',
                'Deparmtent',
                'Leave Nature',
                'Task 1',          
                'Task 2',          
                'Task 3',          
                'Task 4',          
                'Task 5',          
                'Task 6'          
        ];
    }
}
