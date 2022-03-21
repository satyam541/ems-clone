<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquipmentRequests;
use App\User;
use App\Models\Entity;
use App\Http\Requests\EntityRequest;
use App\Http\Requests\EquipmentAssignRequest;
use App\Http\Requests\EquipmentRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Equipment;

class EquipmentRequestController extends Controller
{
    public function request($request_id=null)
    {
      $data['entity']     = Entity::pluck('name', 'id');
      $department         = auth()->user()->employee->department->name;
      $data['department'] = $department;

      if(empty($request_id))
      {
        $data['entity_request'] = new EquipmentRequests();
        $data['submitRoute']    = 'insertEntityRequest';
        $find   = EquipmentRequests::where('request_id', 'like', $department.'%')->get()->last();

        if(empty($find))
        {
          $request_number     = $department."1";
        }
        else{
          $request_number     = $department . ((int)str_replace($department, "", $find->request_id) + 1);
        }
        $data['requestnumber'] = $request_number;
      }
      else{
        $equipment_requests   = EquipmentRequests::with(['manager', 'entity', 'employee'])->whereDoesNtHave('assigner')->where('request_id', $request_id)->get();
        
        if($equipment_requests->isNotEmpty())
        {
          $data['equipment_requests'] = $equipment_requests->groupBy('request_id');
          $data['submitRoute']        = 'alotEquipmentByRequest';
          $equipments                 = Equipment::with('entity')->whereNotNull('alloted_no')->whereNull('employee_id')->where('entity_id', $equipment_requests->first()->entity_id)->get();
          $equipmentAvailable         = array();
          foreach($equipments as $equipment)
          {
            $equipmentAvailable[$equipment->alloted_no]   = $equipment->manufacturer . " (".$equipment->alloted_no.")";
          }
          $data['equipmentAvailable']   = $equipmentAvailable;
        }
        else{
           return  redirect()->route('viewEntityRequest');
        }
      }
      return view('entityRequest.entityRequestForm',$data); 
    }

    public function requestEquipmentAlot(Request $request)
    {
     
      $assigner                           = auth()->user()->employee->id;
      $equipment_request                  = EquipmentRequests::find($request->id);
      $equipment                          = Equipment::where('alloted_no', $request->alloted_id)->first();
      $assign                             = new EquipmentController();
      $employee                           = Employee::find($equipment_request->employee_id);
      $assigned                           = $assign->allotEquipment($employee, $equipment);
      $equipment_request->action_taken_by = $assigner;
      $equipment_request->status          = "approved";
      $equipment_request->save();
      $subject                  = "Equipment Alloted";
      $message                  = "Equipment is assigned to ".$employee->name;
      $data['equipment']        = $equipment;
    
      send_email("email.equipmentAlot", $data, $subject, $message, array($equipment_request->manager->office_email));
    
      $data['status']   = "success";
      return json_encode($data);
    }

    public function getEmployees(Entity $entity)
    {
      $employees = Employee::whereDoesntHave('equipments', function($query) use($entity){
                                $query->where('entity_id', $entity->id);
                              })->where('department_id', auth()->user()->employee->department_id)->pluck('name', 'id');
      return json_encode($employees);
    }

    public function insert(EquipmentAssignRequest $request)
    {
      $this->authorize('managerEntityRequestList',new EquipmentRequests());
  
      foreach($request->employee_id  as $employee_id)
      {
      $equipment_request                      = new EquipmentRequests();
      $equipment_request->employee_id         = $employee_id;
      $equipment_request->comment             = $request->comment;
      $equipment_request->entity_id           = $request->entity_id;
      $equipment_request->requested_by        = $request->requested_by;
      $equipment_request->request_id          = $request->request_id;
      $equipment_request->save();
      }
        //send notification to it
      $entity   = Entity::find($request->entity_id);
      $employee = Employee::find($request->requested_by);
  
      $users    = User::havingRole('IT'); 
      $message  = 'Requested Entity '.$entity->name .'and Request By'.$employee->name;
      $route    = ['name'=>'allEntityRequest','parameter'=>''];
      send_notification($users, $message, $route);
      return  back()->with('success','Entity Request added  successfully');
    }

    public function view()
    {  
      $equipment_request      =   new EquipmentRequests();
      $this->authorize('itEntityRequestList', $equipment_request);
      $entity                 =  [0 => "All"]+Entity::all()->pluck('name','id')->toArray();
      $data['isAssigner']     =  auth()->user()->can('itEntityRequestList', $equipment_request);
      $data['entity']         =  json_encode($entity);
      $departments            =  array();
      if($data['isAssigner'])
      {
        $departments = Department::pluck('name', 'id')->toArray();
      }
      $data['departments'] = $departments;
      return view('entityRequest.entityRequestList',$data);
    }

