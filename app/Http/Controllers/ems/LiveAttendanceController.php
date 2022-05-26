<?php

namespace App\Http\Controllers\ems;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Mail\Action;
use App\Models\Region;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use App\Models\ShiftType;
use App\Models\Attendance;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\LiveAttendance;
use App\Exports\AttendanceExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class LiveAttendanceController extends Controller
{
    private $today;
    private $yesterday;
    private $US_Today;
    public function __construct()
    {

        $this->today            = Carbon::today()->setTimezone('Asia/Kolkata')->format('Y-m-d');
        $this->yesterday        = Carbon::today()->setTimezone('Asia/Kolkata')->subDay()->format('Y-m-d');
        // $indiaToday             = Carbon::now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
        // $this->US_Today         = getFormattedDate($indiaToday, 'Y-m-d', 'America/Vancouver');

    }

    public function storeAttendance()
    {




        //  dd($this->yesterday);
        ini_set('max_execution_time', '-1');

        $liveAttendances = LiveAttendance::whereDate('Logdate', $this->today)
            ->whereIn('Shortname', ['IN-JAL-1',  'IN-JAL-2'])
            ->get();

        $today = $this->today;
        //  dd($liveAttendances);

        foreach ($liveAttendances as $attendanceData) {
            $employee = Employee::with(['user.leaves' => function ($leaves)  use ($today) {

                $leaves->where('is_approved', '1')->where(function ($subQuery)  use ($today) {
                    $subQuery->where(function ($query1) use ($today) {
                        $query1->where('from_Date', '<=', $today)->where('to_Date', '>=', $today);
                    })->orWhere(function ($query2) use ($today) {

                        $query2->whereBetween('from_Date', [$today, $today]);
                    });
                });
            }])->where('biometric_id', $attendanceData->Empcode)->first();

            if (empty($employee) || empty($employee->user)) {
                continue;
            }
            $user = $employee->user;


            $start_time           =   Carbon::parse($user->shiftType->start_time);
            $out_time               =   Carbon::parse($user->shiftType->end_time);

            if ($employee->user->leaves->isNotEmpty()) {

                $leave =  $employee->user->leaves->first();
                if ($leave->leave_session == "First half") {

                    $start_time    = Carbon::parse($user->shiftType->mid_time);
                }
                if ($leave->leave_session == "Second half") {

                    $out_time    = Carbon::parse($user->shiftType->mid_time);
                }
            }

            $inData =   null;


            if (!empty($user->attendances) && $user->attendances->isNotEmpty()) {

                $attendances    = $user->attendances->where('punch_date', '=', $this->today);
                $inData         = $attendances->first();
            }



            if (empty($inData)) {
                $object                   =   new Attendance();
                $object->user_id          =   $user->id;
                $object->shift_type_id    =   $employee->shift_type_id;
                $object->punch_in         =   $attendanceData->Logtime;
                $object->punch_date       =   $attendanceData->Logdate;
                // $start_time           =   Carbon::parse($user->shiftType->start_time);
                $inTime               =   Carbon::parse($attendanceData->Logtime);

                $diff                 =   $inTime->diffInMinutes($start_time);
                if ($inTime > $start_time) {

                    $object->in           =   -$diff;
                } else {
                    $object->in           =   $diff;
                }
            } else {
                $object                 = Attendance::firstOrNew(['punch_date' => $inData->punch_date, 'user_id' => $user->id]);

                $punch_out_time = strtotime($attendanceData->Logtime);
                $punch_in       = strtotime($object->punch_in);
                $diff          =  $punch_out_time - $punch_in;
                $tenMinuteDiff = 600; //  600 seconds (60*10)

                //  dd($punch_in,$punch_out_time,$diff,$tenMinuteDiff);
                if ($diff > $tenMinuteDiff) {

                    $object->punch_out      =   $attendanceData->Logtime;
                    $punchOut               =   Carbon::parse($attendanceData->Logtime);
                    $diff                   =   $punchOut->diffInMinutes($out_time);
                    if ($punchOut < $out_time) {

                        $object->out           =   -$diff;
                    } else {
                        $object->out          =   $diff;
                    }
                    // $object->out            =    $diff;

                }

                if($object->entry_type != "Manual")
                {
                    $object->shift_type_id  =   $employee->shift_type_id;
                    $object->entry_type     = 'Punch';
                }
            }
            $count = $user->attendances->count();

            $firstAttendance = $user->attendances->first();
            
            if ($count == 1  &&  $object->punch_date == $firstAttendance->punch_date) 
            {

                $object               =  $user->attendances->first();
                $object->punch_in     =   "09:00:00";
            }


            $object->remarks        = 'Punched';
            $object->added_by       = 0;
            // $object->shift_type_id  =   $employee->shift_type_id;
            $object->save();
        }


        return "done";
    }


    public function attendanceDashboard(Request $request, $export = false)
    {

        ini_set('max_execution_time', -1);
        $this->authorize('dashboard', new Attendance());
        $today                          =   Carbon::today()->format('Y-m-d');


        if (!empty(request()->get('attendanceDateFrom')) && !empty(request()->get('attendanceDateTo'))) {
            $this->attendanceDateFrom   = request()->attendanceDateFrom;
            $this->attendanceDateTo     = request()->attendanceDateTo;
            $thisMonth                  =   Carbon::createFromFormat('Y-m-d', request()->attendanceDateFrom);
            $currentMonth               =   Carbon::createFromFormat('Y-m-d', request()->attendanceDateFrom)->format('d-m-Y');
            $data['dateStart']          =   Carbon::createFromFormat('Y-m-d', request()->attendanceDateFrom)->format('d M y');
            $data['dateEnd']            =   Carbon::createFromFormat('Y-m-d', request()->attendanceDateTo)->format('d M y');
            $date                       =   $this->attendanceDateFrom;
        } else {
            $this->attendanceDateFrom   = Carbon::now()->startOfMonth()->format('Y-m-d');
            $this->attendanceDateTo     = Carbon::now()->format('Y-m-d');
            $thisMonth                  = Carbon::today()->startOfMonth();
            $data['dateStart']          = Carbon::now()->startOfMonth()->format('d M y');
            $data['dateEnd']            = Carbon::today()->format('d M y');
            $date                       = now()->format('Y-m-d');
        }
        $yesterday      =       Carbon::createFromFormat('Y-m-d', $date)->subDay()->format('Y-m-d');
        if ($request->has('is_late_today')) {
            $this->attendanceDateFrom   = Carbon::now()->format('Y-m-d');
            $this->attendanceDateTo     = Carbon::now()->format('Y-m-d');
            $thisMonth                  = Carbon::today();
            $data['dateStart']          = Carbon::now()->format('d M y');
            $data['dateEnd']            = Carbon::today()->format('d M y');
        }

        if (request()->has('today_punched_in') || request()->has('today_punched_not_in') || request()->has('on_full_day') || request()->has('on_half_day')) {
            if (!empty(request()->get('attendanceDateFrom')) && !empty(request()->get('attendanceDateTo'))) {
                $this->attendanceDateFrom   = request()->attendanceDateFrom;
                $this->attendanceDateTo     = request()->attendanceDateTo;
                $thisMonth                  = Carbon::today();
                $data['dateStart']          =   Carbon::createFromFormat('Y-m-d', request()->attendanceDateFrom)->format('d M y');
                $data['dateEnd']            =   Carbon::createFromFormat('Y-m-d', request()->attendanceDateTo)->format('d M y');
            } else {
                $this->attendanceDateFrom   = Carbon::now()->format('Y-m-d');
                $this->attendanceDateTo     = Carbon::now()->format('Y-m-d');
                $thisMonth                  = Carbon::today();
                $data['dateStart']          = Carbon::now()->format('d M y');
                $data['dateEnd']            = Carbon::today()->format('d M y');
            }
        }

        $users      =       $this->attendanceFilter($thisMonth, $this->attendanceDateFrom, $this->attendanceDateTo);




        $data       =       $this->dashboardCounts($this->attendanceDateFrom, $this->attendanceDateTo, $data);

        $dateArray = [];
        if (request()->has('yesterday_not_punch_out')) {
            $data['dateStart']          = Carbon::createFromFormat('Y-m-d', $yesterday)->format('d M y');
            $data['dateEnd']            = Carbon::createFromFormat('Y-m-d', $yesterday)->format('d M y');
            $users->where('is_active', 1)->whereHas('attendances', function ($query) use ($yesterday) {
                $query->where('punch_date', $yesterday)->whereNull('punch_out');
            });
        }
        $period = CarbonPeriod::create($data['dateStart'], $data['dateEnd']);

        foreach ($period as $date) {

            $dateArray[] = $date->format('Y-m-d');
        }

        if (request()->has('today_punched_not_in')) {
            $users->whereHas('shiftType', function ($query) {


                $query->where('name', 'Morning');
            })->where('is_active', 1)->whereDoesNtHave('attendances', function ($query) use ($date) {
                $query->where('punch_date', $date);
            })->whereDoesNtHave('leaves', function ($query) {

                $query->where('from_date', '<=', $this->attendanceDateFrom)->where('to_date', '>=', $this->attendanceDateTo)->where('is_approved', '1')->where('leave_session', 'Full day');
            });
           
        }

        if (request()->has('today_punched_in')) {
            $users->whereHas('attendances', function ($query) use ($date) {
                $query->where('punch_date', $date);
            });
        }




        $users  =   $users->get();
        $userArray = [];
        // if ($request->ajax()) {

        foreach ($users as  $user) {

            $shiftStartTime   =   !empty($user->shiftType) ? $user->shiftType->start_time : '09:00:00';
            $shiftStartTime   = strtotime($shiftStartTime);
            $userArray[$user->email]['biometric_id'] = $user->employee->biometric_id ?? 'N/A';
            $userArray[$user->email]['name'] = $user->name ?? 'N/A';
            foreach ($dateArray as $date) {

                $attendance            = $user->attendances->where('punch_date', $date)->first();
                $employeeLeave         = $user->leaves->where('is_approved','1')->where('user_id', $user->id)->where('from_date', '<=', $date)->where('to_date', '>=', $date)->first();

                //  dd($attendance,$employeeLeave);
                $userArray[$user->email][$date]['punch_in'] = '';

                if (!empty($attendance) && !empty($attendance->punch_in)) {


                    $userArray[$user->email][$date]['punch_in'] = $attendance->punch_in;
                    $userArray[$user->email][$date]['punch_out'] = $attendance->punch_out;
                }
                $userArray[$user->email][$date]['session'] =   '';
                if (!empty($employeeLeave)) {

                    $userArray[$user->email][$date]['session'] = $employeeLeave->leave_session;
                }
                if ($request->has('is_late_today')) {



                    if (!empty($employeeLeave)) {

                        $userArray[$user->email][$date]['punch_in'] = "";
                        $userArray[$user->email][$date]['session'] = "";
                    }

                    if (!empty($attendance) && $attendance->punch_in) {

                        $punch_in = strtotime($attendance->punch_in);

                        if ($punch_in < $shiftStartTime) {

                            $userArray[$user->email][$date]['punch_in'] = "";
                            $userArray[$user->email][$date]['session'] = "";
                        }
                    }
                }
            }
        }
        $data['dateArray'] = $dateArray;
        $data['userArray'] = $userArray;
        // }



        $data['departments'] = Department::pluck('name', 'id');
        $data['shiftTypes'] = ShiftType::all();
        $data['userTypes'] = Config::get('employee.userTypes');


        if ($export == 'true') {
            return $data;
        }

        return view('attendance.dashboard', $data);
    }

    public function lateAttendanceDashboard(Request $request)
    {
        $this->authorize('dashboard', new Attendance());
        if (!empty($request->dateFrom) && !empty($request->dateTo)) {
            $dateFrom       =   $request->dateFrom;
            $dateTo         =   $request->dateTo;
        } else {
            $dateFrom       =   now()->format('Y-m-d');
            $dateTo         =   $dateFrom;
        }

        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('HR')) {

            $userAttendances    =   Attendance::whereNotNull('punch_in')->with(['user.shiftType', 'user.employee.department'])
                ->whereHas('user.employee', function ($employee) {
                    $employee->where('department_id', auth()->user()->employee->department_id);
                })->whereBetween('punch_date', [$dateFrom, $dateTo])->whereHas('user', function ($user) {
                    $user->whereHas('shiftType')->where('user_type', 'Employee');
                })->where('in', '<', '0')->get()->groupBy('user.email');
        } else {
            $userAttendances    =   Attendance::whereNotNull('punch_in')->with(['user.shiftType', 'user.employee.department'])
                ->whereBetween('punch_date', [$dateFrom, $dateTo])->whereHas('user', function ($user) {
                    $user->whereHas('shiftType')->where('user_type', 'Employee');
                })->where('in', '<', '0')->get()->groupBy('user.email');
        }
        $data['userAttendances']        =   $userAttendances;
        return view('attendance.lateDashboard', $data);
    }

    public function attendanceExport(Request $request)
    {
        $data        =  $this->attendanceDashboard($request, true);
        $fileName    = "attendance.xlsx";

        return Excel::download(new AttendanceExport($data), $fileName);
    }
    public function myAttendance(Request $request)
    {
        $id                     = auth()->user()->id;
        $month                  = Carbon::now()->month;
        $data['startTime']      = auth()->user()->shiftType->start_time;
        $myAttendances          = Attendance::where('user_id', $id);

        if (!empty(request()->get("dateFrom")) && !empty(request()->get("dateTo"))) {
            $myAttendances        = $myAttendances->whereBetween("punch_date", [$request->dateFrom, $request->dateTo]);
        } else {
            $myAttendances        = $myAttendances->whereMonth('punch_date', $month);
        }
        $data['myAttendances']  = $myAttendances->get();

        return view('attendance.myAttendance', $data);
    }

    private function attendanceFilter($thisMonth, $fromDate, $toDate)
    {



        if (request()->has('user_type')) {
            $users              =   User::withoutGlobalScopes(['user_type'])->select('shift_type_id', 'name', 'id', 'user_type', 'email')->has('employee')->with(['employee:department_id,id,biometric_id,user_id', 'attendances' => function ($attendance) use ($thisMonth) {
                $attendance->whereMonth('punch_date', $thisMonth);
            }]);
            $users   = $users->where('user_type', request()->user_type);
        } else {
            $users              =   User::where('user_type', 'Employee')->has('employee')->select('shift_type_id', 'name', 'id', 'user_type', 'email')->with(['employee:department_id,id,biometric_id,user_id', 'attendances' => function ($attendance) use ($thisMonth) {
                $attendance->whereMonth('punch_date', $thisMonth);
            }]);
        }

        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('HR')) {

            $users          = $users->whereHas('employee', function ($employee) {


                $employee->where('department_id', auth()->user()->employee->department_id);
            });
        }

        if (!empty(request()->user_id)) {
            $users  =   $users->where('id', request()->user_id);
        }

        if (request()->has('department_id')) {
            $users  =   $users->whereHas('employee', function ($employee) {

                $employee->where('department_id', request()->department_id);
            });
        }

        if (request()->has('shift_id')) {
            $users  =   $users->where('shift_type_id', request()->shift_id);
        }
        if (empty(request()->attendanceDateFrom) && empty(request()->attendanceDateTo)) {
            $fromDate   =   now()->format('Y-m-d');
            $toDate     =   $fromDate;
        }
        if (request()->has('on_full_day')) {
            $users->whereHas('leaves', function ($leaves) use ($fromDate, $toDate) {
                $leaves->where('is_approved', 1)
                    ->where('from_date', '<=', $fromDate)
                    ->where('to_date', '>=', $toDate)->where('leave_session', 'Full day');
            });
        }

        if (request()->has('on_half_day')) {
            $users->whereHas('leaves', function ($leaves) use ($fromDate, $toDate) {
                $leaves->where('is_approved', 1)->where(function ($subQuery) use ($fromDate, $toDate) {

                    $subQuery->where(function ($query1) use ($fromDate, $toDate) {

                        $query1->where('from_Date', '<=', $fromDate)->where('to_Date', '>=', $fromDate);
                    })->orWhere(function ($query2) use ($fromDate, $toDate) {

                        $query2->whereBetween('from_Date', [$fromDate, $toDate]);
                    });
                })->where('leave_session', '<>', 'Full day');
            });
        }

        return $users;
    }

    private function dashboardCounts($fromDate, $toDate, $data)
    {

        if (empty(request()->attendanceDateFrom) && empty(request()->attendanceDateTo)) {
            $fromDate   =   now()->format('Y-m-d');
            $toDate     =   $fromDate;
        }
        $yesterday      =   Carbon::createFromFormat('Y-m-d', $fromDate)->subDay()->format('Y-m-d');

        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('HR')) {
            $data['totalUsers']              = User::whereIn('user_type', ['Employee'])->whereHas('employee', function ($employee) {
                $employee->where('onboard_status', 'Onboard')->where('department_id', auth()->user()->employee->department_id);
            })->where('is_active', 1)->count();
            $data['totalActiveToday']        = User::whereIn('user_type', ['Employee'])->whereHas('employee', function ($employee) {
                $employee->where('onboard_status', 'Onboard')->where('department_id', auth()->user()->employee->department_id);
            })->where('is_active', 1)->whereDoesNtHave('leaves', function ($leaves) use ($fromDate, $toDate) {
                $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)->where('is_approved', '1')->where('leave_session', 'Full day');
            })->count();
            $data['totalOnFullDayLeaveToday']        = User::whereIn('user_type', ['Employee'])->whereHas('employee', function ($employee) {
                $employee->where('onboard_status', 'Onboard')->where('department_id', auth()->user()->employee->department_id);
            })->where('is_active', 1)->whereHas('leaves', function ($leaves) use ($fromDate, $toDate) {
                $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)->where('is_approved', '1')->where('leave_session', 'Full day');
            })->count();
            $data['totalOnHalfDayLeaveToday']        = User::whereIn('user_type', ['Employee'])->where('is_active', 1)->whereHas('employee', function ($employee) {
                $employee->where('onboard_status', 'Onboard')->where('department_id', auth()->user()->employee->department_id);
            })->whereHas('leaves', function ($leaves) use ($fromDate, $toDate) {
                $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)->where('is_approved', '1')->where('leave_session', '<>', 'Full day');
            })->count();
            $data['todayPunchIn']            =   Attendance::whereHas('user.employee', function ($employee) {

                $employee->where('onboard_status', 'Onboard')->where('department_id', auth()->user()->employee->department_id);
            })->whereDate('punch_date', $fromDate)->count();
            $data['yesterdayNotPunchOut']         =   User::where('user_type', '<>', 'external')->where('is_active', 1)->whereHas('employee', function ($employee) {
                $employee->where('onboard_status', 'Onboard')->where('department_id', auth()->user()->employee->department_id);
            })->whereHas('attendances', function ($attendances) use ($yesterday) {
                $attendances->where('punch_date', $yesterday)->whereNull('punch_out');
            })->count();


            $data['todayNotPunchIn']         =   User::where('user_type', '<>', 'external')->where('is_active', 1)->whereHas('employee', function ($employee) {
                $employee->where('onboard_status', 'Onboard')->where('department_id', auth()->user()->employee->department_id);
            })->whereDoesNtHave('leaves', function ($leaves) use ($fromDate, $toDate) {
                $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)->where('is_approved', '1')->where('leave_session', 'Full day');
            })->whereDoesNtHave('attendances', function ($attendances) use ($fromDate) {
                $attendances->where('punch_date', $fromDate);
            })->count();


            $data['users']          = User::whereHas('employee', function ($employee) {


                $employee->where('department_id', auth()->user()->employee->department_id);
            })->pluck('name', 'id');
        } else {
            $data['totalUsers']              = User::whereIn('user_type', ['Employee'])->where('is_active', 1)->count();
            $data['totalActiveToday']        = User::whereIn('user_type', ['Employee'])->where('is_active', 1)->whereDoesNtHave('leaves', function ($leaves) use ($fromDate, $toDate) {
                $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)->where('is_approved', '1')->where('leave_session', 'Full day');
            })->count();
            $data['totalOnFullDayLeaveToday']        = User::whereIn('user_type', ['Employee'])->where('is_active', 1)->whereHas('leaves', function ($leaves) use ($fromDate, $toDate) {
                $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)->where('is_approved', '1')->where('leave_session', 'Full day');
            })->count();
            $data['totalOnHalfDayLeaveToday']        = User::whereIn('user_type', ['Employee'])->where('is_active', 1)->whereHas('leaves', function ($leaves) use ($fromDate, $toDate) {
                $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)->where('is_approved', '1')->where('leave_session', '<>', 'Full day');
            })->count();
            $data['todayPunchIn']            =   Attendance::whereHas('user', function ($query) {
                $query->where('user_type', 'Employee');
            })->whereDate('punch_date', $fromDate)->count();
            $data['todayNotPunchIn']         =   User::whereIn('user_type', ['Employee'])->whereHas('shiftType', function ($query) {
                $query->where('name', 'Morning');
            })->whereDoesNtHave('leaves', function ($leaves) use ($fromDate, $toDate) {
                $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)->where('is_approved', '1')->where('leave_session', 'Full day');
            })->whereDoesNtHave('attendances', function ($attendances) use ($fromDate) {
                $attendances->where('punch_date', $fromDate);
            })->count();
            $data['yesterdayNotPunchOut']         =   User::whereIn('user_type', ['Employee'])->whereHas('attendances', function ($attendances) use ($yesterday) {
                $attendances->where('punch_date', $yesterday)->whereNull('punch_out');
            })->count();
            $data['users'] = User::with('leaves')->whereHas('employee')->pluck('name', 'id');
        }

        return $data;
    }

    public function removeAttendance()
    {

        $attendances =  Attendance::whereNotNull('punch_in')->whereNotNull('punch_out')->where('punch_date', now()->subDay()->format('Y-m-d'))->get();
        foreach ($attendances as $attendance) {
            $time1 = strtotime($attendance->punch_in);
            $time2 = strtotime($attendance->punch_out);
            $difference = round(abs($time2 - $time1) / 3600, 2);
            if ($difference < 3) {
                $attendance->delete();
            }
        }
        return "done";
    }
}
