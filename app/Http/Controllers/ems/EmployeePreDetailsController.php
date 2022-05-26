<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Mail\Action;
use Illuminate\Http\Request;
use App\Models\EmployeePreDetails;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Storage;

class EmployeePreDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    // public function employeePreDetails()
    // {
    //     $data['object']     =     EmployeePreDetails::firstOrNew(['user_id'=>auth()->user()->id]);
    //     return view('employee.preDetailsForm',$data);
    // }

    // public function employeePreDetailsSubmit(Request $request)
    // {
    //     dd($request->all());


    // }
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function index(Request $request)
    {
        $details = EmployeePreDetails::with('user');
        
        $data['users']   = User::whereHas('employeePreDetails')->pluck('name','id')->toArray();   
        
        if(!empty($request->user_id))
        {
            $details->where('user_id',$request->user_id);
        }

        $data['details']  = $details->get();
                  
  

        return view('employee.preDetailList',$data);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['object']     =     EmployeePreDetails::firstOrNew(['user_id'=>auth()->user()->id]);
        return view('employee.preDetailsForm',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $employeePreDetails                 =   EmployeePreDetails::firstOrNew(['user_id'=>auth()->user()->id]);
        if($request->has('cv'))
        {
            $employeePreDetails->cv         =  $this->uploadDocuments($request,'cv',auth()->user()->id,$employeePreDetails->cv);
        }
        $employeePreDetails->linked_in      =  $request->linked_in;
        $employeePreDetails->contact_number =  $request->contact_number;
        $employeePreDetails->save();

        $employee                           = $user=auth()->user()->employee;
        $employee->onboard_status           = "document submitted";
        $employee->save();

        $hr   = User::where('email','<>','martha.folkes@theknowledgeacademy.com')->whereHas('roles',function($query){
            $query->where('name','hr');
        })->get();
            $message            = "Predetail submitted by ".$employee->name;
            $subject            = 'Predetail Submitted by '.$employee->name;
            $data['employee']   = $employee;
            $data['link']       = route('interview.index');
            $data['message']    =  $subject;
            $email  = $hr->pluck('email')->toArray();
            
            // send_email("email.action", $data, $subject, $message,$email,null);
            $message = (new Action( $user , $data,$subject,'email.action'))->onQueue('emails');
            $this->mailer->to($email)->later(Carbon::now(),$message);

        return back()->with('success','Details are submitted successfully');

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
        //
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

    public function download(Request $request)
    {

        $user_id   = auth()->user()->id;
        if(!empty($request->user_id))
        {
            $user_id    =   $request->user_id;
        }

        $file   = storage_path("app/documents/employeePreDetails/".$user_id."/$request->reference");

        return response()->file($file, [
            'Content-Type' => 'application/pdf'
        ]);

    }


    private function uploadDocuments($request, $fileName, $employee_id, $old_file = null)
    {
        if (!empty($old_file)) {
            if (\Storage::exists("documents/employeePreDetails/$employee_id/$old_file")) {
                \Storage::delete("documents/employeePreDetails/$employee_id/$old_file");
            }
        }
        $file = $fileName . Carbon::now()->timestamp . '.' . $request->file($fileName)->getClientOriginalExtension();
        $request->file($fileName)->move(storage_path('app/documents/employeePreDetails/' . $employee_id), $file);
        return $file;
    }


}
