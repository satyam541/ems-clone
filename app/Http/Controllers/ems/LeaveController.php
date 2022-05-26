<?php

namespace App\Http\Controllers\ems;

use Str;
use App\User;
use DateTime;
use Exception;
use Carbon\Carbon;
use App\Mail\Action;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveLogs;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\Department;
use Illuminate\Support\Arr;
use App\Exports\LeaveExport;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LeaveRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\LeaveApprovalRequest;


class LeaveController extends Controller
{
    public function __construct(Mailer $mailer)
    {
        $this->mailer       =       $mailer;
    }
    public function managerLeaveList(Request $request)
    {
        abort_if(auth()->user()->employee->managerDepartments->isEmpty() && !auth()->user()->hasRole('admin'), 403);

        $departmentIds  =   auth()->user()->employee->managerDepartments->pluck('id', 'id')->toArray();
        $leaves         =   Leave::with('user.employee.department', 'leaveType')->where(function ($query) {
            $query->where(function ($subQuery) {
                $subQuery->where('is_approved', '1')->where(function($subQuery2){
                    $subQuery2->where(function ($subQuery3){
                        $subQuery3->whereDate('to_date', '>=', today())->whereDate('from_date', '<=', today());
                    })->orWhere(function($subQuery4){
                        $subQuery4->whereDate('to_date', '>=', today())->whereDate('from_date', '>=', today());
                    });
                });
            })->orWhereIn('status', ['Pending', 'Auto Forwarded','Forwarded']);
        })->whereNotIn('status', ['Cancelled', 'Rejected'])
            ->where('user_id', '<>', auth()->user()->id);

        $leaves = $this->leaveSearch($request, $leaves);

        if (empty($departmentIds)) {
            $leaves         =   $leaves->whereHas('user.employee', function ($query) {
                $query->where('department_id', auth()->user()->employee->department_id);
            });
        } else {
            $leaves         =   $leaves->whereHas('user.employee', function ($query) use ($departmentIds) {
                $query->whereIn('department_id', $departmentIds);
            });
        }


        $data['leaves']         = $leaves->orderBy('from_date', 'desc')->get();

        $data['submitRoute']    = 'leaveAlter';
        $data['departmentIds']  = $departmentIds;
        $data['today']          = today()->format('Y-m-d');


        return view('manager.leaves', $data);
    }

    public function hrLeaveList(Request $request)
    {
        $this->authorize('hrUpdateEmployee', new Employee());
        $data['department_id']  = Department::pluck('name', 'id')->toArray();
        $data['leaveTypes']     = LeaveType::pluck('name', 'id')->toArray();
        $data['employeeDepartments']  	=  User::where('user_type','Employee')->select('id','user_type','name')->with('employee.department')->get()->groupBy('employee.department.name');
        // $data['leave_nature'] = Leave::pluck('leave_nature', 'leave_nature')->toArray();
        $leaves               = Leave::with('user.employee.department')->whereDate('to_date', '>=', Carbon::today())
            ->where('forwarded', '1')->whereNotIn('status', ['Cancelled', 'Rejected']);


        // dd($leaves->get());
        $leaves     = $this->leaveSearch($request, $leaves);
        if (request()->has('leave_session')) {
            $leaves      = $leaves->where('leave_session', $request->leave_session);
        }
        if (request()->has('department_id')) {
            $leaves = $leaves->whereHas('user.employee', function ($query) {
                $query->where('department_id', request()->department_id);
            });
        }
        if (request()->has('leave_type_id')) {
            $leaves = $leaves->where('leave_type_id', request()->leave_type_id);
        }
        if (request()->has('user_id')) {
            $leaves = $leaves->where('user_id', request()->user_id);
        }
        if (request()->has('is_pending')) {
            $leaves = $leaves->whereNull('is_approved');
        }
        // dd($leaves->get());
        $data['leaves']         = $leaves->orderByRaw("Field(status,'Forwarded','Auto Forwarded','Approved')")->paginate(10);
        $data['submitRoute']    = 'leaveAlter';
        $data['today']          = today()->format('Y-m-d');
        // $data['leaveSessions']  = ['Full day' => 'Full day', 'Half day(First half)' => 'Half day(First half)', 'Half day(Second half)' => 'Half day(Second half)'];
        $data['leaveSessions']  = Leave::pluck('leave_session', 'leave_session')->toArray();

        return view('hr.forwardedLeaves', $data);
    }

