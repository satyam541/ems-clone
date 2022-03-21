<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use App\Models\Employee;
use App\User;
use App\Models\Role;

class DepartmentController extends Controller
{
	public function view(Request $request)
	{
		$this->authorize('view', new Department());
		return view('department.department');
	}


	public function list(Request $request)
	{
		$pageIndex 		=  $request->pageIndex;
		$pageSize 		= $request->pageSize;
		$departments 	= Department::query();
		if (!empty($request->get('name'))) 
		{
			$departments 	= $departments->where('name', 'like', '%' . $request->get('name') . '%');
		}
		$data['itemsCount'] = $departments->count();
		$data['data'] 		= $departments->limit($pageSize)->offset(($pageIndex - 1) * $pageSize)->get();
		return json_encode($data);
	}

	public function insert(DepartmentRequest $request)
	{
		$this->authorize('insert', new Department());
		$department 				= new Department();
		$department->name 			= $request->name;
		$department->description 	= $request->description;
		$department->save();
		return json_encode($department);
	}

	public function update(DepartmentRequest $request, Department $department)
	{
		$department_id 				= 	$request->id;
		$department 				= 	Department::find($department_id);
		$this->authorize('update', $department);
		$department->name 			= 	$request->name;
		$department->description 	= 	$request->description;
		$department->save();
		return json_encode($department);
	}

	public function delete(Request $request)
	{
		$department 	= 	Department::find($request->id);
		$this->authorize('delete', $department);
		$department->delete();
		return json_encode("done");
	}

	public function employee(Request $request)
	{
		$data['departments'] 	= Department::all();
		return view('department.employee', $data);
	}

	public function employeeList(Request $request)
	{
		$pageIndex				=  	$request->pageIndex;
		$pageSize 				= 	$request->pageSize;
		$employees 				= 	Employee::query();

		if ($request->department) {
			$employees 			= 	$employees->where('department_id', $request->department);
		}
		if ($request->name) {
			$employees 			= 	$employees->where('name', 'like', '%' . $request->name . '%');
		}
		if ($request->registration_id) {
			$employees 			= 	$employees->where('registration_id', $request->registration_id);
		}
		$data['itemsCount'] 	= 	$employees->count();
		$data['data'] 			= 	$employees->with('user', 'department')->limit($pageSize)->offset(($pageIndex - 1) * $pageSize)->get();
		return json_encode($data);
	}

	public function viewTrash()
	{
		$this->authorize('trash', new User());
		return view("trash.departmentTrashedList");
	}

	public  function trashList(Request $request)
	{
		$this->authorize('trash', new User());
		$pageIndex 				=  	$request->pageIndex;
		$pageSize 				= 	$request->pageSize;
		$departments 			= 	Department::onlyTrashed()->orderBy('deleted_at', 'desc');
		$data['itemsCount'] 	= 	$departments->count();
		$data['data'] 			= 	$departments->limit($pageSize)->offset(($pageIndex - 1) * $pageSize)->get();
		return json_encode($data);
	}

	public function restore(Department $department, Request $request)
	{
		$department 			= 	Department::onlyTrashed()->find($request->department);
		$this->authorize('restore', $department);
		$department->restore();
		return "restored";
	}
	public function forcedelete(Request $request)
	{
		$department 			= 	Department::onlyTrashed()->find($request->department);
		$this->authorize('destroy', $department);
		$department->forceDelete();
		return "Destroyed";
	}

	public function departmentEmployees()
	{
		$data['departments'] 			= Department::withCount('employees')->paginate(15);
		$data['employeeDepartments']  	=  Employee::select('id','name','department_id')->get()->groupBy('department.name');
		return view('department.departmentEmployee', $data);
	}

	public function managerUpdate(Request $request)
	{
		// optional code need to be terminate
		$department     = Department::find($request->departmentId);
		$roleId			= Role::where('name','manager')->first()->id;
		if(!empty($request->old_manager))
		{
			$currentManager		= Employee::find($request->old_manager);
			if(count($currentManager->managerDepartments)==1)
			{
				$currentManager->user->roles()->detach([$roleId]);
			}
		}
		$user 	= Employee::find($request->employee_id)->user;
		if(!$user->hasRole('manager'))
		{
		$user->roles()->attach($roleId);
		}
		// new code to be used
		
		$department->manager_id	= $request->employee_id;
		$department->save();
		return 'done';
	}

}
