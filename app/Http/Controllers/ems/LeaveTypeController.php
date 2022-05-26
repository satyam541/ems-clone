<?php

namespace App\Http\Controllers\ems;

use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveTypeRequest;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new LeaveType());
        $data['leaveTypes']      =   LeaveType::all();
        
        return view('leaveType.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $leaveType              =    new LeaveType();
        $this->authorize('create', $leaveType );
        $data['leaveType']      =   $leaveType ;
        $data['submitRoute']    =   ['leave-type.store'];
        $data['method']         =   'POST';

        return view('leaveType.form',$data);        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeaveTypeRequest $request)
    {
        $leaveType          =    new LeaveType();
        $this->authorize('create', $leaveType);
        $leaveType->name    = $request->name;

        $leaveType->save();

        return redirect(route('leave-type.index'))->with('success', 'Leave Type added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('update', new LeaveType());
        $data['leaveType']      =   LeaveType::find($id);
        $data['submitRoute']    =   ['leave-type.update',$id];
        $data['method']         =   'PUT';

        return view('leaveType.form',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LeaveTypeRequest $request, $id)
    {
        $this->authorize('update', new LeaveType());
        $leaveType          =    LeaveType::find($id);
        $leaveType->name    =    $request->name;

        $leaveType->update();

        return redirect(route('leave-type.index'))->with('success', 'Leave Type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', new LeaveType());
        LeaveType::findOrFail($id)->delete();
    }
}
