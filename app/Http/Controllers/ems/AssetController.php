<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Mail\Action;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\AssetLogs;
use App\Models\AssetType;
use App\Models\Equipment;
use App\Models\Department;
use App\Models\AssetDetails;
use App\Models\AssetSubType;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Http\Requests\AssetRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Config;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer       =       $mailer;
    }
    public function index(Request $request)
    {
        $this->authorize('view', new Asset());
        $assets             =       Asset::with('assetSubType.assetType');

        if(request()->has('type'))
        {
            $assets         =       $assets->whereHas('assetSubType',function($query){
                $query->where('asset_type_id',request()->type);
            });
        }

        if(request()->has('sub_type'))
        {
            $assets         =       $assets->where('sub_type_id',$request->sub_type);
        }

        if(request()->has('bar_code'))
        {


            $assets         =       $assets->where('barcode',$request->bar_code);

        }
        if(request()->has('status'))
        {
            if($request->status=="Unassigned")
            {

                $assets     =       $assets->whereNull('assigned_to')->where('status',"Working");
            }
            elseif($request->status=="Assigned")
            {

                $assets     =       $assets->whereNotNull('assigned_to')->where('status',"Working");
            }
            else
            {
                $assets     =       $assets->where('status',$request->status);
            }

        }

        $data['types']      =       AssetType::pluck('name', 'id')->toArray();
        $data['sub_types']  =       AssetSubType::pluck('name', 'id')->toArray();
        $arr                =       ["Unassigned"=>"Unassigned","Assigned"=>"Assigned"];
        $statuses           =       Config::get('asset.status');
        $data['statuses']   =       array_merge($arr,$statuses);
        $data['assets']     =       $assets->paginate(25);

        return view('assets.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $asset                  =  new Asset();
        $this->authorize('create', $asset);
        $data['asset']          =   $asset;
        $data['categories']     =   AssetCategory::pluck('name', 'id')->toArray();
        $data['status']         =   config('asset.status');
        $data['submitRoute']    =   ['asset.store'];
        $data['method']         =   'POST';

        return view('assets.form', $data);
    }

    public function getTypes(Request $request)
    {
        $this->authorize('view', new Asset());

        return AssetType::where('asset_category_id',$request->id)->pluck('name','id')->toArray();
    }
    public function getSubTypes(Request $request)
    {
        $this->authorize('view', new Asset());

        return AssetSubType::where('asset_type_id',$request->id)->pluck('name','id')->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', new Asset());
        $duplicateExists        =       Asset::where('barcode',$request->barcode)->first();

        if(!empty($duplicateExists) &&  $duplicateExists->sub_type_id!=$request->sub_type_id)
        {
            return 'barcode already exists in different type';
        }
        $asset                  =   Asset::firstOrCreate([
                                        'sub_type_id'   =>  $request->sub_type_id,
                                        'barcode'       =>  $request->barcode
                                    ]);
        $asset->status          =   $request->status;
        $asset->description     =   $request->description;
        $asset->save();

        $log                    =   new AssetLogs();
        $log->asset_id          =   $asset->id;
        $log->user_id           =   auth()->user()->id;
        $log->action            =   $request->status;
        $log->save();
        return 'asset added';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view', new Asset());
        $data['asset']          =       Asset::with('assetSubType','assetLogs', 'assetDetail')->find($id);

        return view('assets.assetDetail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('update', new Asset());
        $data['asset']          =      Asset::find($id);
        $data['types']          =      AssetType::pluck('name', 'id')->toArray();
        $data['submitRoute']    =     ['asset.update', ['asset'=>$id]];
        $data['method']         =     'PUT';
        $data['subTypes']       =     AssetSubType::pluck('name','id')->toArray();
        $data['status']         =     config('asset.status');

        return view('assets.assetEdit', $data);
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
        $this->authorize('update', new Asset());
        $asset                  =   Asset::findOrFail($id);
        $asset->sub_type_id     =   $request->sub_type_id;
        $asset->status          =   $request->status;
        $asset->description     =   $request->description;
        if($request->has('is_exported'))
        {
            $asset->is_exported     =   1;
        }
        else
        {
            $asset->is_exported     =   0;
        }
        $asset->update();
        $log                    =   new AssetLogs();
        $log->asset_id          =   $asset->id;
        $log->user_id           =   auth()->user()->id;
        $log->action            =   $request->status;
        $log->save();

        return redirect(route('asset.index'))->with('success', 'Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function assignEquipments(Request $request)
    {
        $this->authorize('assignEquipments', new Asset());
        $employees               =   Employee::with(['user.assetAssignments.assetSubType.assetType.assetCategory','department'])->
                                    where('biometric_id',$request->id);

        if(auth()->user()->hasRole('powerUser'))
        {
            $employees   =   $employees->where('department_id',auth()->user()->employee->department_id);
        }


        $data['employee']       =   $employees->first();
        if(empty($data['employee']))
        {
            abort(404);
        }

        return view('assets.assetAssignment',$data);
    }


    public function destroy($id)
    {
        //
    }
    public function assignmentList(Request $request)
    {
        $this->authorize('assignmentList', new Asset());
        $employees          =   Employee::whereHas('user',function($user){
                                    $user->where('is_active','1')->where('user_type','Employee');
                                })->withoutGlobalScope('is_active')->with(['user'=>function($user){
                                $user->where('is_active','1')->where('user_type','Employee');
                                }, 'department', 'documents'])->where('onboard_status','Onboard');
        $employeeNames      =   Employee::whereHas('user',function($user){
                                    $user->where('is_active',1)->where('user_type','Employee');
                                })->withoutGlobalScope('is_active')->where('onboard_status','Onboard');

        $data['employeeDepartments']  	=   $employeeNames->select('biometric_id','user_id','department_id','name')->get()->groupBy('department.name');

        if (request()->has('user_id')) {
            $employees      =   $employees->where('user_id', $request->user_id);
        }
        if (request()->has('department_id')) {
            $employees      =   $employees->where('department_id', $request->department_id);
            $employeeNames  =   $employeeNames->where('department_id', $request->department_id);
        }
        if (request()->has('barcode')) {
            $employees      =   $employees->whereHas('user.assetAssignments',function ($assets){
                $assets->where('barcode',request()->barcode);
            });
        }
        if(request()->has('unassigned'))
        {
            if(request()->has('sub_type'))
            {
                $employees  =   $employees->where(function($query){
                    $query->whereHas('user',function($user){
                        $user->where('is_active','1')->where('user_type','Employee');
                    })->whereDoesntHave('user.assetAssignments',function($assets){
                        $assets->where('sub_type_id',request()->sub_type);
                    })->orWhereDoesntHave('user.assetAssignments');
                });
            }
            else
            {
            $employees      =   $employees->whereDoesntHave('user.assetAssignments');
            }
        }
        $data['departments']            =        Department::pluck('name', 'id')->toArray();
        $data['assetSubTypes']          =        AssetSubType::where('is_assignable','1')->pluck('name', 'id')->toArray();
        if(auth()->user()->hasRole('powerUser'))
        {
            $data['employees']          =        $employees->where('department_id',auth()->user()->employee->department_id)->orderBy('id', 'desc')->paginate(25);
            $data['employeeNames']      =        $employeeNames->where('department_id',auth()->user()->employee->department_id)->pluck('name', 'name')->toArray();
        }
        else
        {

            $data['employees']          =        $employees->get();
            $data['employeeNames']      =        $employeeNames->pluck('name', 'name')->toArray();
            
        }

        return view('assets.assignmentList', $data);

    }

    public function assignAsset(Request $request)
    {
        $this->authorize('assignAsset', new Asset());
        $data               =   [];
        $asset              =   Asset::firstWhere('barcode', $request->assetBarCode);
        if (empty($asset)) {
            $data['message']            =      false;
            return $data;
        }

        if ($request->action == 'unassign')
        {
            if (empty($asset->assigned_to))
            {
                $data['message']        =       'Asset is already Unassigned';
            }
            else
            {
                $user                   =       User::find($asset->assigned_to);
                $data['message']        =       'Asset Unassigned';
                $data['asset']          =       $asset->assetSubType->name;
                $emailData['message']   =       $asset->assetSubType->name." unassigned by ".auth()->user()->name;
                $to                     =       $user->email;
                $emailData['link']      =       route('employeeDetail',['employee'=>$user->employee->id]);

                $message = (new Action($user, $emailData,'Asset Unassigned','email.action'))->onQueue('emails');
                $this->mailer->to($to)->later(Carbon::now()->addSeconds(30),$message);
            }
            $asset->assigned_to         =       null;
            $asset->save();

            return $data;
        }
        else
        {
            $employee                   =       Employee::firstWhere('biometric_id', $request->biometric_id);
            if (empty($asset->assigned_to))
            {
                $asset->assigned_to = $employee->user_id;
                $asset->save();

                $data['asset']           =      $asset->assetSubType->name;
                $data['message']         =      'Asset assigned';
                $emailData['message']    =      $asset->assetSubType->name." assigned by ".auth()->user()->name;
                $emailData['link']       =      route('employeeDetail',['employee'=>$employee->id]);
                $to                      =      $employee->user->email;
                $message                 =      (new Action($employee->user, $emailData,'Asset Assigned','email.action'))->
                                                onQueue('emails');
                $this->mailer->to($to)->later(Carbon::now()->addSeconds(10),$message);
                $data['view']            =      view('assets.assetComponent', ['assetAssignment' => $asset])->render();
            }
            else
            {
                if ($asset->user->employee->biometric_id != $request->biometric_id)
                {
                    $data['message']    =       'Asset is already assigned';
                }
                else
                {
                    $data['message']    =       'Asset assigned';
                    $data['view']       =       '';
                }
            }

            $log                        =       new AssetLogs();
            $log->asset_id              =       $asset->id;
            $log->user_id               =       auth()->user()->id;
            $log->action                =       $request->action;
            $log->save();
        }

        return $data;
    }

    public function dashboard(Request $request)
    {
        $this->authorize('dashboard', new Asset());
        $subTypes       =       AssetSubType::with(['assetType','assets.user','assets'=>function($asset)
                                {
                                    if(request()->has('status'))
                                    {
                                        $asset->where('status',request()->status);
                                    }

                                }])->get();
        if($request->has('type_id'))
        {
            $subTypes=$subTypes->where('id',$request->type_id);
        }
        $subTypesCount =        [];
        foreach($subTypes as $subType)
        {
            $assetCount['assetType']            =   $subType->assetType->name;
            $assetCount['subTypeName']          =   $subType->name;
            $assetCount['id']                   =   $subType->id;
            $assetCount['maintenanceCount']     =   $subType->assets->where('status',"Maintenance")->count();
            $assetCount['damagedCount']         =   $subType->assets->where('status',"Damaged")->count();
            $assetCount['assignedCount']        =   $subType->assets->where('status',"Working")->whereNotNull('assigned_to')->count();
            $assetCount['workingCount']         =   $subType->assets->where('status',"Working")->count();
            $assetCount['unassignedCount']      =   $subType->assets->where('status',"Working")->whereNull('assigned_to')
                                                    ->where('status','<>',"Maintenance")->where('status','<>',"Damaged")->count();
            $assetCount['totalCount']           =   $subType->assets->count();
            $subTypesCount[]                    =   $assetCount;
        }

        $data['subTypesCount']                 =   collect($subTypesCount)->sortBy('assetType');
        $assets                                =   Asset::get();
        if($request->has('type_id'))
        {
            $assets                            =   $assets->where('sub_type_id',$request->type_id);
        }
        if($request->status)
        {
           $assets->where('status',$request->status);
        }
        $pieChart                              =   $this->pieChart($assets);
        $data['pieChartValues']                =   $pieChart['values'];
        $data['pieChartLabels']                =   $pieChart['labels'];
        $barChartTypes                         =   $this->barChart($subTypes);
        $data['barChart']                      =   collect($barChartTypes)->sortByDesc('total')->values();
        $data['types']                         =   AssetSubType::pluck('name','id');
        $data['statuses']                      =   ["Damaged"=>"Damaged","Maintenance"=>"Maintenance","Working"=>"Working","Assigned"=>"Assigned","Unassigned"=>"Unassigned"];
        $data['employeeCount']                 =   User::whereIn('user_type',['Employee'])->whereHas('employee',function($employee)
                                                    {
                                                        $employee->where('onboard_status','Onboard');
                                                    })->where('is_active',1)->count();
        $dashboard                              =  new   EmployeeDashboardController();
        $data['departmentUnassignedAssets']     =   $dashboard->assetData();
        $data['subTypes']                       =   AssetSubType::whereIn('name',["Laptop",'Charger','Mouse','Headphone'])->pluck('id','name');
        return view('assets.dashboard',$data);
    }


    public function pieChart($assets)
    {
        $pieChart           =    [];
        $damaged            =    $assets->where('status',"Damaged")->count();
        $maintenance        =    $assets->where('status',"Maintenance")->count();
        $assignedCount      =    $assets->where('status',"Working")->whereNotNull('assigned_to')->count();
        $workingCount       =    $assets->where('status',"Working")->count();
        $unassignedCount    =    $assets->where('status',"Working")->whereNull('assigned_to')->where('status','<>',"Maintenance")->where('status','<>',"Damaged")->count();
        $labels             =    ["Damaged ($damaged)", "Working ($workingCount)", "Maintenance($maintenance)", "Assigned ($assignedCount)", "Unassigned($unassignedCount)"];
        $pieChart           =    [$damaged, $workingCount, $maintenance, $assignedCount,$unassignedCount];

        return ['labels' => $labels, 'values' => $pieChart];
    }

    public function barChart($subTypes)
    {
        $barChartTypes      =       [];
        foreach ($subTypes as $subType) {


            $chart['subTypeName']         =     $subType->name ."(". $subType->assets->count()  .")";
            $chart['maintenanceCount']    =     $subType->assets->where('status',"Maintenance")->count();
            $chart['damagedCount']        =     $subType->assets->where('status',"Damaged")->count();
            $chart['assignedCount']       =     $subType->assets->where('status',"Working")->whereNotNull('assigned_to')->count();
            $chart['workingCount']        =     $subType->assets->where('status',"Working")->count();
            $chart['unassignedCount']     =     $subType->assets->where('status',"Working")->whereNull('assigned_to')->
                                                where('status','<>',"Maintenance")->where('status','<>',"Damaged")->count();
            $barChartTypes[]              =     $chart;
        }

         return  $barChartTypes;
    }

}
