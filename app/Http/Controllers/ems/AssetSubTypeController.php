<?php

namespace App\Http\Controllers\ems;

use App\Models\AssetType;
use App\Models\Equipment;
use App\Models\AssetSubType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssetSubTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new AssetSubType());
        $data['subTypes']    =      AssetSubType::with('assetType')->get();

        return view('assets.subType.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $subType                      = new AssetSubType();
        $this->authorize('create', $subType);
        $data['subType']              = $subType;
        $data['method']               = 'POST';
        $data['submitRoute']          = ['asset-subtype.store'];
        $data['types']                = AssetType::pluck('name', 'id')->toArray();

        return view('assets.subType.form',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $subType                    = new AssetSubType();
        $this->authorize('create', $subType);
        $subType->name              = $request->name;
        $subType->is_assignable     = empty($request['is_assignable']) ? 0 : 1;
        $subType->asset_type_id     = $request->asset_type_id;
        $subType->save();

        return redirect()->route('asset-subtype.index')->with('success','subType created');
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
        $this->authorize('update', new AssetSubType());
        $data['subType']              = AssetSubType::findOrFail($id);
        $data['method']               = 'PUT';
        $data['submitRoute']          = ['asset-subtype.update',['asset_subtype' => $id]];
        $data['types']                = AssetType::pluck('name', 'id')->toArray();

        return view('assets.subType.form',$data);
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
        $this->authorize('update', new AssetSubType());
        $subType                    = AssetSubType::findOrFail($id);
        $subType->name              = $request->name;
        $subType->is_assignable     = empty($request['is_assignable']) ? 0 : 1;
        $subType->asset_type_id     = $request->asset_type_id;
        $subType->update();

        return redirect()->route('asset-subtype.index')->with('success','subType updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', new AssetSubType());
        AssetSubType::findOrFail($id)->delete();
    }
}
