<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeEquipment;
use App\Models\Entity;
use App\Models\Equipment;
use App\Models\Repair;
use App\Models\Specification;
use Illuminate\Http\Request;
use App\Imports\EquipmentImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\EquipmentRequest;
use App\Exports\EquipmentExport;
use App\Models\Ticket;
use Illuminate\Support\Arr;
use App\User;

class EquipmentController extends Controller
{
    public function view()
    {
        $this->authorize('it',new Equipment());
        $entities                    =   Entity::all();
        $entityArr                  =   $entities->pluck('name','name')->toArray();
        $new['All']                 =   'All';
        // $entity                     =   array_merge($new,$entityArr);
        $entity                     =   ['All'=>'All']+$entityArr;   
        $data['entity']             =   json_encode($entity,JSON_HEX_APOS);
        $manufacturer               =   Equipment::all()->pluck('manufacturer','manufacturer')->unique()->toArray();
        $employee                   =   Employee::all()->pluck('name','id');
        $manufacturer               =   ['All'=>'All']+$manufacturer;
        $data['manufacturer']       =   json_encode($manufacturer,JSON_HEX_APOS);
        $data['status']             =   json_encode(['All'=>'All','true'=>'Working', 'false'=>'Not Working']);
        $data['employee']           =   json_encode($employee,JSON_HEX_APOS);
        $data['entities']           =   $entities;
        $data['availability']       =   json_encode(['0'=>'All','1'=>'Assigned', '2'=>'Un-Assigned']);
        return view('equipment.equipment', $data);
    }

    // trash view
    public function viewTrash()
    {
        $this->authorize('trash',new User());
        $entity                     =   Entity::all()->pluck('name','name')->toArray();
        $manufacturer               =   Equipment::onlyTrashed()->pluck('manufacturer','manufacturer')->toArray();
        $new['All']                 =   'All';
        $employee                   =   Employee::all()->pluck('name','id');
        $entity                     =   array_merge($new,$entity);
        $manufacturer               =   array_merge($new,$manufacturer);
        $data['entity']             =   json_encode($entity,JSON_HEX_APOS);
        $data['manufacturer']       =   json_encode($manufacturer,JSON_HEX_APOS);
        $data['status']             =   json_encode(['All'=>'All','0'=>'Working', '1'=>'Not Working']);
        $data['employee']           =   json_encode($employee,JSON_HEX_APOS);
        return view('trash.equipmentTrashList', $data);
    }


    public function list(Request $request)
    {
        $this->authorize('it',new Equipment());
        $pageIndex      =   $request->pageIndex;
        $pageSize       =   $request->pageSize;
        $equipment      =   Equipment::with(['employee' => function ($q) {
                                $q->withoutGlobalScopes();
                            }]);
        if($request->has('availability') && $request->availability != 0)
        {
            if($request->availability == 2)
            {
                $equipment->whereDoesntHave('employee');
            }
            else{
                $equipment->whereHas('employee');
            }
        }
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
            $equipment->where('alloted_no','like','%'.$request->alloted_no.'%');
        }
        if($request->isWorking != 'All')
        {
            $isWorking=0;
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
        $data['itemsCount'] = $equipment->count();
        $data['data']       = $equipment->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get()->load('entity','specifications','repairs');
        return json_encode($data);
    }

    //   trash list
    public function trashList(Request $request)
    {
        $this->authorize('trash',new User());
        $pageIndex      =   $request->pageIndex;
        $pageSize       =   $request->pageSize;
        $equipment      =   Equipment::onlyTrashed();
        if($request->entity['name'] != 'All')
        {
            $entity = Entity::where('name',$request->entity['name'])->first();
            $equipment->where('entity_id',$entity->id);
        }
        if(!(empty($request->alloted_no)))
        {
            $equipment->where('alloted_no',$request->alloted_no);
        }
        if(!(empty($request->sortField)&&empty($request->sortOrder)))
        {
            if($request->sortField == 'entity.name')
            {
                $request->sortField = 'entity_id';
            }
            $equipment->orderBy($request->sortField,$request->sortOrder);
        }

        $data['itemsCount'] = $equipment->count();
        $data['data']       = $equipment->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get()->load('entity','employee','specifications','repairs');
        return json_encode($data);
    }
    // end of trash list