    public function  managerLeave(Request $request)
    {
        $this->authorize('hrUpdateEmployee', new Employee());
        $manager_ids    = User::whereHas('employee.managerDepartments')->pluck('id', 'id')->toArray();
        $leaves         = Leave::whereDate('to_date', '>=', today())->whereIn('user_id', $manager_ids)->whereNotIn('status', ['Cancelled', 'Rejected']);

        $leaves = $this->leaveSearch($request, $leaves);

        $data['leaves']         = $leaves->orderBy('from_date', 'desc')->paginate(8);
        $data['submitRoute']    = 'leaveAlter';
        $data['today']          = Carbon::today()->format('Y-m-d');
        $data['leaveSessions']  = Leave::pluck('leave_session', 'leave_session')->toArray();
        return view('hr.leaves', $data);
    }

    public function leaveList(Request $request)
    {
        $leaves         = Leave::with('user')->where('user_id', auth()->user()->id);
        $leaves         = $this->leaveSearch($request, $leaves)->where('user_id', auth()->user()->id);
        $data['leaves'] = $leaves->orderBy('created_at','desc')->get();

        return view('leave.leaves', $data);
    }

    public function leaveForm()
    {
        $data['model']              = new Leave();
        $data['submitRoute']        = 'submitLeave';
        $data['leaveTypes']         = LeaveType::where('name', '<>', 'Manual')->pluck('name', 'id')->toArray();
        $leaveBalance               = LeaveBalance::whereMonth('month', Carbon::today())->where('user_id', auth()->user()->id)
            ->first();
        $data['balance']            = !empty($leaveBalance) ? $leaveBalance->balance : 0;
        $data['today']              = Carbon::today()->format('Y-m-d');
        $data['max']                = Carbon::now()->startOfMonth()->addMonth()->endOfMonth()->format('Y-m-d');
        $data['leaveNature']        = $data['model']->getLeaveSession();
        // $data['shortLeaveTimings']  = $data['model']->getshortLeaveTimings();

        return view('leave.leaveForm', $data);
    }

