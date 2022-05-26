<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
// use Picqer\Barcode;
use App\Models\Leave;
// use App\Models\Document;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\DailyReport;
// use App\Models\AssetDetails;
// use App\Models\AssetSubType;
use Illuminate\Support\Str;
// use Illuminate\Http\Request;
// use App\Http\Classes\SystemInfo;
use App\Models\LeaveBalance;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\File;
// use Picqer\Barcode\BarcodeGeneratorPNG;
// use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TestController extends Controller
{


    public function assetPolicyPending()
    {
        $users  =   User::with('employee.department')->where('user_type','Employee')->whereHas('employee',function($employee){
            $employee->whereDoesNtHave('draftProfiles',function($query){
                $query->where('field_name','asset_policy');
            })->orWhereHas('draftProfiles',function($query){
                $query->where('field_name','asset_policy')->where('is_approved',0);
            });
        })->get();
    }

    public function index()
    {


        // $this->test();
        // Example
        // $system         =   new SystemInfo();
        // $disks          =   $system->getDiskSize(PHP_OS == 'WINNT' ? 'WINNT' : '/');
        // $totalSpace     =   [];
        // $totalFreeSpace =   [];
        // foreach ($disks as $index=>$disk)
        // {
        //     if($disk=="")
        //     {
        //         continue;
        //     }
        //     $disk   =   explode('=', $disk);
        //     if($disk[0]=='Size')
        //     {

        //     $totalSpace[]=$disk[1];
        //     }
        //     if($disk[0]=='FreeSpace')
        //     {
        //     $totalFreeSpace[]=$disk[1];
        //     }
        // }
        // $totalSpace     =   array_sum($totalSpace);
        // $totalSpace     =   number_format($totalSpace / 1073741824, 1);
        // $totalFreeSpace =   array_sum($totalFreeSpace);
        // $totalFreeSpace =   number_format($totalFreeSpace / 1073741824, 1);
        // $totalRam       =   round($system->getRamTotal() / 1024 / 1024 / 1024);
        // $ramFree        =   round($system->getRamFree() / 1024 / 1024 / 1024);
        // $systemInfo     =   $system->getCpuLoadPercentage();
        // dd($systemInfo,'dsds');
        // $assetSubTypeId =   AssetSubType::firstWhere('name','Laptop')->id;
        // $user           =   auth()->user();
        // $asset          =   $user->assetAssignments->where('sub_type_id',$assetSubTypeId);
        // if($asset->isNotEmpty())
        // {
        //     $asset              =   $asset->first();
        //     $assetDetails       =   AssetDetails::updateOrCreate(['asset_id'=>$asset->id]);
        //     $assetDetails->ram  =   $totalRam;
        //     $assetDetails->rom  =   $totalSpace;
        //     $assetDetails->save();
        // }


    }

    public function test2()
    {
        ini_set('max_execution_time', '-1');
        $users              =   User::whereHas('employee', function ($query) {
            $query->whereNotNull('contract_date');
        })->with('employee')->get();
        $currentMonth       =   Carbon::now()->startOfMonth();
        $lastMonth          =   Carbon::now()->startOfMonth()->subMonth();
        foreach ($users as $user) {
            $joinMonth      =   Carbon::createFromFormat('Y-m-d', $user->employee->contract_date);
            if ($joinMonth->format('d') > 14) {
                $diff   =   $joinMonth->diffInDays($currentMonth);
                if ($diff < 20) {
                    continue;
                }
            }
            $leaveBalance               =   LeaveBalance::where('user_id', $user->id)->whereMonth('month', $currentMonth)->first();
            $lastMonthLeaveBalance      =   LeaveBalance::where('user_id', $user->id)->whereMonth('month', $lastMonth)->first();
            if (empty($leaveBalance)) {
                $leaveBalance               =   new LeaveBalance();
                $leaveBalance->balance      =   1.25;
                $leaveBalance->month        =   $currentMonth->format('Y-m-d');
                $leaveBalance->user_id      =   $user->id;
                if (!empty($lastMonthLeaveBalance) && $lastMonthLeaveBalance->is_forwarded != 1) {
                    $leaveBalance->balance  = $lastMonthLeaveBalance->balance + $leaveBalance->balance;
                    $lastMonthLeaveBalance->is_forwarded    =   1;
                    $lastMonthLeaveBalance->save();
                }
            } else {
                $lastMonthBalance                       =   0;
                if (!empty($lastMonthLeaveBalance) && $lastMonthLeaveBalance->is_forwarded != 1) {
                    $lastMonthBalance   =   $lastMonthLeaveBalance->balance;
                    $lastMonthLeaveBalance->is_forwarded    =   1;
                    $lastMonthLeaveBalance->save();
                }
                $deductibleBalance  =   $leaveBalance->balance + $lastMonthBalance + 1.25;
                $leftBalance        =   0;
                $whole              = intval($deductibleBalance);
                $decimal1           = $deductibleBalance - $whole;
                $decimal2           = round($decimal1, 2);
                $decimal            = substr($decimal2, 2);
                if ($decimal == '25' || $decimal == '75') {
                    $deductibleBalance    =   $deductibleBalance - 0.25;
                    $leftBalance          =   0.25;
                }


                $balance            =   $leaveBalance->deduction - $deductibleBalance;
                if ($balance <= 0) {
                    $leaveBalance->balance      =    $leftBalance + abs($balance);
                    $leaveBalance->deduction    =   0;
                } else {
                    $leaveBalance->balance      =   $leftBalance;
                    $leaveBalance->deduction    =   $balance;
                }
            }
            $leaveBalance->save();
        }
        dd('dsds');
    }

    public function syncLeaves()
    {
        ini_set('max_execution_time', '-1');
        $leaves     =       Leave::with('employee')->get();
        foreach ($leaves as $leave) {

            $leave_type_id  =   LeaveType::where('name', $leave->leave_nature)->first()->id;
            if (Str::contains($leave->leave_type, 'Full')) {
                $leaveSession   =   'Full day';
            } else {
                $leaveSession   =   Str::before(Str::after($leave->leave_type, '('), ')');
            }

            $leave->leave_type_id   =   $leave_type_id;
            $leave->leave_session   =   $leaveSession;
            $leave->user_id         =   $leave->employee->user_id ?? null;
            $leave->save();
        }
        dd('done sync');
    }

    public function syncAttendanceTime()
    {
        $attendances =  Attendance::with('user.shiftType')->get();
        ini_set('max_execution_time', '-1');
        foreach ($attendances as $attendance) {
            if (empty($attendance->user) || empty($attendance->user->shiftType)) {
                continue;
            }
            $shiftType  =   $attendance->user->shiftType;
            $leaves     =   Leave::where('user_id', $attendance->user_id)->where('is_approved', '1')->where('status', '<>', 'Cancelled')->where(function ($subQuery) use ($attendance) {
                $subQuery->where(function ($query1) use ($attendance) {
                    $query1->where('from_Date', '<=', $attendance->punch_date)->where('to_Date', '>=', $attendance->punch_date);
                })->orWhere(function ($query2) use ($attendance) {

                    $query2->whereBetween('from_Date', [$attendance->punch_date, $attendance->punch_date]);
                });
            })->get();
            if ($leaves->isNotEmpty()) {

                foreach ($leaves as $leave) {
                    if ($leave->leave_session == "Second half") {
                        $startTime              =   Carbon::parse($shiftType->start_time);
                        $inTime                 =   Carbon::parse($attendance->punch_in);
                        $endTime                =   Carbon::parse($shiftType->mid_time);
                        $outTime                =   Carbon::parse($attendance->punch_out);
                    } else {
                        $startTime              =   Carbon::parse($shiftType->mid_time);
                        $inTime                 =   Carbon::parse($attendance->punch_in);
                        $endTime                =   Carbon::parse($shiftType->end_time);
                        $outTime                =   Carbon::parse($attendance->punch_out);
                    }
                }
            } else {

                $startTime              =   Carbon::parse($shiftType->start_time);
                $inTime                 =   Carbon::parse($attendance->punch_in);
                $endTime                =   Carbon::parse($shiftType->end_time);
                $outTime                =   Carbon::parse($attendance->punch_out);
            }
            if (!empty($attendance->punch_in)) {
                // dd($startTime,$inTime,$inTime->diffInMinutes($startTime));
                if ($inTime < $startTime) {

                    $attendance->in         =   $inTime->diffInMinutes($startTime);
                } else {
                    $attendance->in         =   -$inTime->diffInMinutes($startTime);
                }
            }
            if (!empty($attendance->punch_out)) {
                if ($outTime > $endTime) {
                    $attendance->out        =   $outTime->diffInMinutes($endTime);
                } else {
                    $attendance->out        =  -$outTime->diffInMinutes($endTime);
                }
            }
            $attendance->save();
        }
        dd('attendance sync');
    }

    public function sortAttendanceTime()
    {
        $users    =   User::whereHas('employee',function($employee){
            $employee->whereBetween('contract_date',[now()->startOfMonth()->format('Y-m-d'),now()->format('Y-m-d')]);
        })->with(['attendances'=>function($attendances){
            $attendances->orderBy('punch_date','asc');
        }])->get();
        foreach($users as $user)
        {
            dd($users);
            if($user->attendances->isEmpty())
            {
                continue;
            }
            dd($user->attendances->first());
             $user->attendances->first()->update(['punch_in'=>'09:00:00','in'=>0]);
        }
        dd('done');
    }

    public function tester()
    {

        $departmentIds      =   auth()->user()->employee->managerDepartments->pluck('id','id')->toArray();
        $employees          =   Employee::with('user.shiftType','assetAssignments')->select('id','user_id','name')
        ->whereIn('department_id',$departmentIds)->get();

    }

    public function test234()
    {
            $attendances =  Attendance::whereNotNull('punch_in')->whereNotNull('punch_out')->where('punch_date',now()->subDay()->format('Y-m-d'))->get();
            foreach ($attendances as $attendance)
            {
                $time1 = strtotime($attendance->punch_in);
                $time2 = strtotime($attendance->punch_out);
                $difference = round(abs($time2 - $time1) / 3600,2);
                if($difference<3)
                {
                    $attendance->delete();
                }
            }
    }

    public function dailyReportId()

    {

        ini_set('max_execution_time', '-1');

        $reports        =       DailyReport::with('employee')->get();

        // dd($reports  );

        foreach ($reports as $report)

        {

            // dd($report->employee_id);

            $employee       = Employee::withoutGlobalScope('is_active')->where('id',$report->employee_id)->first();

            // dd($employee);



            $report->user_id  = !empty($employee->user_id) ? $employee->user_id :null;

            $report->save();

        }

        dd('done');




    }


    // public function test()
    // {
    //     // ini_set('max_execution_time', '-1');
    //     // $users              =   User::whereHas('employee', function ($query) {
    //     //     $query->whereNotNull('contract_date');
    //     // })->with('employee')->get();
    //     // $currentMonth       =   Carbon::now()->startOfMonth();
    //     // $lastMonth          =   Carbon::now()->startOfMonth()->subMonth();
    //     // foreach ($users as $user) {
    //     //     $joinMonth      =   Carbon::createFromFormat('Y-m-d', $user->employee->contract_date);
    //     //     if ($joinMonth->format('d') > 14 || $joinMonth->format('m')==$currentMonth->format('m')) {
    //     //         $diff   =   $joinMonth->diffInDays($currentMonth);
    //     //         if ($diff < 20) {
    //     //             continue;
    //     //         }
    //     //     }
    //     //     $leaveBalance               =   LeaveBalance::where('user_id', $user->id)->whereMonth('month', $currentMonth)->first();
    //     //     $lastMonthLeaveBalance      =   LeaveBalance::where('user_id', $user->id)->whereMonth('month', $lastMonth)->first();
    //     //     if (empty($leaveBalance)) {
    //     //         $leaveBalance               =   new LeaveBalance();
    //     //         $leaveBalance->balance      =   1.25;
    //     //         $leaveBalance->month        =   $currentMonth->format('Y-m-d');
    //     //         $leaveBalance->user_id      =   $user->id;
    //     //         if (!empty($lastMonthLeaveBalance) && $lastMonthLeaveBalance->is_forwarded != 1) {
    //     //             $leaveBalance->balance                  =   $lastMonthLeaveBalance->balance + $leaveBalance->balance;
    //     //             $lastMonthLeaveBalance->is_forwarded    =   1;
    //     //             $leaveBalance->prev_month_deduction     =   $lastMonthLeaveBalance->next_month_deduction;
    //     //             $lastMonthLeaveBalance->save();
    //     //         }
    //     //     } else {
    //     //         $lastMonthBalance                       =   0;
    //     //         if (!empty($lastMonthLeaveBalance) && $lastMonthLeaveBalance->is_forwarded != 1) {
    //     //             $lastMonthBalance   =   $lastMonthLeaveBalance->balance;
    //     //             $leaveBalance->prev_month_deduction     =   $lastMonthLeaveBalance->next_month_deduction;
    //     //             $lastMonthLeaveBalance->is_forwarded    =   1;
    //     //             $lastMonthLeaveBalance->save();
    //     //         }
    //     //         $deductibleBalance  =   $leaveBalance->balance + $lastMonthBalance + 1.25;
    //     //         $leftBalance        =   0;
    //     //         $whole              = intval($deductibleBalance);
    //     //         $decimal1           = $deductibleBalance - $whole;
    //     //         $decimal2           = round($decimal1, 2);
    //     //         $decimal            = substr($decimal2, 2);
    //     //         if ($decimal == '25' || $decimal == '75') {
    //     //             $deductibleBalance    =   $deductibleBalance - 0.25;
    //     //             $leftBalance          =   0.25;
    //     //         }


    //     //         $balance            =   $leaveBalance->deduction - $deductibleBalance;
    //     //         if ($balance <= 0) {
    //     //             // if person has next month deduction then adjust here
    //     //             if ($leaveBalance->next_month_deduction != 0) {
    //     //                 $balance            =   $leaveBalance->next_month_deduction - abs($balance);
    //     //                 if ($balance <= 0) {
    //     //                     $leaveBalance->next_month_deduction = 0;
    //     //                     $leaveBalance->balance      =    $leftBalance + abs($balance);
    //     //                 } else {
    //     //                     $leaveBalance->next_month_deduction     =   $balance;
    //     //                     $leaveBalance->balance                  =   $leftBalance;
    //     //                 }
    //     //             } else {
    //     //                 $leaveBalance->balance      =    $leftBalance + abs($balance);
    //     //                 $leaveBalance->deduction    =   0;
    //     //             }
    //     //         } else {
    //     //             $leaveBalance->balance      =   $leftBalance;
    //     //             $leaveBalance->deduction    =   $balance;
    //     //         }
    //     //     }
    //     //     $leaveBalance->save();
    //     // }
    //     // dd('dssdds');
    //     // return Response::download();
    //     return view('test')->render();

    // }


    public function test()
     {
        $pathToImage = public_path('pdf/test.jpg');
        // Browsershot::url('https://example.com')->save($pathToImage);
        Browsershot::html(view('test')->render())->save('example.jpeg');
     }

}
