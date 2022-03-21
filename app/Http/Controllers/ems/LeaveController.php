<?php

namespace App\Http\Controllers\ems;

use Str;
use App\User;
use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Arr;
use App\Exports\LeaveExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LeaveRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\LeaveApprovalRequest;


class LeaveController extends Controller
{
    public function managerLeaveList(Request $request)
    {
        abort_if(auth()->user()->employee->managerDepartments->isEmpty() && !auth()->user()->hasRole('admin'),403);

        $departmentIds  =   auth()->user()->employee->managerDepartments->pluck('id','id')->toArray();
        $leaves         =   Leave::with('employee.department')->where(function($query){
                                        $query->where(function($subquery){
                                            $subquery->where('status','Approved')->whereDate('to_date','>=',Carbon::today());
                                        })->orWhereIn('status',['Pending','Auto Forwarded']);
                                    })->whereNotIn('status',['Cancelled','Rejected'])
                                    ->where('employee_id','<>',auth()->user()->employee->id);

        $leaves = $this->leaveSearch($request, $leaves);

        if(empty($departmentIds))
        {
            $leaves         =   $leaves->whereHas('employee',function($query){
                                    $query->where('department_id',auth()->user()->employee->department_id);
                                });
        }
        else{
            $leaves         =   $leaves->whereHas('employee',function($query) use($departmentIds){
                                            $query->whereIn('department_id',$departmentIds);
                                        });
        }

        $data['leaves']         = $leaves->orderBy('from_date','desc')->get();

        $data['submitRoute']    = 'leaveAlter';
        $data['departmentIds']  = $departmentIds;
        $data['today']          = today()->format('Y-m-d');
        $data['leaveTypes']     = ['Full day'=>'Full day','Half day(First half)'=>'Half day(First half)','Half day(Second half)'=>'Half day(Second half)'];

        return view('manager.leaves',$data);
    }

    public function hrLeaveList(Request $request)
    {
        $this->authorize('hrUpdateEmployee',new Employee());
        $data['department_id']  = Department::pluck('name','id')->toarray();
        $data['leave_nature'] = Leave::pluck('leave_nature','leave_nature')->toArray();
        $leaves               = Leave::with('employee.department')->whereDate('to_date','>=',Carbon::today())
                                ->where('forwarded','1')->whereNotIn('status',['Cancelled','Rejected']);

        $leaves     = $this->leaveSearch($request, $leaves);
        if(request()->has('leave_nature'))
        {
           $leaves      = $leaves->where('leave_nature',$request->leave_nature);
        }
        if(request()->has('department_id'))
        {
            $leaves = $leaves->whereHas('employee',function($query){
                $query->where('department_id',request()->department_id);
            });
        }
        $data['leaves']         = $leaves->orderByRaw("Field(status,'Forwarded','Auto Forwarded','Approved')")->paginate(10);
        $data['submitRoute']    = 'leaveAlter';
        $data['today']          = today()->format('Y-m-d');
        $data['leaveTypes']     = ['Full day'=>'Full day','Half day(First half)'=>'Half day(First half)','Half day(Second half)'=>'Half day(Second half)'];

        return view('hr.forwardedLeaves',$data);
    }

    public function  managerLeave(Request $request)
    {
        $this->authorize('hrUpdateEmployee',new Employee());
        $manager_ids    = Employee::whereHas('managerDepartments')->pluck('id','id')->toArray();
        $leaves         = Leave::whereDate('to_date','>=',today())->whereIn('employee_id', $manager_ids)->whereNotIn('status',['Cancelled','Rejected']);

        $leaves = $this->leaveSearch($request, $leaves);

        $data['leaves']         = $leaves->orderBy('from_date','desc')->paginate(8);
        $data['submitRoute']    = 'leaveAlter';
        $data['today']          = Carbon::today()->format('Y-m-d');
        $data['leaveTypes']     = ['Full day'=>'Full day','Half day(First half)'=>'Half day(First half)','Half day(Second half)'=>'Half day(Second half)'];

        return view('hr.leaves',$data);
    }

