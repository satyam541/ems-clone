<?php

use App\Models\ActivityLog;
use App\Http\Controllers\ems\NotificationController;
use App\Models\Qualification;
use App\Models\EmployeeProfileDraft;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Ticket;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\Quotation;
if (!function_exists('saveLogs')) {
    /**
     * stores the activity log of users.
     */
    function saveLogs($action,$module)
    {
        $user_id = auth()->user()->id ?? 1;
        $activity=new ActivityLog();
        $activity->user_id=$user_id;
        $activity->action=$action;
        $module->activity()->save($activity);
        $activity->save();
    }

}
if (!function_exists('send_notification')) {
  
    function send_notification($user_ids,$message,$link,$type=null)
    {
        NotificationController::send_notification($user_ids,$message,$link,$type);
    }

}

if (!function_exists('send_email')) {
    
    function send_email($template, $data, $subject, $message, $to, $file = null, $cc=[], $bcc=[])
    {

        // dd($data,$template,$data,$to,$message);
        $to     =   Arr::wrap($to);
        $to     =   array_unique($to);
        $cc     =   array_unique($cc);
        $bcc    =   array_unique($bcc);
        $data['Subject']=$subject;
        $data['emailMessage']=$message;
        // dd($data,$message);
        Mail::send($template, $data, function ($message) use($to, $subject, $cc ,$bcc, $file) {
        $message->subject($subject);        
        if(!empty($file))
        {
          $message->attach(storage_path('app/'.$file));
        }
        if(count($to) > 0) {
            $message->to($to);
        }
        if(count($cc) > 0){
            $message->cc($cc);
        }
        });
        if (!empty(Mail::failures())) {
            return false;
        }
        return true;
    }
}

if (!function_exists('getQualificationName')) {
    function getQualificationName($id)
    {
        $qualificaton =  Qualification::find($id);
        if(!empty($qualificaton)){
            return $qualificaton->name;
        }
        else{
            return 'N/A';
        }

    }
}
if(!function_exists('draft_check'))
{
    function draft_check($employee_id,$fieldName)
    {
        return EmployeeProfileDraft::where(['employee_id'=>$employee_id,'field_name'=>$fieldName])->whereNull(['is_approved','approved_by'])->exists();
    }

}

if (!function_exists('getFormatedDate')) {
    function getFormatedDate($date)
    {
        if (!empty($date)) {
            return Carbon::parse($date)->format('d/m/Y');
        }else{
            return 'N/A';
        }
    }
}

if (!function_exists('getFormatedTime')) {
    function getFormatedTime($time)
    {
        if(Str::contains($time,'-'))
        {
            return Carbon::parse(Str::before($time,'-'))->format('h:i').'-'.Carbon::parse(Str::after($time,'-'))->format('h:i');    
        }
        return Carbon::parse($time)->format('h:i');
    }
}

if (!function_exists('getFormatedDateTime')) {
    function getFormatedDateTime($datetime)
    {
        if(!empty($datetime)){
            return Carbon::parse($datetime)->format('d M Y H:i A');
        }
        return "N/A";
        
    }
}

if (!function_exists('commonCount'))
{
    function commonCount()
    {
        $openedTickets                   =   Ticket::whereNotIn('status',['Sorted','Closed']);
        if(!auth()->user()->hasRole('admin'))
        {
        $openedTickets                   =   $openedTickets->whereHas('ticketCategory',function($query){
                                                $query->where('type',auth()->user()->employee->department->name);
                                                }); 
        }
        $data['openedTickets']          =   $openedTickets->count();

        $manager_ids                    =   Employee::whereHas('managerDepartments')->pluck('id','id')->toArray();
        $departmentIds                  =   auth()->user()->employee->managerDepartments->pluck('id','id')->toArray();
        $data['departmentLeaves']       =   Leave::whereDate('from_date','>=',Carbon::today())->where('status','Pending')
                                                ->where('employee_id','<>',auth()->user()->employee->id)->where(['forwarded'=>'0'])->
                                                whereHas('employee',function($query) use($departmentIds){
                                                    $query->whereIn('department_id',$departmentIds);
                                                })->count();

        $data['forwardedLeaves']        =   Leave::where('forwarded','1')
                                                ->where('status','Pending')->count();
        
        $data['managerLeaves']          =   Leave::whereIn('employee_id', $manager_ids)
                                                ->where('status','Pending')->count();

        $data['drafts']                 =   EmployeeProfileDraft::with(['employee.department', 'approver'])->whereNull('approved_by')
                                                ->groupBy('employee_id')->get()->count();

        $data['pendingProfiles']        =   Employee::where('is_active','1')->with(['department','profileReminder'=>function($query){
                                                    return $query->whereDay('created_at',Carbon::now()->day)->where('sent',1);
                                                }])->whereDoesntHave('documents')->orWhereDoesntHave('employeeEmergencyContact')
                                                ->orWhereNull('profile_pic')->count();
                                                
        $data['quotations']             =   Quotation::where('status','sent')->whereHas('quotationDetails',function($query){
                                                    $query->whereNull('is_approved');
                                                })->count();
        return $data;
    }
}