    public function insert(LeaveRequest $request)
    {
        $shiftType      =   auth()->user()->shiftType;
        if (empty($shiftType)) {
            return back()->with('failure', 'Please contact to administrator to add your shift first.');
        }
        $leaveSession      =   'Full day';
        $timings = [$shiftType->start_time . '-' . $shiftType->end_time => $shiftType->start_time . '-' . $shiftType->end_time];
        if ($request->leave_session == 'Half day') {
            $leaveSession   =   $request->halfDayType;
            if ($request->halfDayType == 'First half') {
                $timings += [$shiftType->start_time . '-' . $shiftType->mid_time => $shiftType->start_time . '-' . $shiftType->mid_time];
            } else {
                $timings += [$shiftType->mid_time . '-' . $shiftType->end_time => $shiftType->mid_time . '-' . $shiftType->end_time];
            }
        }

        $leaveExists = $this->leaveExists($request,$leaveSession, auth()->user()->id);

        if ($leaveExists->exists()) {
            return back()->with('failure', 'Leave already Exists');
        }

        $end    = Carbon::createFromTimeString('14:00', 'Asia/Kolkata')->format('H:i:s');
        $now    = Carbon::now()->format('H:i:s');

        if ($request->from_date == Carbon::today()->format('Y-m-d') && $now > $end) {
            if ($request->leave_session == 'Full day' || $request->halfDayType == 'First half') {
                return back()->with('failure', 'You can not apply ' . $request->leave_session . ' leave now.');
            }
        }

        $leave  = new Leave();
        $leave->user_id         = auth()->user()->id;
        $leave->leave_session   = $leaveSession;
        $leave->leave_type_id   = $request->leave_type;
        $leave->from_date       = $request->from_date;
        $leave->to_date         = $request->to_date;

        if ($request->has('attachment')) {
            $file   = 'leaveFile' . Carbon::now()->timestamp . '.' . $request->file('attachment')->getClientOriginalExtension();
            $request->file('attachment')->move(storage_path('app/documents/leave_documents'), $file);
            $leave->attachment  = $file;
        }
        $leave->reason  = $request->reason;
        $leave->save();

        $carbonDate     =   Carbon::createFromFormat('Y-m-d', $leave->from_date);
        $duration       =   $leave->leave_session == 'Full day' ? $leave->duration : $leave->duration / 2;
        $appliedAt      =   Carbon::createFromFormat('Y-m-d H:i:s', $leave->created_at)->format('Y-m-d');
        // $appliedAt      =   "2022-05-24";
        $cutOffDate     =   Carbon::now()->startOfMonth()->addDays(19);
        if ($leave->from_date == Carbon::createFromFormat('Y-m-d H:i:s', $leave->created_at)->format('Y-m-d')  && $leave->duration > 1) {

            $from_date = Carbon::parse($leave->from_date)->addDays(1)->format('Y-m-d');

            $newLeave  =   new Leave();
            $newLeave->leave_session  =   $leave->leave_session;
            $newLeave->leave_type_id  =   $leave->leave_type_id;
            $newLeave->user_id        =   $leave->user_id;
            $newLeave->action_by      =   $leave->action_by;
            $newLeave->is_approved    =   $leave->is_approved;
            $newLeave->forwarded      =   $leave->forwarded;
            $newLeave->remarks        =   $leave->remarks;
            $newLeave->attachment     =   $leave->attachment;
            $newLeave->reason         =   $leave->reason;
            $newLeave->from_date      =   $from_date;
            $newLeave->to_date        =   $leave->to_date;
            $newLeave->save();

            $leave->to_date          =  $leave->from_date;
            $leave->save();

            $this->approvalDeduction($leave, $carbonDate, $duration, $appliedAt, $cutOffDate);
            $this->preApprovalBalance($newLeave, $carbonDate, $duration, $appliedAt, $cutOffDate);
        } elseif ($leave->from_date == Carbon::createFromFormat('Y-m-d H:i:s', $leave->created_at)->format('Y-m-d')) {
            $this->approvalDeduction($leave, $carbonDate, $duration, $appliedAt, $cutOffDate);
            // dd('approval');
        } else {

            $this->preApprovalBalance($leave, $carbonDate, $duration, $appliedAt, $cutOffDate);
            // dd('preapproval');
        }


        $manager                = $leave->user->employee->department->deptManager;
        $notificationReceivers  = [$manager->user_id];
        $link                   = route('managerLeaveList');
        $email                  = [$manager->office_email];
        $hr   = User::where('email', '<>', 'martha.folkes@theknowledgeacademy.com')->whereHas('roles', function ($query) {
            $query->where('name', 'hr');
        })->where('mail_sent', '1')->get();
        if (auth()->user()->employee->managerDepartments->isNotEmpty()) {
            $link   = route('hrLeaveList');

            $email  = $hr->pluck('email')->toArray();

            $notificationReceivers  = $hr->pluck('id', 'id')->toArray();
            send_notification($notificationReceivers, 'Leave applied by ' .  $leave->user->name, $link, 'leave');
            $data['leave']  = $leave;
            $data['link']   = $link;
            $message        = "Leave Applied";
            $subject        = 'Leave Applied by ' . $leave->user->name;
            send_email("email.leave", $data, $subject, $message, $email, null);
        } else {
            send_notification($notificationReceivers, 'Leave applied by ' .  $leave->user->name, $link, 'leave');
            $data['leave']  = $leave;
            $data['link']   = $link;
            $message        = "Leave Applied";
            $subject        = 'Leave Applied by ' . $leave->user->name;
            send_email("email.leave", $data, $subject, $message, $email, null);
            $request->from_date = Carbon::now()->format('Y-m-d');
            $leaveExists = $this->managerLeaveCheck($request, $manager->user->id);
            $leaveSessionCheck = ['Full day'];
            if ($leaveSession == 'Half day') {
                array_push($leaveSessionCheck, $leaveSession);
            }

            if ($leaveExists->whereIn('leave_session', $leaveSessionCheck)->exists()) {
                $remarks = "I'm on leave today";
                $leave->update(['forwarded' => '1', 'action_by' => $manager->user_id, 'remarks' => $remarks, 'status' => 'Auto Forwarded']);
                $notificationReceivers  =    $hr->pluck('id', 'id')->toArray();
                $email                  =    $hr->pluck('email')->toArray();
                $link   = route('hrLeaveList');
                send_notification($notificationReceivers, 'Leave forwarded by ' .  $manager->name, $link, 'leave');
                $data['leave']  = $leave;
                $data['link']   = $link;
                $message        = "Leave Forwarded";
                $subject        = 'Leave Forwarded by ' . $manager->name;
                send_email("email.leave", $data, $subject, $message, $email, null);
            }
        }
        return redirect()->route('leaveList')->with('success', 'Leave applied');
    }