    public function leaveList(Request $request)
    {
        $leaves         = Leave::with('employee')->where('employee_id',auth()->user()->employee->id);
        $leaves         = $this->leaveSearch($request, $leaves)->where('employee_id',auth()->user()->employee->id);
        $data['leaves'] = $leaves->orderBy('from_date','desc')->get();

        return view('leave.leaves',$data);
    }

    public function leaveForm()
    {
        $data['model']              = new Leave();
        $data['submitRoute']        = 'submitLeave';
        $data['leaveTypes']         = $data['model']->getTypes();
        $data['today']              = Carbon::today()->format('Y-m-d');
        $data['leaveNature']        = $data['model']->getLeaveNature();
        $data['shortLeaveTimings']  = $data['model']->getshortLeaveTimings();

        return view('leave.leaveForm',$data);
    }

    public function insert(LeaveRequest $request)
    {
        $timings=['9:00-18:00'=>'9:00-18:00'];
        if($request->leave_type=='Half day')
        {
            if($request->halfDayType=='First half')
            {
                $timings+=['9:00-14:00'=>'9:00-14:00'];
            }
            else
            {
                $timings+=['14:00-18:00'=>'14:00-18:00'];
            }
        }

        $leaveExists= $this->leaveExists($request,auth()->user()->employee->id);

        if(count($timings)>1)
        {
            $leaveExists->whereIn('timing',$timings);
        }
        if($leaveExists->exists())
        {
            return back()->with('failure','Leave already Exists');
        }

        $end    = Carbon::createFromTimeString('13:20','Asia/Kolkata')->format('H:i:s');
        $now    = Carbon::now()->format('H:i:s');

        if($request->from_date == Carbon::today()->format('Y-m-d') && $now > $end)
        {
            if($request->leave_type == 'Full day' || $request->halfDayType=='First half')
            {
                return back()->with('failure','You can not apply '.$request->leave_type.' leave now.');
            }
        }

        $leave  = new Leave();
        $leave->employee_id     = auth()->user()->employee->id;
        $leave->leave_nature    = $request->leave_nature;
        $leave->leave_type      = $request->leave_type;
        $leave->from_date       = $request->from_date;
        $leave->to_date         = $request->to_date;
        $leave->timing          = '9:00-18:00';

        if($request->leave_type=='Short leave')
        {
            $leave->timing  = $request->timing;
        }

        if($request->leave_type=='Half day')
        {
            $leave->leave_type  = $request->leave_type.'('.$request->halfDayType.')';
            $leave->timing      = '14:00-18:00';

            if($request->halfDayType=='First half')
            {
            $leave->timing  = '9:00-14:00';
            }
        }

        if($request->has('attachment'))
        {
            $file   = 'leaveFile'.Carbon::now()->timestamp.'.'.$request->file('attachment')->getClientOriginalExtension();
            $request->file('attachment')->move(storage_path('app/documents/leave_documents'), $file);
            $leave->attachment  = $file;
        }
        // $from_date = Carbon::parse($leave->from_date);
        // $to_date = Carbon::parse($leave->to_date);
        // $duration = $from_date->diffInDays($to_date);
        // $leave->duration=$duration+1;
        $leave->reason  = $request->reason;
        // if(auth()->user()->hasRole('manager'))
        // {
        //     // $leave->forwarded=1;

        // }

        $leave->save();
        $manager                = $leave->employee->department->deptManager;
        $notificationReceivers  = [$manager->user_id];
        $link                   = route('managerLeaveList');
        $email                  = [$manager->office_email];
        $hr   = User::where('email','<>','martha.folkes@theknowledgeacademy.com')->whereHas('roles',function($query){
            $query->where('name','hr');
        })->where('mail_sent','1')->get();
        if(auth()->user()->employee->managerDepartments->isNotEmpty())
        {
            $link   = route('hrLeaveList');

            $email  = $hr->pluck('email')->toArray();

            $notificationReceivers  = $hr->pluck('id','id')->toArray();
            send_notification($notificationReceivers,'Leave applied by ' .  $leave->employee->name,$link,'leave');
            $data['leave']  = $leave;
            $data['link']   = $link;
            $message        = "Leave Applied";
            $subject        = 'Leave Applied by '.$leave->employee->name;
            send_email("email.leave", $data, $subject, $message,$email,null);
        }
        else
        {
            send_notification($notificationReceivers,'Leave applied by ' .  $leave->employee->name,$link,'leave');
            $data['leave']  = $leave;
            $data['link']   = $link;
            $message        = "Leave Applied";
            $subject        = 'Leave Applied by '.$leave->employee->name;
            send_email("email.leave", $data, $subject, $message,$email,null);
            $request->from_date=Carbon::now()->format('Y-m-d');
            $leaveExists=$this->managerLeaveCheck($request,$manager->id);
            $leaveType=['Full Day'];
            if($request->leave_type=='Half day')
            {
                array_push($leaveType,$request->leave_type.'('.$request->halfDayType.')');
            }

            if($leaveExists->whereIn('leave_type',$leaveType)->exists())
            {
                $remarks="I'm on leave today";
                $leave->update(['forwarded'=>'1','action_by'=>$manager->user_id,'remarks'=>$remarks,'status'=>'Auto Forwarded']);
                $notificationReceivers  =    $hr->pluck('id','id')->toArray();
                $email                  =    $hr->pluck('email')->toArray();
                $link   = route('hrLeaveList');
                send_notification($notificationReceivers,'Leave forwarded by ' .  $manager->name,$link,'leave');
                $data['leave']  = $leave;
                $data['link']   = $link;
                $message        = "Leave Forwarded";
                $subject        = 'Leave Forwarded by '.$manager->name;
                send_email("email.leave", $data, $subject, $message,$email,null);
            }
        }
        return redirect()->route('leaveList')->with('success','Leave applied');
    }

