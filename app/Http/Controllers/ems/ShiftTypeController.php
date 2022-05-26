<?php

namespace App\Http\Controllers\ems;

use App\Models\ShiftType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShiftTypeRequest;

class ShiftTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['shiftTypes']     =   ShiftType::all();

        return view('shiftType.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['shift']          = new ShiftType();
        $data['submitRoute']    = ['shift-type.store'];
        $data['method']         = 'POST';

        return view('shiftType.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ShiftType::firstOrCreate([
            'name'              => $request->name,
            'start_time'        => $request->start_time,
            'mid_time'          => $request->mid_time,
            'end_time'          => $request->end_time,
        ]);

        return redirect()->route('shift-type.index')->with('Added Successfully.');
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
        $data['shift']          = ShiftType::find($id);
        $data['submitRoute']    = ['shift-type.update',['shift_type' => $id]];
        $data['method']         = 'PUT';

        return view('shiftType.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ShiftTypeRequest $request, $id)
    {
        $type               = ShiftType::find($id);
        $type->name         = $request->name;
        $type->start_time   = $request->start_time;
        $type->mid_time     = $request->mid_time;
        $type->end_time     = $request->end_time;
        $type->update();

        return redirect()->route('shift-type.index')->with('Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ShiftType::find($id)->delete();
    }
}
