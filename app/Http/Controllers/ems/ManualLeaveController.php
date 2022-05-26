<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveLogs;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\ManualLeaveRequest;
use App\Http\Controllers\ems\LeaveController;


class ManualLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $leaves                 =   Leave::where('is_manual', '1')->with('employee.department', 'leaveType');

        if (request()->has('leave_type_id')) {
            $leaves             =   $leaves->where('leave_type_id', $request->leave_type_id);
        }

        if (request()->has('leave_session')) {
            $leaves             =   $leaves->where('leave_session', $request->leave_session);
        }

        if (request()->has('department_id')) {
            $leaves = $leaves->whereHas('user.employee', function ($query) {
                $query->where('department_id', request()->department_id);
            });
        }

        if (request()->has('employee')) {
            $leaves             =   $leaves->where('user_id', $request->employee);
        }

        if (!empty(request()->dateFrom) || !empty(request()->dateTo)) {
            $leaves = $leaves->where(function ($subQuery) use ($request) {
                $subQuery->where(function ($query1) use ($request) {
                    $query1->where('from_Date', '<=', $request->dateFrom)->where('to_Date', '>=', $request->dateFrom);
                })->orWhere(function ($query2) use ($request) {

                    $query2->whereBetween('from_Date', [$request->dateFrom, $request->dateTo]);
                });
            });
        }

        $data['leaveTypes']     =   LeaveType::pluck('name', 'id')->toArray();
        $data['sessions']       =   Leave::pluck('leave_session', 'leave_session')->toArray();
        $data['departments']    =   Department::pluck('name', 'id')->toArray();
        $data['employees']      =   User::pluck('name', 'id')->toArray();
        // dd( $data['sessions']);
        $data['leaves']         =   $leaves->get();
        return view('leave.manualLeaves', $data);
    }

    /**

     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $data['leave']         = new Leave();
        $data['leaveSessions'] = $data['leave']->getLeaveSession();
        $data['submitRoute']   = ['manual-leave.store'];
        $data['today']         = today()->startOfMonth()->format('Y-m-d');
        $data['leaveTypes']    = LeaveType::pluck('name', 'id');
        $data['status']        =  Config::get('leave.status');
        $data['method']        =   'POST';
        $data['max']           = Carbon::now()->startOfMonth()->addMonth()->endOfMonth()->format('Y-m-d');
        $data['employeeDepartments']          = Employee::where('is_active', '1')->whereHas('user', function ($user) {
            $user->where('is_active', '1')->where('user_type', 'Employee');
        })->select('user_id', 'department_id', 'biometric_id', 'name')->get()->groupBy('department.name');
        return view('leave.manualLeaveForm', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ManualLeaveRequest $request)
    {



        $leaveSession = empty($request->leave_session) ? "Full day" : $request->leave_session;
        $leaveExists    =   $this->leaveExists($request,$leaveSession ,$request->user_id);
        if ($leaveExists->exists()) {
            return back()->with('failure', 'Leave already Exists');
        }

        $leave  = new Leave();
        $leave->user_id           =  $request->user_id;
        $leave->leave_session     =  $leaveSession;
        $leave->leave_type_id     =  $request->leave_type;
        $leave->from_date         =  $request->from_date;
        $leave->to_date           =  $request->to_date;
        $leave->status            =  $request->status;
        $leave->reason            =  $request->reason;
        $leave->is_manual         =  1;
        $leave->is_approved       =  1;
        $leave->action_by         = auth()->user()->id;


        if (!empty($request->attachment)) {

            $file   = 'leaveFile' . Carbon::now()->timestamp . '.' . $request->file('attachment')->getClientOriginalExtension();
            $request->file('attachment')->move(storage_path('app/documents/leave_documents'), $file);
            $leave->attachment  = $file;
        }
        $leave->save();
        $carbonDate    =   Carbon::createFromFormat('Y-m-d', $leave->from_date);
        $duration      =   $leave->leave_session == 'Full day' ? $leave->duration : $leave->duration / 2;
        $appliedAt      =  Carbon::createFromFormat('Y-m-d H:i:s', $leave->created_at)->format('Y-m-d');
        // $appliedAt         =  '2022-05-22';
        $cutOffDate     =  Carbon::now()->startOfMonth()->addDays(19);
        if ($leave->status == 'Pre Approved') {


            $this->preApprovalBalance($leave, $carbonDate, $duration, $appliedAt, $cutOffDate);
        } elseif ($leave->status == "Approved") {

            $this->approvalDeduction($leave, $carbonDate, $duration, $appliedAt, $cutOffDate);
        } else {

            $this->absentDeduction($leave, $carbonDate, $duration, $appliedAt, $cutOffDate);
        }
        return redirect(route('manual-leave.index'))->with('success', 'Leave Added');
    }


    private function absentDeduction($leave, $carbonDate, $duration, $appliedAt, $cutOffDate)
    {


        $getBalance      =        $this->getBalance($leave, $carbonDate);
        $leaveBalance    =         $getBalance['leaveBalance'];
        // $leaveBalance

        if (empty($leaveBalance)) {

            $leaveBalance                  =   new LeaveBalance();
            $leaveBalance->month           =   $leave->from_date;
            $leaveBalance->balance         =   0;
            $leaveBalance->taken_leaves    =   $duration;
            $leaveBalance->user_id         =   $leave->user_id;

            if ($appliedAt < $cutOffDate) {
                $leaveBalance->absent =   $duration * 2;
            } elseif ($appliedAt > $cutOffDate) {
                $leaveBalance->next_month_deduction =   $duration * 2;
            }
        } else {

            // $deductibleBalance  =   $getBalance['deductibleBalance'];
            // $leftBalance        =   $getBalance['leftBalance'];

            // $finalBalance                          =   $deductibleBalance   -  ($duration * 2);
            // if ($finalBalance >= 0) {
            //     $leaveBalance->balance             =   $finalBalance  + $leftBalance;
            // } else {
                // $leaveBalance->balance             =   $leftBalance;
                if ($appliedAt < $cutOffDate) {
                    $leaveBalance->absent              =    $leaveBalance->absent + $duration * 2;
                } elseif ($appliedAt > $cutOffDate) {
                    $leaveBalance->next_month_deduction =   $leaveBalance->next_month_deduction +   $duration * 2;
                }
            // }
        }
        $leaveBalance->save();
    }

    private function preApprovalBalance($leave, $carbonDate, $duration, $appliedAt, $cutOffDate)
    {

        $getBalance      =        $this->getBalance($leave, $carbonDate);
        $leaveBalance    =         $getBalance['leaveBalance'];
        if (empty($leaveBalance)) {
            $leaveBalance                  =   new LeaveBalance();
            $leaveBalance->month           =   $leave->from_date;
            $leaveBalance->balance         =   0;
            $leaveBalance->taken_leaves    =   $duration;
            $leaveBalance->user_id         =   $leave->user_id;
            if ($leave->from_date < $cutOffDate ) {
                $leaveBalance->deduction       =   $duration;
            } elseif ($leave->from_date > $cutOffDate) {
                $leaveBalance->next_month_deduction =   $duration;
            }
        } else {

            $deductibleBalance  =   $getBalance['deductibleBalance'];
            $leftBalance        =   $getBalance['leftBalance'];
            $finalBalance                          =   $deductibleBalance           -   $duration;
            $leaveBalance->taken_leaves            =   $leaveBalance->taken_leaves  +   $duration;
            if ($finalBalance >= 0) {
                $leaveBalance->balance             =   $finalBalance  + $leftBalance;
            } else {
                $leaveBalance->balance             =   $leftBalance;
                if ($leave->from_date < $cutOffDate ) {
                    $leaveBalance->deduction           =   $leaveBalance->deduction + abs($finalBalance);
                } else {
                    $leaveBalance->next_month_deduction       =    $leaveBalance->next_month_deduction + abs($finalBalance);
                }
            }
        }
        $leaveBalance->save();
    }

    private function approvalDeduction($leave, $carbonDate, $duration, $appliedAt, $cutOffDate)
    {

        $getBalance      =        $this->getBalance($leave, $carbonDate);
        $leaveBalance    =         $getBalance['leaveBalance'];
        // same month deduction
        if (empty($leaveBalance)) {
            $leaveBalance                  =   new LeaveBalance();
            $leaveBalance->month           =   $leave->from_date;
            $leaveBalance->balance         =   0;
            $leaveBalance->taken_leaves    =   $duration;
            $leaveBalance->user_id         =  $leave->user_id;
            if ($leave->from_date < $cutOffDate ) {
                $leaveBalance->deduction       =   $duration;
            }
            // deduction from next month after 20th leave apply if balance is less then duration
            elseif ($leave->from_date > $cutOffDate) {
                $leaveBalance->next_month_deduction =   $duration;
            }
        } else {
            $leaveBalance->taken_leaves            =   $leaveBalance->taken_leaves  +   $duration;
            if ($leave->from_date < $cutOffDate ) {
                $leaveBalance->deduction       =   $leaveBalance->deduction    +   $duration;
            }
            // deduction from next month after 20th leave apply if balance is less then duration
            elseif ($leave->from_date > $cutOffDate) {
                $leaveBalance->next_month_deduction =                   $leaveBalance->next_month_deduction +   $duration;
            }
        }
        $leaveBalance->save();
    }

    private function getBalance($leave, $carbonDate)
    {
        $leaveBalance   =   LeaveBalance::whereMonth('month', $carbonDate)->where('user_id', $leave->user_id)->first();
        $deductibleBalance  =   0;
        $leftBalance        =   0;
        if (!empty($leaveBalance) && $leaveBalance->balance > 0) {
            $balance = $leaveBalance->balance;
            $whole   = intval($balance);
            $decimal1 = $balance - $whole;
            $decimal2 = round($decimal1, 2);
            $decimal = substr($decimal2, 2);
            if ($decimal != '25' && $decimal != '75') {
                $deductibleBalance    =   $leaveBalance->balance;
                $leftBalance          =   0;
            } else {
                $deductibleBalance    =   $leaveBalance->balance - 0.25;
                $leftBalance          =   0.25;
            }
        }
        $data['deductibleBalance']      =   $deductibleBalance;
        $data['leftBalance']            =   $leftBalance;
        $data['leaveBalance']           =   $leaveBalance;
        return $data;
    }

    private function leaveExists($request,$leaveSession, $user_id)
    {

        $sessions=['Full day'=>'Full day','First half'=>'First half','Second half'=>'Second half'];
        if($leaveSession=='First half')
        {
            $sessions=['Full day'=>'Full day','First half'=>'First half'];
        }
        elseif($leaveSession=='Second half')
        {
            $sessions=['Full day'=>'Full day','Second half'=>'Second half'];
        }
        return Leave::where('user_id', $user_id)->whereNotIn('status', ['Rejected', 'Cancelled'])->whereIn('leave_session', $sessions)
            ->where(function ($subQuery) use ($request) {

                $subQuery->where(function ($query1) use ($request) {

                    $query1->where('from_Date', '<=', $request->from_date)->where('to_Date', '>=', $request->from_date);
                })->orWhere(function ($query2) use ($request) {

                    $query2->whereBetween('from_Date', [$request->from_date, $request->to_date]);
                });
            });
    }
}
