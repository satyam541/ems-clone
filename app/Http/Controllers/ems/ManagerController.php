<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Entity;
use App\Models\Role;
use App\Models\Equipment;
use App\Models\Department;
use Illuminate\Support\Arr;
use App\Models\Attendance;

class ManagerController extends Controller
{
    public function view()
    {
        $this->authorize('managerEmployeeList',new Employee());
        $department_ids             = auth()->user()->employee->managerDepartments->pluck('id','name')->toArray();
        $employees                  = Employee::query();

        if(empty($department_ids))
        {
            $department_names       = auth()->user()->employee->department->name;
            $employees              = $employees->where('department_id',auth()->user()->employee->department_id);
        }
        else{
            $department_names       = implode(", ",array_keys($department_ids));
            $employees              = $employees->whereIn('department_id',$department_ids);
        }
        $data['employees']          = $employees->get();
        $data['department_names']   = $department_names;
        
        return view('manager.employee',$data);
    }

    public function attendance()
    {
        $this->authorize('managerView',new Attendance());
        $data['months']     = [1=>'January','Febuary','March','April','May','June','July','August','September','October','November','December'];
        $department         = auth()->user()->employee->department;
        $employees          = $department->employees->pluck('name','id')->toArray(); 
        $data['department'] = $department;
        $data['employees']  = json_encode($employees,JSON_HEX_APOS);

        return view('manager.attendancelist',$data);
    }

    public function attendanceList(Request $request)
    {
        $pageIndex      =  $request->pageIndex;
        $pageSize       = $request->pageSize;
        $department_id  = auth()->user()->employee->department_id;
        $employees      = Employee::where('department_id',$department_id);

        if($request->id != '0')
        {   
            $employees  = $employees->where('id',$request->id);
        }

        $data['itemsCount'] = $employees->count();
        $employees          = $employees->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get();

        if(!empty($request->month))
        {
            $month      = $request->month;
        
            $employees  = $employees->map(function ($employee) use($month)  {
        
                $data['id']                   =   $employee->id;
                $data['total_absent']         =   $employee->attendances->where('attendance_month',$month)->where('status','absent')->count();
                $data['total_present']        =   $employee->attendances->where('attendance_month',$month)->where('status','present')->count();
                return $data;
            });
        }       
        $data['data']   = $employees;
        
        return json_encode($data);
    }
    

        // ---***** Manager's Department wise Equipment list *****------
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function views()
    { 
        $this->authorize('managerEquipmentList',new Equipment());
        $entity                     =   Entity::all()->pluck('name','name')->toArray();
        $manufacturer               =   Equipment::all()->pluck('manufacturer','manufacturer')->unique()->toArray();
        $new['All']                 =   'All';
        $entity                     =   array_merge($new,$entity);
        $department                 =   auth()->user()->employee->department;
        $employees                  =   $department->employees->pluck('name','id')->toArray(); 
        $manufacturer               =   array_merge($new,$manufacturer);
        $data['entity']             =   json_encode($entity,JSON_HEX_APOS);
        $data['manufacturer']       =   json_encode($manufacturer,JSON_HEX_APOS);
        $data['status']             =   json_encode(['All'=>'All','Working'=>'Working', 'Not Working'=>'Not Working']);
        $data['employees']          =   json_encode($employees,JSON_HEX_APOS);

        return view('manager.managerDepartmentEquipment', $data);
    }

    public function lists(Request $request)
    {
        $pageIndex      =   $request->pageIndex;
        $pageSize       =   $request->pageSize;
        $equipment      =   Equipment::query();

        if($request->manufacturer != 'All')
        {
            $equipment->where('manufacturer',$request->manufacturer);
        }
        if($request->entity['name'] != 'All')
        {
            $entity = Entity::where('name',$request->entity['name'])->first();  
            $equipment->where('entity_id',$entity->id);
        }
        if(!(empty($request->alloted_no)))
        {
            $equipment->where('alloted_no',$request->alloted_no);
        }
        if($request->isWorking != 'All')
        {
            $isWorking  = 0;
            if($request->isWorking=='true')
            {
                $isWorking  = 1;
            }
            $equipment->where('isWorking',$isWorking);
        }
        
        if(!(empty($request->sortField)&&empty($request->sortOrder)))
        {
            if($request->sortField == 'entity.name')
            {
                $request->sortField = 'entity_id';
            }
            $equipment->orderBy($request->sortField,$request->sortOrder);
        }
        if($request->employee_id != '0')
        {
            $employee_id = $request->employee_id;
            $equipment->whereHas('employee', function($query) use($employee_id){
                return $query->where('employee_id',$employee_id);
            });
        }
          $department_id    = auth()->user()->employee->department_id; 
          $equipment->whereHas('employee', function($query) use($department_id){
            return $query->where('department_id',$department_id);
        });
        
        $data['itemsCount'] = $equipment->count();
        $data['data']       = $equipment->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get()->load('employee','entity','specifications','repairs');
        
        return json_encode($data);
    }
    
}