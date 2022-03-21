<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use App\User;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Department;
use App\Models\Interviewee;
use App\Models\Leave;
use App\Models\ActivityLog;
use App\Models\Entity;
use App\Models\EquipmentRequests;
use App\Models\Equipment;
use App\Models\Ticket;
class DashboardController extends Controller
{

  public function index()
  {

    $data = [];
    $user                 =   auth()->user();

    if ($user->can('hrDashboard', $user)) {
      $data['hr']         =   $this->hrDashboard();
    }
    if ($user->can('managerDashboard', $user)) {
      $data['manager']    =   $this->managerDashboard();
    }
    if ($user->can('employeeDashboard', $user)) {
      $data['employees']  =   $this->employeeDashboard();
    };
    if ($user->can('itDashboard', $user)) {
      $data['it']         =   $this->itDashboard();
    }
    $data['logs']             = ActivityLog::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->get()->take(20);
    $data['leaveDashboard']   = $this->leaveDashboard();
    $data['myLeaveDashboard'] = $this->myLeaveDashboard();

    $data['recentJoining']    = Employee::where('is_active','1')->whereDate('join_date', '>=',  Carbon::now()->startOfMonth())
                                         ->whereDate('join_date', '<=', Carbon::now()->endOfMonth())->count();

    if(!empty(auth()->user()->employee->birth_date))
    {
      $birthDate = Carbon::parse(auth()->user()->employee->birth_date)->format('M-d');
      $today     = Carbon::now()->format('M-d');
      if($birthDate == $today)
      {
        $data['employeeBirthday'] = auth()->user()->employee;
      }
    }

    $data['start'] = Carbon::now()->startOfMonth()->format('Y-m-d');
    $data['end']   = Carbon::now()->endOfMonth()->format('Y-m-d');


    return view('dashboard', $data);
  }

  public function hrDashboard()
  {
    $employees                       =    Employee::all();
    $active                          =    $employees->where('is_active','1')->count();
    $data['profilesPendingCount']    =    Employee::whereDoesntHave('documents')
                                                    ->orWhereHas('documents',function($query){
                                                        return $query->whereNull(['aadhaar_file','aadhaar_number','cv']);
                                                      })->orWhereDoesntHave('employeeEmergencyContact')->orWhereNull('profile_pic')->count();
    // $managers                        =    User::havingRole(['manager']);
    // $data['managers']                =    Employee::whereIn('user_id', $managers)->get();
    $departments                     =    Department::all();
    $attendance                      =    Attendance::all();
    $data['intervieweeCount']        =    Interviewee::where('status', 'pending')->count();
    $data['present']                 =    $attendance->where('status', 'present')->where('attendance_date', Carbon::today())->count();
    $data['absent']                  =    $attendance->where('status', 'absent')->where('attendance_date', Carbon::today())->count();
    $data['department']              =    $departments->count();
    $data['employeeCount']           =    $employees->count();
    $data['employees']               =    $employees->take(10);
    $data['active']                  =    json_encode($active);
    $data['in_active']               =    Employee::withoutGlobalScopes()->where('is_active', 0)->count();
    $data['admins']                  =    count(User::havingRole('admin'));

    $department_count                =    [];

    foreach($departments as $department)
    {

        $department_count[$department->name]=$department->employees->count();
    }

    $data['department_count']=  $department_count;
    return $data;
  }

  public function managerDashboard()
  {
    $user                           =   auth()->user();
    $department_id                  =   $user->employee->department_id;
    // $department_attendance          =   Attendance::whereHas('employee.department', function ($query) use ($department_id) {
    //                                                   $query->where('id', $department_id);
    //                                                 })->where('attendance_date', Carbon::today())->get();
    $data['employees']              =   Employee::where('department_id', $department_id)
                                                  ->orderBy('created_at', 'desc')
                                                  ->pluck('name', 'id')->toArray();
    $data['employeeCount']          =   count($data['employees']);
    $data['entityRequestCount']     =   EquipmentRequests::where('employee_id', auth()->user()->employee->id)->count();
    return $data;
  }

  public function  employeeDashboard()
  {
    $employee                       =   auth()->user()->employee;
    $data['previous_month']         =   Carbon::now()->subMonth()->format('F');
    $attendance_record              =   Attendance::where("employee_id", $employee->id)->whereYear("attendance_date", Carbon::now()->year)->get();
    // $data['todayAttendance']     =   $attendance_record->where('attendance_date', Carbon::today())->pluck('status')->first();
    $data['present']                =   $employee->total_present;
    $data['absent']                 =   $employee->total_absent;
    $this_year_attendance           =   [];
    for ($i = 1; $i <= Carbon::now()->subMonth()->month; $i++) {
      // db query in loop use collection instead
      $this_year_attendance[$i]['count'] = $attendance_record->where('attendance_month', $i)->count();
      $this_year_attendance[$i]['month'] = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    }
    $data['attendanceYearWise']     =  $this_year_attendance;
    $data['equipment']              =  Equipment::where('employee_id', $employee->id)->count();
    $data['ticketCount']            =  $employee->tickets->whereNotIn('status',['Sorted','Closed'])->count();
    return $data;

  }

  public function itDashboard()
  {
    $data['entityCount']                   =   Entity::count();
    $data['equipmentCount']                =   Equipment::count();
    $users                                 =   User::all();
    $data['emailAssign']                   =   $users->sortByDesc('created_at')->take(5);
    $data['emailUpdate']                   =   $users->sortByDesc('updated_at')->take(5);
    $data['entityRequestCount']            =   EquipmentRequests::where('employee_id', auth()->user()->employee->id)->count();
    $ticketCount                           =   Ticket::whereNotIn('status',['Sorted','Closed']);
    if(!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('guest'))
    {
    $ticketCount                           =   $ticketCount->whereHas('ticketCategory',function($query){
                                                $query->where('type','IT');
                                               });
    }
    $data['ticketCount']                   =  $ticketCount->count();
    return $data;
  }

  public function leaveDashboard()
  {
    $today_date         = Carbon::now()->format('Y-m-d');

    $data['leaves']     = Leave::with('employee.department')->leftjoin("employee","employee.id","=","leave.employee_id")->leftjoin("departments","departments.id","=","employee.department_id")
                                ->orderBy('departments.name')->whereDate('from_date', '<=', $today_date)
                                ->whereDate('to_date', '>=', $today_date)->where('status', 'Approved')->get();
// dd($data);
    return $data;
  }

  public function myLeaveDashboard()
  {
    $leaves   = Leave::with('employee')->where('employee_id',auth()->user()->employee->id)
                      ->whereYear('from_date','>=',Carbon::now()->year)->whereMonth('from_date','>=', Carbon::now()->month)
                      ->where('status', 'Approved')->get();
                    //   dd($leaves);
    $fulldurationCount    = 0;
    $halfdaydurationCount = 0;
    foreach($leaves as $leave)
    {
        if($leave->leave_type != 'Full day')
        {
            $halfdaydurationCount+=$leave->duration;
        }
       else
       {
            $fulldurationCount +=$leave->duration;
       }

    }
    $halfdaydurationCount   = $halfdaydurationCount/2;
    $data['totalleaves']    = $fulldurationCount+$halfdaydurationCount;
    return $data;
  }




}