    function leaveAction(Request $request)
    {
        $leave                  = Leave::find($request->id);
        $message                = null;
        $notificationReceivers  = [$leave->user->id];
        $link                   = route('leaveList');
        if ($request->action == 'forward') {
            $leave->forwarded   = 1;
            $leave->status      = 'Forwarded';
            $leave->remarks     = $request->remarks;
            $leave->save();
            $message            = 'Leave forwarded';
            $link               = route('hrLeaveList');
            $user               = User::where('email', '<>', 'martha.folkes@theknowledgeacademy.com')->whereHas('roles', function ($query) {
                $query->where('name', 'hr');
            })->get();

            $email              = $user->pluck('email', 'email')->toArray();
            $notificationReceivers  = $user->pluck('id', 'id')->toArray();
            send_notification($notificationReceivers, 'Leave forwarded by ' . auth()->user()->name, $link, 'leave');
            $data['leave']      = $leave;
            $data['link']       = $link;
            $subject            = "Leave Forwarded by " . auth()->user()->name;
            send_email("email.action", $data, $subject, $message, $email, null);

            return $message;
        } elseif ($request->action == 'Approved') {
            $leave->is_approved = 1;

            if ($leave->from_date == Carbon::createFromFormat('Y-m-d H:i:s', $leave->created_at)->format('Y-m-d')) {
                $leave->status      = 'Approved';
            } else {
                $leave->status      = 'Pre Approved';
            }
            $message            = "Leave $request->action";
            send_notification($notificationReceivers, "Leave $request->action", $link, 'leave');
            $data['link']       = $link;
            $data['message']    = $message;
            $subject            = "Leave $request->action";
            $email              = [User::find($leave->user_id)->email];
            $cc = [];
            if ($leave->forwarded) {
                $cc                 = $leave->user->employee->department->deptManager->office_email;
                $cc                 = Arr::wrap($cc);
            }
            $emailData['message']  =    $message;
            $emailData['link']     =    $link;
            // send_email("email.action", $data, $subject, $message, $email, $file = null, $cc);
            $message = (new Action($leave, $emailData,$message,'email.action'))->onQueue('emails');
            $this->mailer->to($email)->cc($cc)->later(Carbon::now()->addSeconds(30),$message);
        } else {
            $message            = 'Leave rejected';
            $leave->is_approved = 0;
            $leave->status      = 'Rejected';
            send_notification($notificationReceivers, 'Leave rejected', $link, 'leave');
            $data['link']       = $link;
            $data['message']    = $message;
            $subject            = "Leave Rejected";
            $email              = User::find($notificationReceivers)->first()->email;
            $cc = [];
            if ($leave->forwarded) {
                $cc                 = $leave->user->employee->department->deptManager->office_email;
                $cc                 = Arr::wrap($cc);
            }
            $this->updateLeaveBalance($leave);
            // send_email("email.action", $data, $subject, $message, $email, $file = null, $cc);
            $emailData['message']  =    $message;
            $emailData['link']     =    $link;
            // send_email("email.action", $data, $subject, $message, $email, $file = null, $cc);
            $message = (new Action($leave, $emailData,$message,'email.action'))->onQueue('emails');
            $this->mailer->to($email)->cc($cc)->later(Carbon::now()->addSeconds(30),$message);
        }
        $leave->action_by   = auth()->user()->id;
        $leave->remarks     = $request->remarks ?? null;
        $leave->save();

        return $message;
    }

