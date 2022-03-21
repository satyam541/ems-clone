<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RemoteAttendance;
use App\Imports\AttendanceImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RemoteAttendanceExport;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->end          = Carbon::createFromTimeString('08:31','Asia/Kolkata')->format('Y-m-d H:i:s');
        $this->now          = Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s');
        $this->today        = Carbon::today('Asia/Kolkata')->format('Y-m-d');
        $this->yesterday    = Carbon::today('Asia/Kolkata')->subDay()->format('Y-m-d');
    }

    public function list(Request $request)
    {    
        $this->authorize('view',new RemoteAttendance());
        $departments            = Department::query();
        $data['department']     = $departments->pluck('name','id')->toArray();
        $employeeNames          = Employee::withoutGlobalScope('is_active');
      
        if(!auth()->user()->hasRole('hr') && auth()->user()->employee->managerDepartments->isNotEmpty())
        {
            $departments    =   $departments->where('manager_id',auth()->user()->employee->id);
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

            $employeeNames  = $employeeNames->where('department_id',request()->department_id);
        }          
        
        $data['today']          =   $date;
        $data['attendances']    =   $attendances->orderBy('created_at','desc')->paginate(10);
        $data['employeeNames']  =   $employeeNames->pluck('name','id')->toArray();

        return view('attendance.remoteAttendanceList',$data);
    }

    public function form()
    {
        $this->authorize('submit',new RemoteAttendance());
        $min = $this->yesterday;
        $max = $this->today;
        
        if ($this->now >= $this->end) {
            $min = $max;

        }
        else{
            $max = $min;
        }
        
        $employee           =   Employee::with(['department','attendances'=>
                                    function($query)use($min){
                                        $query->whereDate('date',$min)->first();
                                    },
                                    'leaves'=>function($query) use($min){
                                        $query->whereDate('from_date', '<=', $min)
                                        ->whereDate('to_date', '>=', $min)->where('status', 'Approved');
                                    }])
                                    ->find(auth()->user()->employee->id);

        $data['leave']      = $employee->leaves->first();
        
        $data['action']     = empty($employee->attendances->first()) ? 'Punch In':'Punch Out';
        $data['id']         = $employee->attendances->first()->id ?? '';
        $data['employee']   = $employee;
        $data['min']        = $min;
        $data['max']        = $max;

        return view('attendance.attendanceForm',$data);
    }
 
    public function submitAttendance(Request $request)
    {
        if (empty($request->location) || !Str::contains($request->location, ['goo.gl/maps/', 'google.com/maps/'])) {
            return back()->with('failure','Location Invalid.');
        }

        $min = $this->yesterday;
        $max = $this->today;
        
        if ($this->now >= $this->end) {
            $min = $max;

        }
        else{
            $max = $min;
        }

        if(empty($request->id))
        {
            $attendance                 = new RemoteAttendance();
            $attendance->punch_in       = now()->format('h:i:A');
            $attendance->date           = $min;
            $attendance->location_in    = $request->location;
        }
        else{
            $attendance                 = RemoteAttendance::find($request->id);
            $attendance->punch_out      = now()->format('h:i:A');
            $attendance->location_out   = $request->location;
        }
        $attendance->employee_id    = auth()->user()->employee->id;
        $attendance->save();

        return back()->with('success','Attendance Submitted');
    }

    public function export(Request $request)
    {
        ini_set('max_execution_time', -1);
        return Excel::download(new RemoteAttendanceExport($request), 'RemoteAttendance.xlsx');
    }

    // public function index()
    // {
    //     $this->authorize('import',new Attendance());
    //     return view('attendance.attendance');
    // }

    // public function import()
    // {
    //     $this->authorize('import',new Attendance());
    //     Excel::import(new AttendanceImport,request()->file('file'));
    //     $user_ids     =    User::havingRole('employee'); 
    //     $message      =    "Attendance is uploaded";
    //     $link         =    ['name'=>'employeeAttendance','parameter'=>auth()->user()->employee->id];
    //     send_notification($user_ids,$message,$link);
    //     return back()->with('success','Attendance Uploaded  Successfully');
    // }

    // public function view()
    // {
    //     $this->authorize('hrView',new Attendance());
    //     $data['months']      =   [1=>'January','Febuary','March','April','May','June','July','August','September','October','November','December'];
    //     $employees           =   Employee::all()->pluck('name','id')->toArray();
    //     $departments         =   Department::all()->pluck('name','id')->toArray(); 
    //     $employees           =   ['0'=>'All']+$employees; 
    //     $departments         =   ['0'=>'All']+$departments; 
    //     $data['employees']   =   json_encode($employees,JSON_HEX_APOS);
    //     $data['departments'] =   json_encode($departments,JSON_HEX_APOS);
       
    //     return view('attendance.attendanceList',$data);
    // }

    // public function list(Request $request)
    // {
    //      $pageIndex         =  $request->pageIndex;
    //      $pageSize          =  $request->pageSize;
    //      $employees         =  Employee::with('department','attendances');
       
   
    //      if($request->id != '0')
    //      {   
    //         $employees   = $employees->where('id',$request->id);
          
    //     }
    //     if($request->department_id != '0')
    //     {
            
    //         $employees  = $employees->where('department_id',$request->department_id);
    //     }
     
    //     $data['itemsCount'] = $employees->count();
    //     $employees          = $employees->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get();
    //      if(!empty($request->month))
    //      {
    //             $month=$request->month;
    //             $employees=$employees->map(function ($employee) use($month)  {
                 
    //                 $data['id']                 = $employee->id;
    //                 $data['department_id']      = $employee->department_id;
    //                 $data['total_absent']       = $employee->attendances->where('attendance_date',Carbon::now()->year)->where('attendance_month',$month)->where('status','absent')->count();
    //                 $data['total_present']      = $employee->attendances->where('attendance_date',Carbon::now()->year)->where('attendance_month',$month)->where('status','present')->count();
    //                 return $data;
    //             });
    //     }       
    //     $data['data']  = $employees;
    //     return json_encode($data);
    // }

    // public function monthlyRecord(Request $request)
    // {
           
    //   $month                = $request->month;
    //   $employeeId           = $request->employee;
    //   $data['employee']     = Employee::find($employeeId);
    //   $data['monthNum']     = $month;
    //   $data['month']        = date('F', mktime(0, 0, 0, $month, 10));

    //   return view('attendance.monthlyrecord',$data);
    // }

    // public function monthlyDetail(Request $request)
    // {
    //     $pageIndex   =  $request->pageIndex;
    //     $pageSize    =  $request->pageSize;
    //     $month       =  $request->month;
    //     $employeeId  =  $request->employee;
    //     $employee    =  Employee::find($employeeId);
    //     $attendances =  $employee->attendances()->whereYear('attendance_date',Carbon::now()->year)->whereMonth('attendance_date',$month)->orderBy('attendance_date','asc');
    //     $data['itemsCount'] = $attendances->count();
    //     $data['data']       = $attendances->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get();
    //     return json_encode($data);

    // }

    // public function update(Attendance $attendance,Request $request)
    // {
    //     $this->authorize('hrView',new Attendance());
    //     $attendance           = Attendance::find($request->id);
    //     $attendance->comment = $request->comment;
    //     $attendance->save();
    //     return json_encode($attendance);
    // }
}
