<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Entity;
use App\Models\Ticket;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\ActivityLog;
use App\Models\Interviewee;
use App\Models\EquipmentRequests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

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

    $today                      = Carbon::now()->format('Y-m-d');

    $data['todayAttendance']    = Attendance::where('user_id', auth()->user()->id)->where('punch_date',$today)->first();

    $tickets                    = Ticket::with('user','ticketCategory')->whereNotIn('status',['Sorted','Closed'])->
                                whereHas('ticketCategory',function($ticketCategory)
                                {
                                    $ticketCategory->where('type','IT');
                                })->
                                  whereHas('user.employee',function($departments)
                                  {
                                      $departments->where('department_id',auth()->user()->employee->department_id);
                                  });
                      
                                  
    $data['departmentTicketCount']  = $tickets->count();
    return view('dashboard', $data);
  }

  public function hrDashboard()
  {
    $employees                       =    Employee::withoutGlobalScopes(['guest'])->whereHas('user',function ($user){
        $user->where('user_type','Employee');
    })->where('onboard_status','Onboard')->get();
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
    $data['employees']              =   Employee::where('onboard_status','Onboard')->where('department_id', $department_id)
                                                  ->orderBy('created_at', 'desc')
                                                  ->pluck('name', 'id')->toArray();
    $data['employeeCount']          =   count($data['employees']);
    $data['entityRequestCount']     =   EquipmentRequests::where('employee_id', auth()->user()->employee->id)->count();
    return $data;
  }

  public function  employeeDashboard()
  {
    $user                       =   auth()->user();


    // $data['previous_month']         =   Carbon::now()->subMonth()->format('F');
    // // $attendance_record              =   Attendance::where("employee_id", $employee->id)->whereYear("attendance_date", Carbon::now()->year)->get();
    // // // $data['todayAttendance']     =   $attendance_record->where('attendance_date', Carbon::today())->pluck('status')->first();
    // // $data['present']                =   $employee->total_present;
    // // $data['absent']                 =   $employee->total_absent;
    // $this_year_attendance           =   [];
    // for ($i = 1; $i <= Carbon::now()->subMonth()->month; $i++) {
    //     // db query in loop use collection instead
    //     $this_year_attendance[$i]['count'] = $attendance_record->where('attendance_month', $i)->count();
    //     $this_year_attendance[$i]['month'] = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    // }
    $data['attendanceYearWise']     =  [];
    // $data['equipment']              =  Equipment::where('employee_id', $employee->id)->count();
    $data['ticketCount']            =  $user->tickets->whereNotIn('status', ['Sorted', 'Closed'])->count();
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
    $ticketCount                           =   Ticket::whereNotIn('status',['Sorted','Closed'])->whereHas('user',function($query){
        $query->where('is_active','1');
    });
    if(!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('guest') && !auth()->user()->hasRole('HR'))
    {
    $ticketCount                           =   $ticketCount->whereHas('ticketCategory',function($query){
                                                $query->where('type','IT');
                                               });
    }
    else
    {
        $ticketCount                           =   $ticketCount->whereHas('ticketCategory',function($query){
            $query->where('type','HR');
           });
    }
    $data['ticketCount']                   =  $ticketCount->count();
    return $data;
  }

  public function leaveDashboard()
  {
    $today_date         = Carbon::now()->format('Y-m-d');

    $data['leaves']     = Leave::with('user.employee.department')->leftjoin("users","users.id","=","leaves.user_id")->leftjoin("employee","employee.user_id","=","users.id")->leftjoin("departments","departments.id","=","employee.department_id")
                                ->orderBy('departments.name')->whereDate('from_date', '<=', $today_date)
                                ->whereDate('to_date', '>=', $today_date)->where('status','<>','Cancelled')->where('is_approved', '1')->get();
// dd($data);
    return $data;
  }

  public function myLeaveDashboard()
  {

     if(empty(auth()->user()))
     {

        return [];
     }
    $leaves   = Leave::with('user.employee')->where('user_id',auth()->user()->id)
                      ->whereYear('from_date',Carbon::now()->year)->whereMonth('from_date', Carbon::now()->month)
                      ->where('is_approved', '1')->get();
                    //   dd($leaves);
    $fulldurationCount    = 0;
    $halfdaydurationCount = 0;
    foreach($leaves as $leave)
    {
        if($leave->leave_session != 'Full day')
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