    function leaveAction(Request $request)
    {
        $leave                  = Leave::find($request->id);
        $message                = null;
        $notificationReceivers  = [$leave->employee->user_id];
        $link                   = route('leaveList');

        if($request->action=='forward')
        {
            $leave->forwarded   = 1;
            $leave->status      = 'Forwarded';
            $leave->remarks     = $request->remarks;
            $leave->save();
            $message            = 'Leave forwarded';
            $link               = route('hrLeaveList');
            $user               = User::where('email','<>','martha.folkes@theknowledgeacademy.com')->whereHas('roles',function($query){
                                        $query->where('name','hr');
                                    })->get();

            $email              = $user->pluck('email','email')->toArray();
            $notificationReceivers  = $user->pluck('id','id')->toArray();
            send_notification($notificationReceivers,'Leave forwarded by '.auth()->user()->employee->name,$link,'leave');
            $data['leave']      = $leave;
            $data['link']       = $link;
            $subject            = "Leave Forwarded by ". auth()->user()->name;
            send_email("email.action", $data, $subject, $message,$email,null);

            return $message;
        }
        elseif($request->action=='approve')
        {
            $leave->is_approved = 1;
            $leave->status      = 'Approved';
            $message            = 'Leave approved';
            send_notification($notificationReceivers,'Leave approved',$link,'leave');
            $data['link']       = $link;
            $data['message']    = $message;
            $subject            = "Leave Approved";
            $email              = User::find($notificationReceivers)->first()->email;
            $cc=[];
            if($leave->forwarded)
            {
            $cc                 = $leave->employee->department->deptManager->office_email;
            $cc                 = Arr::wrap($cc);
            }
            send_email("email.action", $data, $subject, $message,$email,$file = null,$cc);
        }
        else{
            $message            = 'Leave rejected';
            $leave->is_approved = 0;
            $leave->status      = 'Rejected';
            send_notification($notificationReceivers,'Leave rejected',$link,'leave');
            $data['link']       = $link;
            $data['message']    = $message;
            $subject            = "Leave Rejected";
            $email              = User::find($notificationReceivers)->first()->email;
            $cc=[];
            if($leave->forwarded)
            {
            $cc                 = $leave->employee->department->deptManager->office_email;
            $cc                 = Arr::wrap($cc);
            }
            send_email("email.action", $data, $subject, $message,$email,$file = null,$cc);
        }
        $leave->action_by   = auth()->user()->employee->id;
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
        $this->authorize('hrUpdateEmployee',new Employee());
        $data['leave_nature']   =   Leave::pluck('leave_nature','leave_nature')->toArray();
        $data['leave_type']     =   Leave::pluck('leave_type','leave_type')->toArray();
        $employeeNames          =   Employee::withoutGlobalScope('is_active');
        $data['department']     =   Department::pluck('name','id')->toArray();
        $leaves                 = Leave::with('employee.department')->where('status','<>','Pending');

        $leaves         = $this->leaveSearch($request, $leaves);

        if(request()->has('leave_nature'))
        {
           $leaves      = $leaves->where('leave_nature',$request->leave_nature);
        }
        if(request()->has('leave_type'))
        {
           $leaves      = $leaves->where('leave_type',$request->leave_type);
        }
        if(request()->has('employee_id'))
        {
           $leaves      = $leaves->where('employee_id',$request->employee_id);
        }

        if(request()->has('department_id'))
        {
            $leaves = $leaves->whereHas('employee',function($query){
                $query->where('department_id',request()->department_id);
            });
            $employeeNames  = $employeeNames->where('department_id',$request->department_id);

        }
        $data['leaves']         = $leaves->orderBy('from_date','desc')->paginate(10);
        $data['submitRoute']    = 'hrLeaveHistoryCancel';
        $data['employee']       = $employeeNames->pluck('name','id')->toArray();

        return view('hr.leaveHistory',$data);
    }

