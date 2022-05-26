<?php

namespace App\Http\Controllers\ems;

use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetCategoryRequest;

class AssetCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new AssetCategory());
        $data['assetCategorys']      =   AssetCategory::all();

        return view('assets.category.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $assetCategory              = new AssetCategory();
        $this->authorize('create', $assetCategory);
        $data['assetCategory']      =   $assetCategory;
        $data['submitRoute']        =   ['asset-category.store'];
        $data['method']             =   'POST';

        return view('assets.category.form',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AssetCategoryRequest $request)
    {
        $assetCategory          = new AssetCategory();
        $this->authorize('create', $assetCategory);
        $assetCategory->name    = $request->name;
        $assetCategory->save();

        return redirect()->route('asset-category.index')->with('success','category created');
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
        $this->authorize('update', new AssetCategory());
        $data['assetCategory']      =   AssetCategory::findOrFail($id);
        $data['submitRoute']        =   ['asset-category.update',['asset_category' => $id]];
        $data['method']             =   'PUT';

        return view('assets.category.form',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AssetCategoryRequest $request, $id)
    {
        $this->authorize('update', new AssetCategory());
        $assetCategory          = AssetCategory::findOrFail($id);
        $assetCategory->name    = $request->name;
        $assetCategory->Update();

        return redirect()->route('asset-category.index')->with('success','category updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', new AssetCategory());
        AssetCategory::findOrFail($id)->delete();
    }
}