    public function list(Request $request)
    {   
      $equipment_request  =   new EquipmentRequests();
      $this->authorize('managerEntityRequestList', $equipment_request);
      $pageIndex          =   $request->pageIndex;
      $pageSize           =   $request->pageSize;
      $equipmentRequests  =   EquipmentRequests::with('manager')->whereDoesntHave('assigner')->where('status','pending');
      if(auth()->user()->can('itEntityRequestList', $equipment_request));
      elseif(auth()->user()->can('managerEntityRequestList', $equipment_request))
      {
        $equipmentRequests->where('requested_by', auth()->user()->employee->id);
      }
      if(!empty($request->entity_id) && $request->entity_id != 0)
      {
        $equipmentRequests->where('entity_id', $request->entity_id);
      }
      $finalResult  = collect();
      $equipmentRequests->get()->groupBy('request_id')->map(function($equipment_requests, $key) use($finalResult)
                                                        {
                                                            $result                       = array();
                                                            $result['request_id']         = $key;
                                                            $result['entity_id']          = $equipment_requests->first()->entity_id;
                                                            $result['requested_quantity'] = $equipment_requests->count();
                                                            $result['department']         = $equipment_requests->first()->manager->department->name;
                                                            $result['comment']            = $equipment_requests->first()->comment;
                                                            $result['applied_on']         = $equipment_requests->first()->created_at->format('d-m-Y');
                                                            $finalResult->push($result);
                                                        });
      $data['data']       = $finalResult;
      $data['itemsCount'] = $finalResult->count();
      return json_encode($data);
    }

    public function allList(Request $request)
    {   
      $this->authorize('itEntityRequestList',new EquipmentRequests());
      $pageIndex      =   $request->pageIndex;
      $pageSize       =   $request->pageSize;
      $entity         =   EquipmentRequests::query();
      if($request->status!='all')
      {
        $entity = $entity->where('status',$request->status);
      }
      $data['itemsCount'] = $entity->count();
      $data['data']       = $entity->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get();
      return json_encode($data);
    }

    public function delete(EntityRequests $entity)
    {
      $this->authorize('itEntityRequestList',$entity);
      //send notification to IT
      $entity_name  = Entity::find($entity->id);
      $users        = User::havingRole('IT'); 
      $message      = 'Entity Request '.$entity_name->name.' Removed By Requester';
      $route        = ['name'=>'deleteEntityRequest','parameter'=>$entity->id];
      send_notification($users, $message, $route);
      $entity->delete();
    }

    public function update(EntityRequest $request,EquipmentRequests $entity)
    {
      $this->authorize('managerEntityRequestList',$entity);
      $entity->entity_id          = $request->entity_id;
      $entity->requested_quantity = $request->requested_quantity;
      $entity->manager_comment    = $request->manager_comment;
      $entity->save();
        //send notification to IT
      $entity_name  = Entity::find($entity->id);
      $users        = User::havingRole('IT'); 
      $message      = 'Entity Request '.$entity_name->name.' Updated By Requester';
      $route        = ['name'=>'allEntityRequest','parameter'=>''];
      send_notification($users, $message, $route);
      return json_encode($entity);
    }

    public function allotEquipment(Request $request,EquipmentRequests $entity)
    {
      $this->authorize('it',$entity);
      $entity->status   = $request->status;
      if($request->approved_quantity == $request->requested_quantity)
      {
        $entity->status='approved';
      }
      $entity->approved_quantity  = $request->approved_quantity;
      $entity->remarks            = $request->remarks;
      $entity->save();
      //send notification to IT
      $user_ids   = $entity->employee_id;
      $message    = 'Requested Entity '.$entity->status.'. '.$entity->status.' quantity: '.$entity->approved_quantity;
      $route      = ['name'=>'viewEntityRequest','parameter'=>''];
      send_notification($user_ids, $message, $route);
      return json_encode($entity);
    }

    public function viewAll()
    {
      $this->authorize('itEntityRequestList', new EquipmentRequests());
      $status           = json_encode(['all'=>'all','pending'=>'pending','approved'=>'approved','rejected'=>'rejected']);
      $entity           = Entity::all()->pluck('name','id');
      $data['status']   = $status;
      $data['entity']   = $entity;
      
      return view('entityRequest.allEntityRequestList',$data);
    }
}