    public function managerLeaveHistory(Request $request)
    {
        $this->authorize('managerLeaveList',new Leave());
        $departmentIds              =  auth()->user()->employee->managerDepartments->pluck('id','id')->toArray();
        $data['departmentCount']    = (count($departmentIds)>1) ? true :false;

        $leaves         = Leave::with('employee.department');

        if(empty($departmentIds))
        {
            $leaves         =   $leaves->whereHas('employee',function($query){
                                    $query->where('department_id',auth()->user()->employee->department_id);
                                });
        }
        else{
            $leaves         =   $leaves->whereHas('employee',function($query) use($departmentIds){
                                            $query->whereIn('department_id',$departmentIds);
                                        });
        }

        $leaves         = $this->leaveSearch($request, $leaves);
        $data['leaves'] = $leaves->orderBy('from_date','desc')->get();

        return view('manager.leaveHistory',$data);
    }

    public function alterLeave(LeaveRequest $request)
    {
        $leave  = Leave::find($request->id);

        $leaveExists= $this->leaveExists($request,$leave->employee_id);

        $leaveExists    = $leaveExists->where('id','<>',$leave->id)->exists();

        if($leaveExists)
        {
            return 'Leave Already Exists';
        }

        if($request->action=='cancel')
        {
            $leave->update(['status'=>'Cancelled']);
            $message    = 'Leave Cancelled';
        }
        else{
            $timing = null;
            switch($request->leave_type)
            {
                case 'Full day':
                    $timing = '9:00-18:00';
                    break;
                case 'Half day(First half)':
                    $timing = '9:00-14:00';
                    break;
                case 'Half day(Second half)':
                    $timing = '14:00-18:00';
                    break;
            }
            $leave->leave_type  = $request->leave_type;
            $leave->timing      = $timing;
            $leave->from_date   = $request->from_date;
            $leave->to_date     = $request->to_date;

            $leave->update();
            $message    = 'Leave Updated';
        }

        $this->leaveProcess($leave,$message);

        return $message;
    }


