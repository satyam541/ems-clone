<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Department;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use App\Exports\LeaveBalanceExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\LeaveBalanceRequest;

class LeaveBalanceController extends Controller
{
    public function dashboard(Request $request,$export=false)
    {

        $this->authorize('hrEmployeeList', new Employee());

        $date                   =   empty($request->month) ? Carbon::now() :Carbon::createFromFormat('Y-m',$request->month);
        $leaveBalances          =   LeaveBalance::whereMonth('month',$date)->whereYear('month',$date)->with('user.employee.department')->whereHas('user',function($user){
            $user->where('user_type','Employee')->where('is_active','1');
        });



        if(!empty($request->user_id))
        {
            $leaveBalances->where('user_id',$request->user_id);
        }
        if(!empty($request->department_id))
        {
            $leaveBalances->whereHas('user.employee',function($user){
                $user->where('department_id',request()->department_id);
            });
        }

        // $data['users']                  =   User::has('employee')->pluck('name','id')->toArray();
        $data['employeeDepartments']  	=   User::where('user_type','Employee')->where('is_active','1')
        ->select('id','name')->with('employee.department')->get()->groupBy('employee.department.name');
        $data['departments']            =   Department::pluck('name','id')->toArray();
        $leaveBalances                  =   $leaveBalances;
        if($export)
        {

            return $leaveBalances->get();
        }
        $start                  =   Carbon::parse($date)->startOfMonth()->format('Y-m-d');
        $end                    =   Carbon::parse($date)->endOfMonth()->format('Y-m-d');
        $data['leaveBalances']  =   $leaveBalances->get();
        $data['start']          =   $start;
        $data['end']            =   $end;
        $data['date']           =   $date;

        return view('leave.balanceDashboard',$data);

    }

    public function edit($id,Request $request)
    {
        if(!empty($request->user_id))
        {
            $month                                  =   Carbon::createFromFormat('Y-m-d',$request->month);
            $leaveBalance                           =   LeaveBalance::where('user_id',$request->user_id)->whereMonth('month',$month)->first();
        }
        else
        {
        $leaveBalance                           =   LeaveBalance::findOrFail($id);
        }
        $data['submitRoute']                    =   ['leaveBalanceUpdate',$id];
        $data['method']                         =   'POST';
        $data['user']                           = User::where('id',$leaveBalance->user_id)->pluck('name','name')->first();
        $data['leaveBalance']                   =  $leaveBalance;
        $data['employeeDepartments']            =   User::with('employee.department')->where('is_active', '1')->where('user_type', 'Employee')
                                                    ->select('id','name')->get()->groupBy('employee.department.name');
        return view('leave.updateLeaveBalance',$data);
    }

    public function update(LeaveBalanceRequest $request,$id)
    {
        $leaveBalance                           = LeaveBalance::findOrFail($id);
        $leaveBalance->balance                  = $request->balance;
        $leaveBalance->absent                   = $request->absent;
        $leaveBalance->taken_leaves             = $request->taken_leaves;
        $leaveBalance->deduction                = $request->deduction;
        $leaveBalance->prev_month_deduction     = $request->prev_month_deduction;
        $leaveBalance->next_month_deduction     = $request->next_month_deduction;
        $leaveBalance->update();

        return redirect(route('leaveBalanceDashboard'))->with('success','Success ');
    }

    public function myBalance(Request $request)
    {
        if(!auth()->user()->hasRole('admin'))
        {
            abort('403');
        }

        $month                  =      empty($request->month) ? now() : Carbon::createFromFormat('Y-m',$request->month);
        $data['myBalance']      =      LeaveBalance::with('user')->where('user_id',auth()->user()->id)->whereMonth('month',$month)->first();
        return view('leave.myBalance',$data);

    }

    public function export(Request $request)
    {
        $leaveBalances   =   $this->dashboard($request,true);
        //  dd($leaveBalances);
        return Excel::download(new LeaveBalanceExport($leaveBalances),'leaveBalance.xlsx');
    }
}
