<?php

namespace App\Http\Controllers\ems;

use App\Models\Asset;
use App\Models\Equipment;
use App\Models\AssetDetails;
use App\Models\AssetSubType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetDetailsRequest;

class AssetDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', new AssetDetails());

        $data['assetDetail']    =   AssetDetails::firstOrNew(['asset_id' => $request->asset]);
        $data['submitRoute']    =   ['asset-detail.store'];
        $data['method']         =   'POST';

        return view('assets.detail.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AssetDetailsRequest $request)
    {
        $this->authorize('create', new AssetDetails());

        $assetDetail            =   AssetDetails::updateOrCreate([
                'asset_id' => $request->asset_id],[
                'company' => $request->company,
                'ram' => $request->ram,
                'rom' => $request->rom,
        ]);

        // if ($request->has('bill')) {
        //     $files = $request->company . '.' . $request->file('bill')->getClientOriginalExtension();
        //     $request->file('bill')->move(public_path('upload/bill'), $files);
        //     $assetDetail['bill'] = $files;
        //     $assetDetail->save();
        // }

        return redirect()->route('asset.show',['asset' => $request->asset_id])->with('success', 'Created Successfully.');
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
        $this->authorize('update', new AssetDetails());

        $data['assetDetail']    =   AssetDetails::findOrFail($id);
        $data['submitRoute']    =   ['asset-detail.update',['asset_detail' => $id]];
        $data['method']         =   'PUT';

        return view('assets.detail.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AssetDetailsRequest $request, $id)
    {
        $this->authorize('update', new AssetDetails());

        $assetDetail            =   AssetDetails::findOrFail($id);
        $assetDetail->asset_id  =   $request->asset_id;
        $assetDetail->company   =   $request->company;
        $assetDetail->ram       =   $request->ram;
        $assetDetail->rom       =   $request->rom;
        $assetDetail->update();

        // if ($request->has('bill')) {
        //     $files = $request->company . '.' . $request->file('bill')->getClientOriginalExtension();
        //     $request->file('bill')->move(public_path('upload/bill'), $files);
        //     $assetDetail['bill'] = $files;
        //     $assetDetail->save();
        // }

        return redirect()->route('asset.show',['asset' => $request->asset_id])->with('success', 'Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