    public function viewFile(Request $request)
    {
        $file   = storage_path("app/documents/leave_documents/$request->file");

        return response()->file($file, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    public function hrLeaveHistoryList(Request $request)
    {
        $this->authorize('hrUpdateEmployee', new Employee());
        $data['leave_types']    =   LeaveType::pluck('name', 'id')->toArray();
        $data['leave_session']  =   Leave::pluck('leave_session', 'leave_session')->toArray();
        $data['department']     =   Department::pluck('name', 'id')->toArray();
        $leaves                 =   Leave::with('user.employee.department');

        $leaves         = $this->leaveSearch($request, $leaves);

        if (request()->has('leave_type_id')) {
            $leaves      = $leaves->where('leave_type_id', $request->leave_type_id);
        }
        if (request()->has('leave_session')) {
            $leaves      = $leaves->where('leave_session', $request->leave_session);
        }
        if (request()->has('user_id')) {
            $leaves      = $leaves->where('user_id', $request->user_id);
        }

        if (request()->has('department_id')) {
            $leaves = $leaves->whereHas('user.employee', function ($query) {
                $query->where('department_id', request()->department_id);
            });
            // $users  = $users->where('department_id', $request->department_id);
        }
        if (!empty(request()->get("dateFrom")) || !empty(request()->get("dateTo"))) {
            $leaves = $leaves->WhereBetween("from_date", [$request->dateFrom, $request->dateTo]);
        }

        if (request()->has('pre_approved')) {

            $leaves = $leaves->where('status', 'Pre Approved');
        }

        $data['leaves']         = $leaves->orderBy('from_date', 'desc')->paginate(10);
        $data['submitRoute']    = 'hrLeaveHistoryCancel';
        $data['users']       =  User::pluck('name', 'id')->toArray();

        return view('hr.leaveHistory', $data);
    }

    public function managerLeaveHistory(Request $request)
    {
        $this->authorize('managerLeaveList', new Leave());
        $departmentIds              =  auth()->user()->employee->managerDepartments->pluck('id', 'id')->toArray();
        $data['departmentCount']    = (count($departmentIds) > 1) ? true : false;

        $leaves         = Leave::with('user.employee.department');

        if (empty($departmentIds)) {
            $leaves         =   $leaves->whereHas('user.employee', function ($query) {
                $query->where('department_id', auth()->user()->employee->department_id);
            });
        } else {
            $leaves         =   $leaves->whereHas('user.employee', function ($query) use ($departmentIds) {
                $query->whereIn('department_id', $departmentIds);
            });
        }

        $leaves         = $this->leaveSearch($request, $leaves);
        $data['leaves'] = $leaves->orderBy('from_date', 'desc')->get();

        return view('manager.leaveHistory', $data);
    }

    public function export(Request $request)
    {

        ini_set('max_execution_time', -1);
        return Excel::download(new LeaveExport($request), 'leave.xlsx');
    }

    function hrLeaveHistoryCancel(Request $request)
    {
        $leave = Leave::find($request->id);
        $leave->update(['status' => 'Cancelled', 'is_approved' => 0]);
        $message    = 'Leave Cancelled';
        $this->leaveProcess($leave, $message);
        $this->updateLeaveBalance($leave);
        return back()->with('success', 'Leave cancelled');
    }

    private function leaveProcess($leave, $message)
    {
        $email      = $leave->user->employee->office_email;
        $notificationReceivers  = $leave->user->employee->user_id;
        $link       = route('leaveList');

        send_notification($notificationReceivers, $message . " by " . auth()->user()->employee->name, $link, 'leave');

        $data['leave']  = $leave;
        $data['link']   = $link;
        $subject        = $message . " by " . auth()->user()->name;

        send_email("email.action", $data, $subject, $message, $email, null);
    }



    public function cancelLeave(Request $request)
    {


        $leave  = Leave::find($request->leave_id);


        // $duration      =   $leave->leave_session=='Full day' ? $leave->duration:$leave->duration/2;
        // $carbonDate    =   Carbon::createFromFormat('Y-m-d',$leave->from_date);
        // $leaveBalance  =   LeaveBalance::whereMonth('month', $carbonDate)->where('user_id',auth()->user()->id)->first();
        // $deduction                  =   $leaveBalance->deduction    - $duration;
        // $leaveBalance->deduction    =   $deduction;
        // $leaveBalance->taken_leaves =   $leaveBalance->taken_leaves    - $duration;
        // if($deduction<0)
        // {
        //     $leaveBalance->balance      =   $leaveBalance->balance + abs($deduction);
        //     $leaveBalance->deduction    =   0;
        // }
        // $leaveBalance->save();

        $leave->update(['status' => 'Cancelled', 'is_approved' => 0]);
        $this->updateLeaveBalance($leave);
        $department             = $leave->user->employee->department;
        $manager                = $department->managerDetails();
        $notificationReceivers  = [$manager->user->id];
        $email                  = [$manager->office_email];
        $link                   = route('leaveList');
        $data['leave']          = $leave;
        $data['link']           = $link;
        $message                = "Leave Cancelled";
        $subject                = "Leave Cancelled by " . auth()->user()->name;



        if (auth()->user()->hasRole('manager')) {
            // $link=route('hrLeaveList');
            $user   = User::where('email', '<>', 'martha.folkes@theknowledgeacademy.com')->whereHas('roles', function ($query) {
                $query->where('name', 'hr');
            })->get();
            $email  = $user->pluck('email')->toArray();
            $notificationReceivers  = $user->pluck('id')->toArray();
        }



        send_notification($notificationReceivers, 'Leave Cancelled by ' . auth()->user()->employee->name, $link, 'leave');
        send_email("email.action", $data, $subject, $message, $email, null);
    }

    public function bulkLeaveAction(Request $request)
    {
        $leaveIds =   $request->leaves;
        ini_set('max_execution_time',-1);
        foreach($leaveIds as $leaveId)
        {
            $request->request->add(['id'=>$leaveId]);
            $this->leaveAction($request);
        }
    }

    private function updateLeaveBalance($leave)
    {
        $carbonDate     =   Carbon::createFromFormat('Y-m-d', $leave->from_date);
        $duration       =   $leave->leave_session == 'Full day' ? $leave->duration : $leave->duration / 2;
        $appliedAt      =   Carbon::createFromFormat('Y-m-d H:i:s', $leave->created_at)->format('Y-m-d');
        $fromMonth                  =   Carbon::createFromFormat('Y-m-d', $leave->from_date)->format('m');
        $cutOffDateMonth            =   Carbon::now()->format('m');
        // $appliedAt      =   "2022-05-24";
        // leave applied on same date
        $cutOffDate     =   Carbon::now()->startOfMonth()->addDays(19);
        if ($fromMonth != $cutOffDateMonth) {
            $cutOffDate         =   Carbon::now()->addMonth(1)->startOfMonth()->addDays(19);
        }
        $leaveBalance       =   LeaveBalance::whereMonth('month', $carbonDate)->where('user_id', $leave->user_id)->first();
        $leaveBalance->taken_leaves            =   $leaveBalance->taken_leaves  -   $duration;
        // leave applied before cutoff date
        if ($leave->from_date < $cutOffDate ) {
            if ($leave->from_date != Carbon::createFromFormat('Y-m-d H:i:s', $leave->created_at)->format('Y-m-d')) {

                $extraBalance                          =   $leaveBalance->deduction    -   $duration;
                if ($extraBalance < 0) {
                    $extraBalance       =   abs($extraBalance);
                        if ($leaveBalance->next_month_deduction != 0) {
                            $extraBalance       = $leaveBalance->next_month_deduction    -   $extraBalance;
                            if ($extraBalance <= 0) {
                                $leaveBalance->balance          =   $leaveBalance->balance +    abs($extraBalance);
                                $leaveBalance->next_month_deduction    =   0;
                            } else {
                                $leaveBalance->next_month_deduction    =   $extraBalance;
                            }
                        } else {
                            $leaveBalance->balance      =   $leaveBalance->balance +    abs($extraBalance);
                        }

                    $leaveBalance->deduction    =   0;
                } else {
                    $leaveBalance->deduction    =   $extraBalance;
                }
            } else {

                $leaveBalance->deduction        =   $leave->deduction - $duration;
            }
        }
        // leave applied after cutoff date
        else {
            if ($leave->from_date != Carbon::createFromFormat('Y-m-d H:i:s', $leave->created_at)->format('Y-m-d')) {
                $extraBalance           =   $leaveBalance->next_month_deduction -   $duration;
                if ($extraBalance < 0) {
                    $extraBalance       =   abs($extraBalance);
                        // if there is deduction available for same month
                        if ($leaveBalance->deduction != 0) {
                            $extraBalance       = $leaveBalance->deduction    -   $extraBalance;
                            if ($extraBalance < 0) {
                                $leaveBalance->balance          =   $leaveBalance->balance +    abs($extraBalance);
                                $leaveBalance->deduction               =   0;
                            } else {
                                $leaveBalance->deduction               =   $extraBalance;
                            }
                        } else {
                            $leaveBalance->balance      =   $leaveBalance->balance +    abs($extraBalance);
                        }

                    $leaveBalance->next_month_deduction    =   0;
                } else {
                    $leaveBalance->next_month_deduction    =   $extraBalance;
                }
            } else {
                $leaveBalance->next_month_deduction        =   $leave->next_month_deduction - $duration;
            }
        }


        // $duration      =   $leave->leave_session == 'Full day' ? $leave->duration : $leave->duration / 2;
        // $carbonDate    =   Carbon::createFromFormat('Y-m-d', $leave->from_date);
        // $leaveBalance  =   LeaveBalance::whereMonth('month', $carbonDate)->where('user_id', $leave->user_id)->first();
        // if (empty($leaveBalance)) {

        //     return null;
        // }
        // $deduction                  =   $leaveBalance->deduction    - $duration;
        // $leaveBalance->deduction    =   $deduction;
        // $leaveBalance->taken_leaves =   $leaveBalance->taken_leaves    - $duration;
        // if ($leave->from_date != Carbon::createFromFormat('Y-m-d H:i:s', $leave->created_at)->format('Y-m-d')) {

        //     if ($deduction < 0) {
        //         $leaveBalance->balance      =   $leaveBalance->balance + abs($deduction);
        //     }
        // }
        // if ($deduction < 0) {
        //     $leaveBalance->deduction    =   0;
        // }
        $leaveBalance->save();
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
    private function managerLeaveCheck($request, $user_id)
    {
        return Leave::where('user_id', $user_id)->whereNotIn('status', ['Rejected', 'Cancelled'])
            ->where(function ($subQuery) use ($request) {

                $subQuery->where(function ($query1) use ($request) {

                    $query1->where('from_Date', '<=', $request->from_date)->where('to_Date', '>=', $request->from_date);
                });
            });
    }
    private function leaveSearch(Request $request, $leaves)
    {
        if (!empty(request()->dateFrom) || !empty(request()->dateTo)) {
            $leaves->where(function ($subQuery) use ($request) {
                $subQuery->where(function ($query1) use ($request) {
                    $query1->where('from_Date', '<=', $request->dateFrom)->where('to_Date', '>=', $request->dateFrom);
                })->orWhere(function ($query2) use ($request) {

                    $query2->whereBetween('from_Date', [$request->dateFrom, $request->dateTo]);
                });
            });
        } else {
            $leaves->whereYear('from_date', '>=', Carbon::now()->year);
        }

        return $leaves;
    }

    private function preApprovalBalance($leave, $carbonDate, $duration, $appliedAt, $cutOffDate)
    {

        $fromMonth                  =   Carbon::createFromFormat('Y-m-d', $leave->from_date)->format('m');
        $cutOffDateMonth            =   Carbon::now()->format('m');
        if ($fromMonth != $cutOffDateMonth) {
            $cutOffDate         =   Carbon::now()->addMonth(1)->startOfMonth()->addDays(19);
        }

        $getBalance     =   $this->getBalance($leave, $carbonDate);
        $leaveBalance   =   $getBalance['leaveBalance'];
        if (empty($leaveBalance)) {
            $leaveBalance                  =   new LeaveBalance();
            $leaveBalance->month           =   $leave->from_date;
            $leaveBalance->balance         =   0;
            $leaveBalance->taken_leaves    =   $duration;
            $leaveBalance->user_id         =   auth()->user()->id;
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
        $getBalance     =   $this->getBalance($leave, $carbonDate);
        $leaveBalance   =   $getBalance['leaveBalance'];
        // same month deduction
        if (empty($leaveBalance)) {
            $leaveBalance                  =   new LeaveBalance();
            $leaveBalance->month           =   $leave->from_date;
            $leaveBalance->balance         =   0;
            $leaveBalance->taken_leaves    =   $duration;
            $leaveBalance->user_id         =   auth()->user()->id;
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
}
