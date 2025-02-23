<?php

namespace App\Http\Controllers\ems;

use App\models\Designation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $pageIndex  = $request->pageIndex;
            $pageSize   = $request->pageSize;
            $designations    = Designation::query();

            if(!empty($request->get('name')))
            {        
                $designations  = $designations->where('name','like', '%' .$request->get('name') . '%');
            }
            
            $data['itemsCount'] = $designations->count();
            $data['data']       = $designations->limit($pageSize)->offset(($pageIndex-1)* $pageSize)->get();
            
            return json_encode($data);
        }

        return view('designation.designations');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $designation         = new Designation();
        $designation->name   = $request->name;
        $designation->save();

        if(!empty($designation->id))
        return $designation;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $designation            = Designation::findOrFail($id);
        $designation->name      = $request->name;
        $designation->save();

        if(!empty($designation->id))
        return $designation;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Designation::findOrFail($id)->delete();

        return json_encode('done');
    }
}
