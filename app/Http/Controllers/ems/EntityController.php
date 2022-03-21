<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use Illuminate\Http\Request;
use App\Http\Requests\EntityRequest;

class EntityController extends Controller
{
    public function view()
    {
        return view('entity.entity');
    }
    public function list(Request $request)
    {
        $pageIndex      =   $request->pageIndex;
        $pageSize       =   $request->pageSize;
        $entity         =   Entity::query();
        if(!empty($request->get('name')))
        {
            $entity->where('name',$request->get('name'));
        }
        $data['itemsCount']=$entity->count();
        $data['data']=$entity->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get();
        return json_encode($data);
    }
    public function addEntity(EntityRequest $request)
    {
        //$entity = Entity::firstOrNew(['name'=>$request->get('name')]);
        // dd($entity);
            $entity=new Entity();
            $entity->name=$request->name;
            $entity->save();
            return json_encode($entity);

    }
    public function updateEntity(EntityRequest $request, Entity $entity)
    {
        $entity->name = $request->get('name');
        $entity->save();
        return json_encode($entity);
    }

    public function deleteEntity(Entity $entity)
    {
      $entity->delete();
      return json_encode('Entity Deleted Successfully');
    }
}