    // force delete
    public function forceDelete(Request $request)
    {
        $this->authorize('destroy',new Equipment());
        Equipment::where('id',$request->equipment_id)->forceDelete();
        return "Equipment deleted permanently";
    }

    //restore
    public function restore(Request $request)
    {
        $this->authorize('restore',new Equipment());
        Equipment::where('id',$request->equipment_id)->restore();
        return "Equipment Restored Successfully";
    }

    public function checkAllotedNumber(Request $request)
    {
        $allotedno = $request->allotedno;
        $equipment = Equipment::where('alloted_no', $allotedno)->first();
        if(empty($equipment))
        {
            return json_encode(['alloted'=>false]);
        }
        if($equipment->id == $request->id)
        {
            return json_encode(["status"=>false]);
        }
        return json_encode(['alloted'=>true]);
    }
    public function checkCurrentEquipment(Employee $employee, Entity $entity, Request $request)
    {
        $equipment = Equipment::with(['entity','employee'])->where('employee_id',$employee->id)
                                    ->where('entity_id',$entity->id)->first();
        if(empty($equipment->id))
        {
            return json_encode(["status"=>false]);
        }
        return json_encode(["status"=>true,"equipment"=>$equipment]);
    }
    public function addEquipment()
    {
        $this->authorize('it',new Equipment());
        $data['equipment']         = new Equipment();
        $data['submitRoute']       = array('insertEquipment');
        $data['entity']            = Entity::all()->pluck('name','id');
        $data['breadcrumb']        = "Insert";
        
      

        return view('equipment.equipmentForm',$data);
    }
    public function editEquipment(Equipment $equipment)
    {
        $this->authorize('it',new Equipment());
        $data['equipment']                    =     $equipment;
        $data['submitRoute']                  =     array('updateEquipment',$equipment->id);
        $data['entity']                       =     Entity::all()->pluck('name','id');
        $employees                            =     Employee::with('department')->get();
        $finalArray                           =     array();
        foreach($employees as $employee)
        {
            $finalArray[$employee->id]        =     $employee->name . "( " . $employee->department->name ." )";
        }
        $data['employees']                    =     $finalArray;
        $data['breadcrumb']                   =     "Update";
        $data['specifications']               =      $data['equipment']->specifications;
        $data['repairs']                      =      $data['equipment']->repairs;
        $data['problems']                     =      $data['equipment']->problems;
        return view('equipment.equipmentForm',$data);
    }
    public function insertEquipment(EquipmentRequest $request)
    {
        $this->authorize('it',new Equipment());
        $quantity = $request->quantity;
        while($quantity>0)
        {
            $equipment                           =       new Equipment();
            $equipment->entity_id                =       $request->entity_id;
            $equipment->manufacturer             =       $request->manufacturer;
            $equipment->buy_date                 =       $request->buy_date;
            $equipment->save();
            if($request->has('specifications'))
            {
                foreach($request->specifications as $specification)
                {
                    $spec = new Specification();
                    $spec->equipment_id   =   $equipment->id;
                    $spec->name                   =   $specification['name'];
                    $spec->description            =   $specification['description'];
                    $spec->save();
                }
            }
            $quantity--;
        }
        return redirect()->back()->with('success', 'Equipments added to List');
    }
    public function updateEquipment(EquipmentRequest $request, Equipment $equipment)
    {
        $this->authorize('it',new Equipment());
        $equipment->entity_id           =   $request->entity_id;
        $equipment->alloted_no          =   $request->alloted_no;
        $equipment->manufacturer        =   $request->manufacturer;
        $equipment->buy_date            =   $request->buy_date;
        $equipment->isWorking           =   $request->isWorking;
        $equipment->save();
        if($request->has('specifications'))
        {
            foreach($request->specifications as $specification)
            {
                if(empty($specification['id']))
                {
                    $spec                       =   new Specification();
                }
                else
                {
                    $spec                       =   Specification::find($specification['id']);
                }
                $spec->equipment_id             =   $equipment->id;
                $spec->name                     =   $specification['name'];
                $spec->description              =   $specification['description'];
                $spec->save();
            }
        }
        if($request->has('repairs'))
        {
            foreach($request->repairs as $repair)
            {
                if(empty($repair['id']))
                {
                    $repair_detail              =   new Repair();
                }
                else
                {
                    $repair_detail                     =   Repair::find($repair['id']);
                }
                $repair_detail->equipment_id           =   $equipment->id;
                $repair_detail->date                   =   $repair['date'];
                $repair_detail->part                   =   $repair['part'];
                $repair_detail->cost                   =   $repair['cost'];
                $repair_detail->save();
            }
        }
        if($request->has('problems'))
        {
            foreach($request->problems as $problem)
            {
                if(empty($problem['id']))
                {
                    $problem_detail              =   new Ticket();
                }
                else
                {
                    $problem_detail              =   Ticket::find($problem['id']);
                }
                $problem_detail->equipment_alloted_no   =   $equipment->alloted_no;
                $problem_detail->name                   =   $problem['name'];
                $problem_detail->save();
            }
        }
        $message = "Updated Successfully";
        if($request->has('employee_id'))
        {
            $assign = $equipment;
            if($request->employee_id == null)
            {
                if(!empty($assign->id))
                {
                    $assign->update(['employee_id'=>null]);
                    return redirect()->back()->with('success', "Un-Assigned And Updated Successfully");
                }
            }
            $employee = Employee::find($request->employee_id);
            if(empty($employee))
            {
                return redirect()->back()->with('error', 'Employee not Found');
            }
            $unassignEquipments = optional($employee->equipments)->where('entity_id', $request->entity_id);
            foreach($unassignEquipments as $unassign)
            {
                $unassign->update(['employee_id'=>null]);
            }
            $assign->employee_id       =   $request->employee_id;
            $assign->save();
            return redirect()->back()->with('success', "Assigned And Updated Successfully");
        }
        return redirect()->back()->with('success', "Updated Successfully");

    }
    public function deleteSpecification(Request $request, Specification $specification)
    {
        $specification->delete();
        $data['success'] = true;
        return json_encode($data);
    }
    public function deleteRepair(Request $request, Repair $repair)
    {
        $repair->delete();
        $data['success'] = true;
        return json_encode($data);
    }
    public function deleteProblem(Request $request, EquipmentProblem $problem)
    {
        $problem->delete();
        $data['success'] = true;
        return json_encode($data);
    }
    public function autocomplete(Request $request)
    {
        $term = $request->term;
        $manufacturer = Equipment::where('manufacturer','LIKE',"%".$term."%")->pluck('manufacturer','manufacturer')->unique();
        return $manufacturer;
    }
    public function getEquipmentData(Request $request, Employee $employee)
    {
        $equipment          =   Equipment::whereNull('employee_id')->get();
        $data['equipment']  =   $equipment;
        return json_encode($data, JSON_HEX_APOS);
    }
    public function allotEquipment(Employee $employee, Equipment $equipment) 
    {
        $equipment->employee_id = $employee->id;
        $equipment->save();
        return json_encode($equipment, JSON_HEX_APOS);
    }
    public function importView()
    {
        return view('equipment.equipmentImport');
    }
    public function import()
    {
        Excel::import(new EquipmentImport,request()->file('file'));
        return back()->with('success','Equipment Imported  Successfully');
    }
    public function export(Request $request)
    {
        return Excel::download(new EquipmentExport($request->entity), 'equipment.xlsx');
    }

    public function deleteEquipment(Request $request){
        if($request->ajax() && $request->has('equipment'))
        {
            $equipment = Equipment::find($request->equipment);
            $equipment->delete();
            return json_encode(array('status'=>'Deleted Successfully'));
        }
        else{
            abort(403);
        }
    }
}