    public function export(Request $request)
    {

        ini_set('max_execution_time', -1);
        return Excel::download(new LeaveExport($request), 'leave.xlsx');
    }

    function hrLeaveHistoryCancel(Request $request)
    {
        $leave=Leave::find($request->id);
        $leave->update(['status'=>'Cancelled']);
        $message    = 'Leave Cancelled';
        $this->leaveProcess($leave,$message);
        return back()->with('success','Leave cancelled');
    }

    private function leaveProcess($leave,$message)
    {
        $email      = $leave->employee->office_email;
        $notificationReceivers  = $leave->employee->user_id;
        $link       = route('leaveList');

        send_notification($notificationReceivers,$message." by ".auth()->user()->employee->name,$link,'leave');

        $data['leave']  = $leave;
        $data['link']   = $link;
        $subject        = $message." by ". auth()->user()->name;

        send_email("email.action", $data, $subject, $message,$email,null);
    }

    public function cancelLeave(Request $request)
    {
        $leave  = Leave::find($request->leave_id);

        $leave->update(['status'=>'Cancelled']);
        $department             = $leave->employee->department;
        $manager                = $department->managerDetails();
        $notificationReceivers  = [$manager->user->id];
        $email                  = [$manager->office_email];
        $link                   = route('leaveList');
        $data['leave']          = $leave;
        $data['link']           = $link;
        $message                = "Leave Cancelled";
        $subject                = "Leave Cancelled by ". auth()->user()->name;



        if(auth()->user()->hasRole('manager'))
        {
            // $link=route('hrLeaveList');
            $user   = User::where('email','<>','martha.folkes@theknowledgeacademy.com')->whereHas('roles',function($query){
                            $query->where('name','hr');
                        })->get();
            $email  = $user->pluck('email')->toArray();
            $notificationReceivers  = $user->pluck('id')->toArray();
        }



        send_notification($notificationReceivers,'Leave Cancelled by '.auth()->user()->employee->name,$link,'leave');
        send_email("email.action", $data, $subject, $message,$email,null);
    }


    private function leaveExists($request,$employee_id)
    {
        return Leave::where('employee_id',$employee_id)->whereNotIn('status',['Rejected','Cancelled'])
                            ->where(function($subQuery) use ($request){

                            $subQuery->where(function($query1) use($request)
                            {

                                $query1->where('from_Date','<=',$request->from_date)->where('to_Date','>=',$request->from_date);

                            })->orWhere(function($query2) use($request){

                                $query2->whereBetween('from_Date',[$request->from_date,$request->to_date]);
                            });
                        });
    }
    private function managerLeaveCheck($request,$employee_id)
    {
       return Leave::where('employee_id',$employee_id)->whereNotIn('status',['Rejected','Cancelled'])
                            ->where(function($subQuery) use ($request){

                            $subQuery->where(function($query1) use($request)
                            {

                                $query1->where('from_Date','<=',$request->from_date)->where('to_Date','>=',$request->from_date);

                            });
                        });
    }
    private function leaveSearch(Request $request, $leaves)
    {
        if(!empty(request()->dateFrom) || !empty(request()->dateTo))
        {
            $leaves->where(function($subQuery) use($request){
                $subQuery->where(function($query1) use($request)
                {
                    $query1->where('from_Date','<=',$request->dateFrom)->where('to_Date','>=',$request->dateFrom);
                })->orWhere(function($query2) use($request){

                        $query2->whereBetween('from_Date',[$request->dateFrom,$request->dateTo]);
                });
            });
        }
        else{
            $leaves->whereYear('from_date','>=',Carbon::now()->year);
        }

        return $leaves;

    }

}
