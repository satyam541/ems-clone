<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleRequest;
use App\Models\Module;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\User;

class ModuleController extends Controller
{
    public function moduleView()
    {
        $this->authorize('view', new Module());
        return view('module.modules');
    }

    public function moduleList(Request $request)
    {
        $this->authorize('view', new Module());
        $pageIndex  = $request->pageIndex;
        $pageSize   = $request->pageSize;
        $modules    = Module :: query();

        if(!empty($request->get('name')))
        {        
          $modules  = $modules->where('name','like', '%' .$request->get('name') . '%');
        }

        //$modules = $query->get();
        $data['itemsCount'] = $modules->count();
        $data['data']       = $modules->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get();
        
        return json_encode($data);
    }

    public function insertModule(ModuleRequest $request)
    {
        $this->authorize('insert', new Module());
        $module         = new Module();
        $module->name   = $request->name;
        $module->save();
        if(!empty($module->id))
        return $module;
    }

    public function updateModule(ModuleRequest $request)
    {
        $module     = Module :: findOrFail($request->input('id'));
        $this->authorize('update', $module);
        $module->name        = $request->name;
        $module->save();
        if(!empty($module->id))
        return $module;
    }

    public function deleteModule(Request $request)
    {
        $module = Module::findOrFail($request->input('id'));
        $this->authorize('delete', $module);
        $module->delete();
        return json_encode('done');
    }

    public function viewTrash()
    {
        $this->authorize('trash', new User());
        return view('trash.moduleTrashedList');
    }

    public function trashList(Request $request)
    {   
        $this->authorize('trash', new User());
        $pageIndex      = $request->pageIndex;
        $pageSize       = $request->pageSize;
        $modules        = Module :: onlyTrashed();
        $name           = $request->get('name');
        $description    = $request->get('description');
       

        if (!empty($name)) {
            $modules->where('name', $name);
        }
        if(!empty($description)) {
            $modules->where('description','like', '%' . $description . '%');
        }

        $data['itemsCount'] = $modules->count();
        $data['data']       = $modules->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get();
        
        return json_encode($data);
    }

    public function restore(Request $request)
    {
        $module = Module :: onlyTrashed()->find($request->get('module'));
        $this->authorize('restore', $module);
        $module->restore();

        return 'restored';
    }

    public function forcedelete(Request $request)
    {
        $module = Module :: onlyTrashed()->find($request->get('module'));
        $this->authorize('forceDelete', $module);
        $module->forceDelete();
        
        return 'destroyed';
    }
}
