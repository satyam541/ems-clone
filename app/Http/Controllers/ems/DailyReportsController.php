<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Department;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\DailyReportExport;
use App\Models\Employee;
use App\Models\Leave;
use Maatwebsite\Excel\Facades\Excel;

class DailyReportsController extends Controller
{

    public function __construct()
    {
        $this->end          = Carbon::createFromTimeString('08:30','Asia/Kolkata')->format('Y-m-d H:i:s');
        $this->now          = Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s');
        $this->today        = Carbon::today('Asia/Kolkata')->format('Y-m-d');
        $this->yesterday    = Carbon::today('Asia/Kolkata')->subDay()->format('Y-m-d');
    }

    public function form()
    {

        $data['min'] = $this->yesterday;
        $data['max'] = $this->today;

            $today=Carbon::today();
            $user=Auth()->user();

            $data['leaves']=$user->employee->leaves()->where('status','Approved')->where(function($subQuery)use($today){
               $subQuery->where(function($q1)use($today){
                   $q1->where('from_date','<=',$today)->where('to_date','>=',$today);
               })->orWhere(function($q2)use ($today){
                  $q2->whereBetween('from_date',[$today,$today]);
               }); })->first();

// dd($leaves->leave_type);

        if ($this->now >= $this->end) {
            $data['min'] = $data['max'];
           $report = DailyReport::whereDate('report_date', $data['max'])->where('employee_id', auth()->user()->employee->id)->first();

        }else{
            $report = new DailyReport();
        }

        $data['report']   = $report;

        return view('dailyReport.form', $data);
    }

    public function submit(Request $request)
    {
        $report_date = Carbon::parse($request->report_date)->format('Y-m-d');
        if (($report_date == $this->yesterday &&  $this->now <= $this->end)|| $report_date == $this->today) {
            return $this->saveReport($request);
        }
        $message_date = Carbon::parse($request->report_date)->format('d-m-Y');
        return redirect()->back()->with('failure','You can not submit '.$message_date. ' report.');

    }

    public function saveReport($request)
    {
        $report              = DailyReport::firstOrNew(['report_date' => $request->report_date, 'employee_id' => auth()->user()->employee->id]);

        $report->task1       = $request->task1;
        $report->task2       = $request->task2;
        $report->task3       = $request->task3;
        $report->task4       = $request->task4;
        $report->task5       = $request->task5;
        $report->task6       = $request->task6;
        $report->save();

        return redirect()->back()->with('success','Report Submitted Successfully.');
    }

    public function myList(Request $request)
    {

        $month       = Carbon::now()->month;

        if($request->month=="last_month")
        {
            $month      = Carbon::now()->subMonth();
        }

        $reports         = DailyReport::where('employee_id',auth()->user()->employee->id)
                                ->whereMonth('report_date',$month);
        $data['reports'] = $reports->orderBy('report_date','desc')->paginate(10);
        return view('dailyReport.myList',$data);
    }

    public function departmentReports(Request $request)
    {
        abort_if(auth()->user()->cannot('managerDashboard', new User()) && auth()->user()->cannot('hrDashboard',  new User()),403);

        $departments    = Department::query();

        if(!auth()->user()->hasRole('hr') && auth()->user()->employee->managerDepartments->isNotEmpty())
        {
            $departments    = $departments->where('manager_id',auth()->user()->employee->id);
        }

        $data['departments']    = $departments->pluck('name','id')->toArray();

        if (request()->has('date')) {
            $date = $request->date;
        }else{
            $date            = Carbon::today()->format('Y-m-d');
        }
        $departments_id              = auth()->user()->employee->managerDepartments->pluck('id','id')->toArray();
        // $employees = Employee::with('department');

        $employees              = Employee::query();

        if(request()->has('department_id'))
        {
            $employees    = $employees->whereHas('department',function($query){
                                $query->where('id',request()->department_id);
                            });
        }
        else
        {
            $employees    = $employees->whereHas('department',function($query) use($departments){
                $departmentsIds = $departments->pluck('id','id');
                $query->whereIn('id',$departmentsIds);
            });
        }

        // if(auth()->user()->hasRole('hr'))
        // {
        //     $employees    = $employees->with(['workReports' => function($query) use($date){
        //             $query->whereDate('report_date',$date);
        //         }]);

        // }
        $employees    = $employees->with(['workReports' => function($query) use($date){
            $query->whereDate('report_date',$date);
        }]);

        // $employees    = Employee::whereIn('department_id',$departments_id )->with(['workReports' => function($query) use($date){
        //     $query->whereDate('report_date',$date);
        // }]);

        $employees  = $employees->with(['leaves' => function($query) use($date){
            $query->whereDate('from_date', '<=', $date)
                    ->whereDate('to_date', '>=', $date)->where('status', 'Approved');
        }]);


        $data['today']      =  $date;
        $data['employees']  =  $employees->orderBy('department_id')->paginate(25);

        return view('dailyReport.departmentReports',$data);
    }

    public function export(Request $request)
    {
        ini_set('max_execution_time', -1);

        if (request()->has('date')) {
            $date = Carbon::parse($request->date)->format('d-m-Y');
        }else{
            $date            = Carbon::today()->format('d-m-Y');
        }

        $fileName = "workReport_$date.xlsx";
        return Excel::download(new DailyReportExport($request), $fileName);
    }
}
