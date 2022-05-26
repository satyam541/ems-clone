<?php

namespace App\Http\Controllers\ems;

use Str;
use Hash;
use Mail;
use Storage;
use App\User;
use FileVault;
use Carbon\Carbon;
use App\Mail\Action;
use App\Models\Role;
use App\Models\Document;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Interview;
use App\Models\ShiftType;
use App\Models\BankDetail;
use App\Models\Department;
use PhpParser\Comment\Doc;
use App\Imports\TestImport;
use App\Imports\UserImport;
use App\Models\Designation;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use App\Models\Notifications;
use App\Models\Qualification;
use App\Exports\EmployeeExport;
use App\Imports\EmployeeImport;
use App\Models\EmployeeExitDetail;
use App\Models\EmployeePreDetails;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use App\Models\EmployeeProfileDraft;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\EmployeeRequest;
use App\Models\PendingProfileReminder;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\EmployeeEmergencyContact;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\OfficeEmailRequest;


class EmployeeController extends Controller

{
    private $Image_prefix;
    public function __construct(Mailer $mailer)
    {
        $this->Image_prefix     = "userImage";
        $this->mailer = $mailer;
    }

    public function view(Request $request, $export = false)
    {
        $this->authorize('hrEmployeeList', new Employee());

        if (request()->has('user_type')) {
            $employees          = Employee::withoutGlobalScopes(['is_active', 'guest'])
                ->with('user', 'department');
            $employeeNames      = Employee::withoutGlobalScopes(['is_active', 'guest']);
            $employees  = $employees->whereHas('user', function ($user) {
                $user->whereIn('user_type', request()->user_type);
            });
        } else {
            $employees          = Employee::withoutGlobalScopes(['is_active', 'guest'])
                ->with('user', 'department')->where('onboard_status', 'Onboard');
            $employeeNames      = Employee::withoutGlobalScopes(['is_active', 'guest'])->where('onboard_status', 'Onboard');
            $employees  = $employees->whereHas('user', function ($user) {
                $user->where('user_type', 'Employee');
            });
        }
        if (request()->has('user_id')) {
            $employees->where('user_id', $request->user_id);
        }
        if (request()->has('office_email')) {
            $employees  = $employees->where('office_email', $request->office_email);
        }
        if ($request->status == 'exit') {
            $employees    = $employees->where('is_active', 0);
        } else {
            $employees    = $employees->where('is_active', 1);
        }

        if (request()->has('department_id')) {
            $employees      = $employees->where('department_id', $request->department_id);
            $employeeNames  = $employeeNames->where('department_id', $request->department_id);
        }
        if (request()->has('shift_time')) {

            $employees  = $employees->whereHas('user', function ($user) {
                $user->where('shift_type_id', request()->shift_time);
            });
        }

        if (request()->has('shift_type')) {

            $employees  = $employees->whereHas('user', function ($user) {
                $user->whereHas('shiftType', function ($query) {
                    $query->where('name', request()->shift_type);
                });
            });
        }

        if (request()->has('gender')) {
            $employees  =   $employees->where('gender', $request->gender);
        }
        if (request()->has('biometric_id')) {
            $employees  =   $employees->where('biometric_id', $request->biometric_id);
        }

        if (request()->has('is_power_user')) {
            $employees  =   $employees->where('is_power_user', '1');
        }

        // $data['name']                   = $employeeNames->where('is_active', 1)->pluck('name', 'name')->toArray();
        $data['employeeDepartments']  	=   Employee::with('user')->select('biometric_id','user_id','department_id','name')->whereHas('user',function($user)
                                            {
                                                $user->where('is_active',1)->where('user_type','Employee');
                                            })->get()->groupBy('department.name');

        $data['office_emails']           = Employee::where('is_active',1)->pluck('office_email', 'office_email')->toArray();
        $data['department_id']           = Department::pluck('name', 'id')->toArray();

        $data['shift_types']            = ['Morning' => 'Morning', 'Evening' => 'Evening'];
        $data['userTypes']              = config('employee.userTypes');
        $data['gender']                 = config('employee.gender');
        $data['shiftTypes']             = ShiftType::get();
        $employees                      = $employees->orderBy('id', 'desc');
        if ($export) {
            return $employees->get();
        }
        $data['employees']              = $employees->paginate(20);
        //     $data['employees']              = $employees->leftjoin("users","employee.user_id","=","users.id")->leftjoin("role_user","role_user.user_id","=","users.id")
        //     ->leftjoin("role","role_user.role_id","=","role.id")->orderBy('display_order')->select('employee.*','users.id','role_user.*','role.display_order')
        //    ->get()->unique('office_email');
        return view('employee.employee', $data);
    }

    public function create()
    {

        $this->authorize('hrUpdateEmployee', new Employee());
        $data['employee']                  =        new Employee();
        $data['submitRoute']               =        "insertEmployee";
        $list['departments']               =        Department::pluck('name', 'id')->toArray();
        $list['qualification']             =        Qualification::pluck('name', 'id')->toArray();
        $data['list']                      =        $list;
        $data['designations']              =        Designation::pluck('name', 'id')->toArray();
        $data['documents']                 =        collect();
        $data['shifts']                    =        ShiftType::all();
        $data['days']                      =        config('employee.days');
        $data['userTypes']                 =   config('employee.userTypes');
        return view('employee.employeeForm', $data);
    }

    public function insert(EmployeeRequest $request)
    {
        $userInputs                        =    $request->only(['name', 'personal_email', 'office_email']);
        $user                              =    new User();
        $user->name                        =    $userInputs['name'];
        $user->email                       =    strtolower($userInputs['office_email']);
        $password                          =    $this::generatePassword();
        $user->password                    =   \Hash::make($password);
        $user->is_active                   =    "1";
        $user->user_type                   =   empty($request->user_type) ? 'Employee' : $request->user_type;

        if (!empty($request->off_day)) {
            $user->off_day          =    $request->off_day;
        }

        $user->save();
        $role    = Role::where('name', 'employee')->first();
        $user->roles()->sync($role->id);
        $user->save();
        $user->rawPassword                 =    $password;
        $this->sendPassword($user);
        $userDetailInputs                  =   $request->except(['name', 'personal_email']);
        if (!empty($userDetailInputs)) {

            $employee                       =   new Employee();
            $employee->name                 =   $request->name;
            $employee->registration_id      =   $request->registration_id;
            $employee->personal_email       =   $request->personal_email;
            $employee->department_id        =   $request->department_id;
            $employee->phone                =   $request->phone;
            $employee->birth_date           =   $request->birth_date;
            $employee->join_date            =   $request->join_date;
            $employee->biometric_id         = $request->biometric_id;
            $employee->qualification_id     =   $request->qualification_id;
            $employee->contract_date        =   $request->contract_date;
            $employee->user_id              =   $user->id;
            $employee->office_email         =   $request->office_email;
            $employee->shift_type_id        =   $request->shift_type_id;
            $employee->gender               =   $request->gender;


            if ($request->is_active == 'on') {
                $employee->is_active        =   1;
            }
            $employee->designation_id       =   $request->designation_id;
            $employee->pf_no                =   $request->pf_no;

            if ($request->hasFile('profile_pic')) {
                $employee->profile_pic  =   $this->uploadProfilePic($request);
            }
            $employee->onboard_status              =    'onboard';
            $employee->save();
            $user       =       $employee->user;
            if (!empty($user)) {
                if ($request->has('start_time')) {
                    $user->start_time       =   $request->start_time;
                }
                if ($request->has('end_time')) {
                    $user->end_time         =   $request->end_time;
                }
                $user->save();
            }
            if ($request->bank_details_form == 'on') {
                //save bank details
                $bankDetail                     = new BankDetail();
                $bankDetail->employee_id        = $employee->id;
                $bankDetail->account_holder     = $request->account_holder;
                $bankDetail->bank_name          = $request->bank_name;
                $bankDetail->account_no         = $request->account_no;
                $bankDetail->ifsc_code          = $request->ifsc_code;
                $bankDetail->save();
            }


            $employee->user_id       = $user->id;
            $employee->office_email  = strtolower($request->office_email);
            $employee->save();
            $user_ids   = User::havingRole('IT');
            $message    = "Assign email to" . $employee->name;
            $link       = route("officeEmailView");
            send_notification($user_ids, $message, $link);

            // Documments Upload

            $uploadedDocuments  = [];

            if ($request->hasFile('aadhaar_file')) {
                $uploadedDocuments['aadhaar_file']  =   $this->uploadDocuments($request, 'aadhaar_file', $employee->id);
            }
            if ($request->hasFile('pan_file')) {
                $uploadedDocuments['pan_file']  =   $this->uploadDocuments($request, 'pan_file', $employee->id);
            }
            if ($request->hasFile('cv')) {
                $uploadedDocuments['cv']    =   $this->uploadDocuments($request, 'cv', $employee->id);
            }

            $this->saveDocuments($request, $employee->id, $uploadedDocuments);
        }
        return back()->with('success', 'Employee Registered successfully');
    }

    public function edit(Request $request)
    {

        $employee               =   Employee::withoutGlobalScopes()->find($request->employee);
        $this->authorize('hrUpdateEmployee', $employee);
        $data['employee']       =   $employee->load('designation', 'documents');
        $data['documents']      =   $employee->documents;
        $data['submitRoute']    =   array('updateEmployee');
        $list['departments']    =   Department::pluck('name', 'id')->toArray();
        $list['qualification']  =   Qualification::pluck('name', 'id')->toArray();
        $data['list']           =   $list;
        $data['designations']   =   Designation::pluck('name', 'id')->toArray();
        $data['shifts']                    =        ShiftType::all();
        $data['days']           =   config('employee.days');
        $data['userTypes']      =   config('employee.userTypes');
        return view('employee.employeeForm', $data);
    }

    public function update(EmployeeRequest $request)
    {

        $documents  = $request->only(['aadhaar_file', 'pan_file', 'cv', 'cheque', 'asset_policy']);
        $employee   = Employee::withoutGlobalScopes()->find($request->id);

        if ($employee->department_id !=  $request->department_id) {
            $users      = User::havingRole('Admin');
            $message    = $employee->name .  "   department is changed";
            $route      = ['name' => 'editUser', 'parameter' => $employee->user->id];
            send_notification($users, $message, $route);
        }

        if ($request->hasFile('profile_pic')) {

            $employee->profile_pic = $this->uploadProfilePic($request, $employee->profile_pic);
        }

        $employee->name             = $request->name;
        $employee->personal_email   = $request->personal_email;
        $employee->phone            = $request->phone;
        $employee->birth_date       = $request->birth_date;
        $employee->join_date        = $request->join_date;
        $employee->department_id    = $request->department_id;
        $employee->qualification_id = $request->qualification_id;
        $employee->registration_id  = $request->registration_id;
        if ($request->has('is_active')) {
            $employee->is_active        = empty($request->is_active) ? 0 : 1;
        }
        $employee->contract_date    = $request->contract_date;
        $employee->designation_id   = $request->designation_id;
        $employee->biometric_id     = $request->biometric_id;
        $employee->office_email     = $request->office_email;
        $employee->gender           = $request->gender;
        $employee->is_power_user    = !empty($request->is_power_user) ? 1 : 0;

        $employee->pf_no    = $request->pf_no;
        if ($request->hasFile('id_card_photo')) {

            $employee->id_card_photo  = $this->uploadDocuments($request, 'id_card_photo', $employee->id, $employee->id_card_photo ?? null);
        }
        $employee->save();

        $user                   =   $employee->user;
        $user->shift_type_id    =   $request->shift_type_id;
        if (!empty($request->off_day)) {
            $user->off_day          =    $request->off_day;
        }
        $user->user_type            = $request->user_type;
        $user->email                =  $request->office_email;
        $user->save();

        //save bank details
        if (!empty($employee->bankdetail)) {
            $bankDetail = $employee->bankdetail;
        } else {
            $bankDetail = new BankDetail();
        }

        $bankDetail->employee_id    = $employee->id;
        $bankDetail->account_holder = $request->account_holder;
        $bankDetail->bank_name      = $request->bank_name;
        $bankDetail->account_no     = $request->account_no;
        $bankDetail->ifsc_code      = $request->ifsc_code;
        $bankDetail->save();

        // documents upload

        $currentDocumentDetails     = Document::select('aadhaar_file', 'pan_file', 'cv', 'cheque', 'asset_policy')->where('employee_id', $request->id)->first();

        if (!empty($currentDocumentDetails)) {
            $currentDocumentDetails = $currentDocumentDetails->toArray();
            $newDocumentDetails     = array_diff($documents, $currentDocumentDetails);
        } else {
            $newDocumentDetails     = $documents;
        }

        if (!empty($newDocumentDetails)) {
            $uploadedDocuments  = [];

            if ($request->hasFile('aadhaar_file')) {
                $uploadedDocuments['aadhaar_file']  = $this->uploadDocuments($request, 'aadhaar_file', $employee->id, $currentDocumentDetails['aadhaar_file'] ?? null);
            }
            if ($request->hasFile('pan_file')) {
                $uploadedDocuments['pan_file']  = $this->uploadDocuments($request, 'pan_file', $employee->id, $currentDocumentDetails['pan_file'] ?? null);
            }
            if ($request->hasFile('cv')) {
                $uploadedDocuments['cv']    = $this->uploadDocuments($request, 'cv', $employee->id, $currentDocumentDetails['cv'] ?? null);;
            }
            if ($request->hasFile('cheque')) {
                $uploadedDocuments['cheque']    = $this->uploadDocuments($request, 'cheque', $employee->id, $currentDocumentDetails['cheque'] ?? null);;
            }
            if ($request->hasFile('asset_policy')) {
                $uploadedDocuments['asset_policy']    = $this->uploadDocuments($request, 'asset_policy', $employee->id, $currentDocumentDetails['asset_policy'] ?? null);;
            }

            $this->saveDocuments($request, $employee->id, $uploadedDocuments);
        }

        if ($employee->onboard_status == 'asked for documents') {
            $employee->onboard_status = 'documents submitted';
            $user = $employee->user;
            $data['link']       = route('onboardDashboard');
            $data['user']       = $user;
            $to                 = User::whereHas('employee.department', function ($query) {
                $query->where('name', 'HR');
            })->pluck('email', 'email')->toArray();
            $data['subject']    = "Documents Submitted";
            $data['message']    = "Documents Submitted by $employee->name";
            $message = (new Action($user, $data, 'Documents Submitted', 'email.action'))->onQueue('emails');
            $this->mailer->to($to)->later(Carbon::now()->addSeconds(10), $message);
        } elseif ($employee->onboard_status == 'documents submitted') {
            $employee->department_id   =    $request->department_id;
            $employee->join_date       =    $request->join_date;
            $employee->onboard_status  =   'contract pending';
            $user = $employee->user;
            $data['link']       = route('login');
            $data['user']       = $user;
            $it                 = User::whereHas('employee.department', function ($query) {
                $query->where('name', 'IT');
            })->pluck('email', 'email')->toArray();
            $manager            =   Department::find($request->department_id)->deptManager->office_email;
            $it[$manager]       =   $manager;
            $to                 =   $it;
            $data['subject']    = "Documents Submitted";
            $data['message']    = "New employee added fulfill the requirements";
            $message = (new Action($user, $data, 'New Employee Added', 'email.action'))->onQueue('emails');
            $this->mailer->to($to)->later(Carbon::now()->addSeconds(10), $message);
        } elseif ($employee->onboard_status == 'contract pending') {
            $employee->onboard_status  =   $request->status;
        }
        $employee->save();
        return  back()->with('success', 'Data updated successfully');
    }

    private function uploadProfilePic($request, $old_profile_pic = null)
    {
        if (!empty($old_profile_pic)) {
            $fileName   = 'upload/employeeimage/' . $old_profile_pic;
            if (File::exists(public_path($fileName))) {
                File::delete(public_path($fileName));
            }
        }
        $imageName = $this->Image_prefix . Carbon::now()->timestamp . '.' . $request->file('profile_pic')->getClientOriginalExtension();
        $request->file('profile_pic')->move(public_path('upload/employeeimage'), $imageName);
        return $imageName;
    }

    private function uploadDocuments($request, $fileName, $employee_id, $old_file = null)
    {
        if (!empty($old_file)) {
            if (\Storage::exists("documents/employee/$employee_id/$old_file")) {
                \Storage::delete("documents/employee/$employee_id/$old_file");
            }
        }
        $file = $fileName . Carbon::now()->timestamp . '.' . $request->file($fileName)->getClientOriginalExtension();
        $request->file($fileName)->move(storage_path('app/documents/employee/' . $employee_id), $file);
        return $file;
    }

    private function saveDocuments($request, $employee_id, $uploadedDocuments)
    {
        $profile    = null;
        if (!empty($request->id)) {
            $profile    = Document::where('employee_id', $employee_id)->first();
        }

        if (empty($profile)) {
            $profile                = new Document();
            $profile->employee_id   = $employee_id;
        }

        $profile->aadhaar_number    = $request->aadhaar_number;

        if (!empty($uploadedDocuments) && array_key_exists('aadhaar_file', $uploadedDocuments)) {
            $profile->aadhaar_file = $uploadedDocuments['aadhaar_file'];
        }

        $profile->pan_number    = $request->pan_number;

        if (!empty($uploadedDocuments) && array_key_exists('pan_file', $uploadedDocuments)) {
            $profile->pan_file = $uploadedDocuments['pan_file'];
        }

        if (!empty($uploadedDocuments) && array_key_exists('cv', $uploadedDocuments)) {
            $profile->cv = $uploadedDocuments['cv'];
        }
        if (!empty($uploadedDocuments) && array_key_exists('cheque', $uploadedDocuments)) {
            $profile->cheque = $uploadedDocuments['cheque'];
        }
        if (!empty($uploadedDocuments) && array_key_exists('asset_policy', $uploadedDocuments)) {
            $profile->asset_policy = $uploadedDocuments['asset_policy'];
        }
        $profile->save();
    }

    public function delete(Request $request)
    {
        $employee   = Employee::withoutGlobalScope('is_active')->find($request->id);
        $this->authorize('hrUpdateEmployee', $employee);
        $employee->delete();
    }

    public function detail(Request $request)
    {
        $this->authorize('viewProfile', new Employee());
        $employeeId =   $request->employee;
        if(!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('HR') && !auth()->user()->hasRole('manager'))
        {
            $employeeId =   auth()->user()->employee->id;
        }
        $employee   = Employee::withoutGlobalScopes()->with(['bankdetail', 'documents'])->find($employeeId);
        if(auth()->user()->hasRole('manager') && (!auth()->user()->hasRole('HR') && !auth()->user()->hasRole('Admin')))
        {
            if($employee->department_id!=auth()->user()->employee->department_id)
            {
                $employee   = Employee::withoutGlobalScopes()->with(['bankdetail', 'documents'])->find(auth()->user()->employee->id);
            }
        }
        if (empty($employee)) {
            abort(404);
        }

        $employee['birth_date']     =   $employee->birth_date;
        $employee['join_date']      =   $employee->join_date;
        $employee->load('department.deptManager', 'designation', 'documents', 'equipmentAssigned');
        $data['employee']           =   $employee;
        $data['documents']          =   $employee->documents;

        $data['assets']             =   $employee->user->assetAssignments;
        return view('employee.employeeDetail', $data);
    }

    //download employee excel
    public function export(Request $request)
    {
        ini_set('max_execution_time', -1);
        $employees   = $this->view($request, true);

        return Excel::download(new EmployeeExport($employees), 'employee.xlsx');
    }

    public function download_document(Request $request)
    {
        $file   = storage_path("app/documents/employee/$request->employee/$request->reference");
        return response()->file($file);
    }

    public function import()
    {
        Excel::import(new TestImport, request()->file('file'));
        return back()->with('success', 'Employee Imported Successfully');
    }

    public function viewOfficeEmail()
    {
        $this->authorize('it', new Equipment());
        $data['employees']  = Employee::withoutGlobalScopes(['guest'])->with('department')->where(['user_id' => null, 'office_email' => null])->get();

        return view('employee.officeEmail', $data);
    }

    public function emailList(Request $request)
    {
        $pageIndex  = $request->pageIndex;
        $pageSize   = $request->pageSize;
        $employees  = Employee::with('department');

        if (!empty($request->get('name'))) {
            $employees  = $employees->where('name', 'like', '%' . $request->get('name') . '%');
        }
        if ($request->department != '0') {

            $employees      =   $employees->where('department_id', $request->department);
        }
        $employees->where('user_id', null);
        $data['itemsCount']   =   $employees->count();
        $data['data']         =   $employees->limit($pageSize)->offset(($pageIndex - 1) * $pageSize)->get();

        return json_encode($data);
    }

    private static function generatePassword(): String
    {
        $length         = rand(8, 10);
        $alphabet       = '1234567890abcdefghijklmnopqrstuvwxyz12345678901234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass           = array();
        $alphaLength    = strlen($alphabet) - 1;

        for ($i = 0; $i < $length; $i++) {
            $n      = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }

    public function sendPassword($user)
    {
        $data['user'] = $user;
        $data['link'] = route('dashboard');

        Mail::send('email.password', $data, function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Ems Account Created');
        });
    }

    public function updateOfficeEmail(OfficeEmailRequest $request)
    {
        $user       = User::withTrashed()->where('email', $request->email)->first();
        $employee   = Employee::withoutGlobalScopes(['guest'])->find($request->id);

        if (empty($user)) {
            $user                    =  new User();
            $user->name              =  $employee->name;
            $user->email             =  strtolower($request->email);
            $password                =  $this::generatePassword();
            $user->password          =   \Hash::make($password);
            $user->is_active         =  "1";
            $user->save();
            $user->rawPassword       = $password;
            //assiging employee role

            $this->sendPassword($user);
            $role    = Role::where('name', 'employee')->first();
            $user->roles()->sync($role->id);

            $employee->user_id       = $user->id;
            $employee->office_email  = strtolower($request->email);
            $employee->save();
            return back()->with('success', 'Email assigned Successfully');
        }
        return back()->with('failure', 'Already Assigned');
    }

    public function officeEmailAlloted()
    {
        // $this->authorize('it',new Equipment());
        $departments            = Department::pluck('name', 'id')->toArray();
        $data['departments']    = json_encode($departments, JSON_HEX_APOS);

        return view('employee.officeEmailUpdate', $data);
    }

    public function allotedOfficeEmailList(Request $request)
    {
        ini_set('max_execution_time', '-1');
        $employees  = Employee::with('department')->whereNotNull('user_id')->with('user')->get();

        if ($request->department != '0') {

            $employees   =   $employees->where('department_id', $request->department);
        }
        return $employees;
    }
    public function updateAllotedOfficeEmail(OfficeEmailRequest $request)
    {
        $user                   = User::find($request->user['id']);
        $user->email            = $request->user['email'];
        $user->save();

        $employee               = $user->employee;
        $employee->office_email = $user->email;
        $employee->save();

        return json_encode($user);
    }

    public function employeeAttendance(Request $request)
    {
        $employee           = Employee::withoutGlobalScopes()->find($request->employee);
        $data['employee']   = $employee;

        return view('attendance.employeeAttendance', $data);
    }

    public function employeeAttendanceList(Request $request)
    {
        $employee            =   Employee::withoutGlobalScopes()->find($request->employee);
        $pageIndex           =   $request->pageIndex;
        $pageSize            =   $request->pageSize;
        $attendance          =   $employee->attendances()->orderBy('attendance_date', 'desc');

        if (!empty($request->status)) {
            $attendance      =   $attendance->where('status', $request->status);
        }

        if (!empty($request->attendance_date)) {
            $attendance      =   $attendance->where('attendance_date', $request->attendance_date);
        }
        $data['itemsCount']  =   $attendance->count();
        $data['data']        =   $attendance->with('employee')->limit($pageSize)->offset(($pageIndex - 1) * $pageSize)->get();

        return json_encode($data);
    }

    public function deleteDocument(Document $document)
    {
        $document->forcedelete();
        return back()->with('success', 'Document Deleted Successfully');
    }

    public function deleteEmployeeDocument(Request $request)
    {

         $employee =  Employee::with('documents')->where('id',$request->employee_id)->first();

         $path = storage_path("app/documents/employee/$request->employee_id/" . $employee->documents[$request->reference]);

        if (File::exists($path)) {

            File::delete($path);
        }
         $documents                       =  $employee->documents;
         $documents[$request->reference]  =  null;
         $documents->save();

         $link                   = route('editEmployee',['employee'=>$employee->id]);

         $data['link']           = $link;
         $subject                = "Document Deleted";
         $message                = " Your  " . $request->reference  . " is deleted .Please Upload it again";
         $email                  =  $employee->user->email;

        send_email("email.action", $data, $subject, $message,$email,null);

        return back()->with('success', 'Document Deleted Successfully');
    }

    public function viewTrash()
    {
        $this->authorize('trash', new User());
        return view("trash.employeeTrashedList");
    }

    public  function trashList(Request $request)
    {
        $this->authorize('trash', new User());
        $pageIndex                      =  $request->pageIndex;
        $pageSize                       =  $request->pageSize;
        $employees                      =  Employee::onlyTrashed()->orderBy('deleted_at', 'desc');
        $data['itemsCount']             =  $employees->count();
        $data['data']                   =  $employees->limit($pageSize)->offset(($pageIndex - 1) * $pageSize)->get();
        return json_encode($data);
    }

    public function restore(Employee $employee, Request $request)
    {
        $employee  = Employee::onlyTrashed()->find($request->employee);
        $this->authorize('restore', $employee);
        $employee->restore();
        return "restored";
    }

    public function forcedelete(Request $request)
    {
        $employee  = Employee::onlyTrashed()->find($request->id);
        $this->authorize('destroy', $employee);
        $employee->forceDelete();
        return "Destroyed";
    }

    public function deactivateEmployee(Request $request)
    {
        $employee   = Employee::withoutGlobalScopes()->find($request->id);

        if ($request->is_active == "true") {
            $employee->is_active = 0;

            if (!empty($employee->user)) {
                $employee->user_id      = null;
                $employee->office_email = null;
                $user                   = $employee->user;
                $user->is_active        = 0;
                $user->save();
            }
        } else {
            $employee->is_active    =   1;
            $it                     =   User::havingRole('IT');
            $message                =   $employee->name . 'Employee Re-activated, Assign Office Email to User ';
            $route                  =   ['name' => 'officeEmailView', 'parameter' => null];
            send_notification($it, $message, $route);
        }
        $employee->save();
        return  $employee;
    }

    public function pendingEmployeeProfile(Request $request)
    {
        $this->authorize('pendingProfile', new Employee());
        $date       =   Carbon::parse('2022-05-14');

        if (empty($request->pending_profile_pic)) {
            $pendingEmployeeProfiles    =    Employee::where('is_active', '1')->with(['department', 'profileReminder' => function ($query) {
                return $query->whereDay('created_at', Carbon::now()->day)->where('sent', 1);
            }])->whereHas('user', function ($query) {
                $query->where('user_type', 'Employee');
            })->where(function ($query) {
                $query->whereDoesntHave('documents')->orWhereDoesntHave('employeeEmergencyContact')->orWhereNull('profile_pic');
            })->get();
            // $personsAddedTheProfilePic = [];
        } else {
            $pendingEmployeeProfiles    =    Employee::where('is_active', '1')->with(['department', 'profileReminder' => function ($query) {
                return $query->whereDay('created_at', Carbon::now()->day)->where('sent', 1);
            }])->whereDoesntHave('draftProfiles',function($drafts) use($date){
                $drafts->where('field_name','profile_pic')->where('updated_at','>=',$date)->where('is_approved',null);
            })->whereHas('user',function($user){
                $user->where('user_type','Employee');
            })->get();
        }
        // $data['personsAddedTheProfilePic']        =   $personsAddedTheProfilePic;
        $data['pendingEmployeeProfiles']        =   $pendingEmployeeProfiles;
        return view('employee.incompleteEmployeeProfile', $data);
    }

    public function editProfile(Request $request, Employee $employee)
    {
        $this->authorize('editProfile', $employee);
        $employee                   = Employee::find(auth()->user()->employee->id);
        $data['employee']           = $employee;
        $data['submitRoute']        = array('updateProfile', $employee->id);
        $list['qualification']      = Qualification::all()->pluck('name', 'id')->toArray();
        $data['list']               = $list;
        $data['employee']           = $employee->load('documents', 'draftProfiles');
        $data['documents']          = $employee->documents;
        $data['draft']              = $employee->draftProfiles;
        $data['person_relations']   = ['Father' => 'Father', 'Mother' => 'Mother', 'Brother' => 'Brother', 'Sister' => 'Sister', 'Husband' => 'Husband', 'Wife' => 'Wife', 'Any Other' => 'Any Other'];

        return view('employee.editProfile', $data);
    }

    public function updateProfile(EmployeeRequest $request)
    {
        $details            = $request->only(['id', 'name', 'phone', 'personal_email', 'birth_date', 'qualification_id', 'join_date']);
        $documents          = $request->only(['aadhaar_number', 'pan_number', 'aadhaar_file', 'pan_file', 'cv', 'cheque', 'asset_policy']);
        $bankDetails        = $request->only(['account_holder', 'bank_name', 'account_no', 'ifsc_code']);
        $emergencyContacts  = $request->only(['person_name', 'person_relation', 'person_contact', 'person_address']);

        $employee   = Employee::where('id', $request->id)->first();

        $employee->name = $request->name;
        $employee->office_email = $request->office_email;
        $employee->gender = $request->gender;
        $employee->save();
        $user =  $employee->user;
        $user->name = $request->name;
        $user->save();

        if ($request->hasFile('profile_pic')) {
            $profile        = EmployeeProfileDraft::where(['employee_id' => $request->id, 'field_name' => 'profile_pic'])->first();
            $imageName      = $this->Image_prefix . Carbon::now()->timestamp . '.' . $request->file('profile_pic')->getClientOriginalExtension();
            $request->file('profile_pic')->move(public_path('upload/employeeimage'), $imageName);
            if (empty($profile)) {
                $profile    = new EmployeeProfileDraft();
            }
            $profile->employee_id   = $request->id;
            $profile->field_name    = 'profile_pic';
            $profile->field_value   = $imageName;
            $profile->is_approved   = null;
            $profile->approved_by   = null;
            $profile->is_file       = 1;
            $profile->save();
        }
        if ($request->hasFile('id_card_photo')) {
            $profile        = EmployeeProfileDraft::where(['employee_id' => $request->id, 'field_name' => 'id_card_photo'])->first();
            $imageName      = $this->Image_prefix . Carbon::now()->timestamp . '.' . $request->file('id_card_photo')->getClientOriginalExtension();
            $request->file('id_card_photo')->move(storage_path('app/documents/employee/'.$employee->id.'/'), $imageName);
            if (empty($profile)) {
                $profile    = new EmployeeProfileDraft();
            }
            $profile->employee_id   = $request->id;
            $profile->field_name    = 'id_card_photo';
            $profile->field_value   = $imageName;
            $profile->is_approved   = null;
            $profile->approved_by   = null;
            $profile->is_file       = 1;
            $profile->save();
        }
        //Step 1
        $currentDetails     = Employee::select('id', 'name', 'qualification_id', 'phone', 'personal_email', 'birth_date', 'join_date')->find($request->id)->setAppends([])->toArray();
        $newDetails         = array_diff($details, $currentDetails);
        if (!empty($newDetails)) {
            foreach ($newDetails as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                $employeeImage  = EmployeeProfileDraft::where(['employee_id' => $request->id, 'field_name' => $key])->first();
                if (empty($employeeImage)) {
                    $employeeImage  = new EmployeeProfileDraft();
                }
                $employeeImage->employee_id     = $request->id;
                $employeeImage->field_name      = $key;
                $employeeImage->field_value     = $value;
                $employeeImage->is_approved     = null;
                $employeeImage->approved_by     = null;
                $employeeImage->save();
            }
        }

        //Step 2
        $currentBankDetails    = BankDetail::select('bank_name', 'account_holder', 'ifsc_code', 'account_no')->where('employee_id', $request->id)->first();

        if (!empty($currentBankDetails)) {
            $currentBankDetails     = $currentBankDetails->toArray();
            $newBankDetails         = array_diff($bankDetails, $currentBankDetails);
        } else {
            $newBankDetails        = $bankDetails;
        }

        if (!empty($newBankDetails)) {
            foreach ($newBankDetails as $key => $value) {
                if (empty($value)) {
                    continue;
                }

                $profile    = EmployeeProfileDraft::where(['employee_id' => $request->id, 'field_name' => $key])->first();
                if (empty($profile)) {
                    $profile = new EmployeeProfileDraft();
                }
                $profile->employee_id   = $request->id;
                $profile->field_name    = $key;
                $profile->field_value   = $value;
                $profile->is_approved   = null;
                $profile->approved_by   = null;
                $profile->save();
            }
        }

        $currentEmergencyContact = EmployeeEmergencyContact::select('person_name', 'person_relation', 'person_contact', 'person_address')->where('employee_id', $request->id)->first();

        if (!empty($currentEmergencyContact)) {
            $currentEmergencyContact    = $currentEmergencyContact->toArray();
            $newEmergencyContacts       = array_diff($emergencyContacts, $currentEmergencyContact);
        } else {
            $newEmergencyContacts             = $emergencyContacts;
        }

        if (!empty($newEmergencyContacts)) {
            foreach ($newEmergencyContacts as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                $profile = EmployeeProfileDraft::where(['employee_id' => $request->id, 'field_name' => $key])->first();
                if (empty($profile)) {
                    $profile = new EmployeeProfileDraft();
                }
                $profile->employee_id = $request->id;
                $profile->field_name  = $key;
                $profile->field_value = $value;
                $profile->is_approved = null;
                $profile->approved_by = null;
                $profile->save();
            }
        }

        //Step 3
        $currentDocumentDetails = Document::select('aadhaar_number', 'aadhaar_file', 'pan_number', 'pan_file', 'cv', 'cheque')->where('employee_id', $request->id)->first();

        if (!empty($currentDocumentDetails)) {
            $currentDocumentDetails = $currentDocumentDetails->toArray();
            $newDocumentDetails     = array_diff($documents, $currentDocumentDetails);
        } else {
            $newDocumentDetails     = $documents;
        }
        if (!empty($newDocumentDetails)) {
            $is_file    = null;
            foreach ($newDocumentDetails as $key => $value) {
                if (empty($value) || $key == 'profile_pic') {
                    continue;
                }

                if ($key == 'aadhaar_file') {
                    $is_file    = 1;
                    $fileName   = $key . Carbon::now()->timestamp . '.' . $request->file('aadhaar_file')->getClientOriginalExtension();
                    $request->file('aadhaar_file')->move(storage_path('app/documents/employee/' . $request->id), $fileName);
                    $value      = $fileName;
                } elseif ($key == 'cheque') {
                    $is_file    = 1;
                    $fileName   = $key . Carbon::now()->timestamp . '.' . $request->file('cheque')->getClientOriginalExtension();
                    $request->file('cheque')->move(storage_path('app/documents/employee/' . $request->id), $fileName);
                    $value      = $fileName;
                } elseif ($key == 'asset_policy') {
                    $is_file    = 1;
                    $fileName   = $key . Carbon::now()->timestamp . '.' . $request->file('asset_policy')->getClientOriginalExtension();
                    $request->file('asset_policy')->move(storage_path('app/documents/employee/' . $request->id), $fileName);
                    $value      = $fileName;
                } elseif ($key == 'pan_file') {
                    $is_file    = 1;
                    $fileName   = $key . Carbon::now()->timestamp . '.' . $request->file('pan_file')->getClientOriginalExtension();
                    $request->file('pan_file')->move(storage_path('app/documents/employee/' . $request->id), $fileName);
                    $value      = $fileName;
                } elseif ($key == 'cv') {
                    $is_file    = 1;
                    $fileName   = $key . Carbon::now()->timestamp . '.' . $request->file('cv')->getClientOriginalExtension();
                    $request->file('cv')->move(storage_path('app/documents/employee/' . $request->id), $fileName);
                    $value      = $fileName;
                }
                $profile        = EmployeeProfileDraft::where(['employee_id' => $request->id, 'field_name' => $key])->first();
                if (empty($profile)) {
                    $profile    = new EmployeeProfileDraft();
                }
                $profile->employee_id   = $request->id;
                $profile->field_name    = $key;
                $profile->field_value   = $value;
                $profile->is_approved   = null;
                $profile->approved_by   = null;
                $profile->is_file   = $is_file;
                $profile->save();
            }
            $user_ids = User::where('email', '<>', 'martha.folkes@theknowledgeacademy.com')->whereHas('roles', function ($query) {
                $query->where('name', 'HR');
            })->pluck('id', 'id')->toArray();
            $message    = "Profile updated by " . $currentDetails['name'];
            $link       = route("draftList");
            send_notification($user_ids, $message, $link);
        }



        return  back()->with('success', 'Data Saved As Draft For Approval');
    }

    public static function removeDocument()
    {
        $documents  = Document::onlyTrashed()->get();
        foreach ($documents as $document) {
            Storage::delete('document/' . $document->filename);
        }
        return 'done';
    }

    public function editDocument(Document $document)
    {
        $data['document']           =   $document;
        $data['submitRoute']        =   "updateDocument";

        return view('employee.document', $data);
    }

    public function updateDocument(Request $request, Document $document)
    {
        $document                   =       Document::find($request->id);

        if ($document->status == 'rejected') {
            $document               =       new Document();
        }

        $reference                  =       Str::random(10);
        $document->reference        =       $reference;
        $document->document_name    =       $request->document_name;
        $filename                   =       Storage::putFile('document', $request->file);
        FileVault::encrypt($filename);
        $filename                   =       str_replace('document/', '', $filename);
        $document->filename         =       $filename . '.enc';
        $document->status           =       "pending";
        $document->save();

        return  back()->with('success', 'Document updated successfully');
    }

    public function draft()
    {

        $drafts = EmployeeProfileDraft::with(['employee.department', 'approver'])->whereNull('approved_by')
            ->orderBy('created_at', 'desc');

        if (request()->has('draft_field')) {

            $drafts->where('field_name',request()->draft_field);
        }

        $data['pendingProfiles']    =   $drafts->groupBy('employee_id')->get();
        $data['draftFields']        =    EmployeeProfileDraft::pluck('field_name','field_name')->toArray();
        return view('employee.draftList', $data);
    }

    public function draft_view(Employee $employee)
    {
        $employee->load(['draftProfiles', 'user']);
        $pending            =   $employee->draftProfiles->whereNull('approved_by');

        if ($pending->isEmpty()) {
            return redirect()->route('draftList')->with('No Draft Available');
        }

        $data['employee']   =   $employee;
        $data['drafts']     =   $pending;

        return view('employee.draftShow', $data);
    }

    public function draft_action(Request $request)
    {
        if ($request->ajax()) {
            $employee                       =   Employee::with('bankdetail', 'documents')->find($request->employee);
            $draft                          =   EmployeeProfileDraft::find($request->draft);
            if ($request->has('is_approved')) {
                $draft->is_approved         =   $request->is_approved =='true' ? 1 : 0;
                $draft->approved_by         =   auth()->user()->employee->id;
                $data                       =   array();
                $link = route("editProfile", ['employee' => $employee->id]);
                if ($request->is_approved == "true") {
                    if (in_array($draft->field_name, ['bank_name', 'account_no', 'ifsc_code', 'account_holder'])) {
                        $bankdetail         =   $employee->bankdetail;
                        if (empty($bankdetail)) {
                            $bankdetail     = new BankDetail();
                            $bankdetail->employee_id = $employee->id;
                        }
                        $field              =   $draft->field_name;
                        $old_value          =   $bankdetail->$field;
                        $bankdetail->$field =   $draft->field_value;
                        $draft->old_value   =   $old_value;
                        $bankdetail->save();
                    } elseif (in_array($draft->field_name, ['aadhaar_number', 'aadhaar_file', 'pan_number', 'pan_file', 'cv', 'cheque', 'asset_policy'])) {
                        $document           = $employee->documents;
                        $field              =   $draft->field_name;

                        if (empty($document)) {
                            $document               = new Document();
                            $document->employee_id  = $employee->id;
                        } elseif (!empty($document->$field) && in_array($draft->field_name, ['aadhaar_file', 'pan_file', 'cv', 'cheque', 'asset_policy'])) {
                            $old_file       = $document->$field;
                        }
                        $old_value          =   $document->$field;
                        $document->$field   =   $draft->field_value;
                        $draft->old_value   =   $old_value;
                        $document->save();
                    } elseif (in_array($draft->field_name, ['person_name', 'person_relation', 'person_contact', 'person_address'])) {
                        $emergencyContact   = $employee->employeeEmergencyContact;
                        $field              = $draft->field_name;
                        if (empty($emergencyContact)) {
                            $emergencyContact               = new EmployeeEmergencyContact();
                            $emergencyContact->employee_id  = $employee->id;
                        }
                        $field                      =   $draft->field_name;
                        $old_value                  =   $emergencyContact->$field;
                        $emergencyContact->$field   =   $draft->field_value;
                        $draft->old_value           =   $old_value;
                        $emergencyContact->save();
                    } elseif ($draft->field_name == 'profile_pic') {
                        $employee->profile_pic  = $draft->field_value;
                        $employee->save();
                    } else {
                        $field              =   $draft->field_name;
                        $old_value          =   $employee->$field;
                        $employee->$field   =   $draft->field_value;
                        $draft->old_value   =   $old_value;
                    }
                    $data['status']     = 'approved';
                    if (Str::after($draft->field_name, '_') == 'id') {
                        $draft->field_name  = ucfirst(Str::before($draft->field_name, '_'));
                    }
                    $message    =  'Your ' . $draft->field_name . ' has been approved';
                    send_notification([$request->user_id], $message, $link);
                } elseif ($request->is_approved == "false") {
                    if (Str::after($draft->field_name, '_') == 'id') {
                        $draft->field_name  = ucfirst(Str::before($draft->field_name, '_'));
                    }
                    $data['status']     = 'rejected';
                    $data['name']       = $employee->name;
                    $email              = $employee->office_email;
                    $subject            = $draft->field_name . ' has been rejected';
                    $data['field_name'] = $draft->field_name;
                    $data['message']    = $draft->field_name;
                    $data['link']       = route('login');
                    if (in_array($draft->field_name, ['aadhaar_file', 'pan_file', 'cv', 'cheque', 'asset_policy'])) {
                        $old_file       = $draft->field_value;
                    }
                    $message            = !empty($request->comment) ? $request->comment : null;
                    $data['remarks']    = $message;
                    $user = $employee->user;
                    $to   = $user->email;

                    $message            = (new Action($user, $data, $subject, 'email.profileRejected'))->onQueue('emails');
                    $this->mailer->to($to)->later(Carbon::now(), $message);

                    send_notification([$request->user_id], 'Your ' . $draft->field_name . ' has been rejected', $link);
                }
                $draft->save();
                $employee->save();

                if (isset($old_file)) {
                    if (\Storage::exists("documents/employee/$employee->id/$old_file")) {
                        \Storage::delete("documents/employee/$employee->id/$old_file");
                    }
                }
                return $data;
            }
        }
        abort(403);
    }

    // public function saveLeaveBalance($user,$contract_date)
    // {
    //   //   dd($contract_date);

    //        $date=Carbon::parse($contract_date)->format('d');
    //        $leaveBalance          =  new LeaveBalance();
    //        $leaveBalance->user_id = $user->id;
    //        $leaveBalance->balance = 1.25;
    //         if($date>15)
    //         {
    //             $date=Carbon::parse($contract_date)->addMonths(2)->startOfMonth();

    //         }
    //         else{

    //             $date=Carbon::parse($contract_date)->addMonth()->startOfMonth();

    //         }
    //         $leaveBalance->month=$date;
    //         $leaveBalance->save();


    // }
    public function updateDepartment()
    {

        $data['employees']          =   Employee::pluck('name', 'id');
        $data['departments']        =   Department::pluck('name', 'id');

        return view('employee.departmentChange', $data);
    }

    public function changeDepartment(Request $request)
    {
        $employee                   =   Employee::find($request->id);
        $department_id              =   $employee->department->id;
        return $department_id;
    }

    public function assignDepartment(Request $request)
    {

        $employee                   =   Employee::find($request->employee);
        $employee->department_id    =   $request->department;
        $employee->save();
        return  back()->with('succes', 'Department Updated');
    }

    public function sendReminder(Employee $employee)
    {
        $this->send_email_reminder($employee);
    }

    public function scheduleReminder()
    {
        $employees      =    Employee::whereDoesntHave('documents')->orWhereDoesntHave('employeeEmergencyContact')->orWhereNull('profile_pic')->where('is_active', '1')->get();

        $employees      =   $employees->where('office_email', '!=', 'martha.folkes@theknowledgeacademy.com');
        foreach ($employees as $employee) {

            // if($employee->office_email=='martha.folkes@theknowledgeacademy.com') // not to send email to martha
            // {

            //     continue;
            // }
            $this->send_email_reminder($employee);
        }
    }
    private function send_email_reminder($employee)
    {
        $subject                   =     'Profile Incomplete';
        $message                   =     'Please complete your pending profile';
        $email                     =     $employee->office_email;
        $data['employee']          =     $employee;
        $link                      =     ['name' => 'editEmployee', 'parameter' => $employee->id];
        $profileReminder           =     PendingProfileReminder::create(['employee_id' => $employee->id, 'sent' => 0]);
        send_notification([$employee->user_id], $message, $link);
        send_email("email.profileUpdateReminder", $data, $subject, $message, array($email), null);
        $profileReminder->sent     =     1;
        $profileReminder->update();
    }

    public function exitList()
    {
        $this->authorize('hrNoDuesApprover', new Employee());
        $data['employees']  =   Employee::withoutGlobalScopes()->has('employeeExitDetail')->with('employeeExitDetail')->get();
        return view('employee.exitList', $data);
    }

    public function exitForm()
    {
        $this->authorize('hrNoDuesApprover', new Employee());
        $data['departments']    = Department::pluck('name', 'id')->toArray();
        $data['employees']      =  Employee::withoutGlobalScopes()->whereHas('user', function ($query) {
            $query->where('is_active', '1')->where('user_type', '<>', 'External');
        })->pluck('office_email', 'office_email');

        return view('employee.exitForm', $data);
    }

    public function getEmployees($department_id)
    {
        $data   = Employee::where('department_id', $department_id)->pluck('name', 'office_email')->toArray();
        return $data;
    }

    public function getEmployeeDetail($email)
    {

        $data['employee']   =   Employee::where('office_email', $email)->first();
        return view('employee.employeeDetailFragment', $data);
    }

    public function noDuesInitiate(Request $request)
    {
        $this->authorize('hrNoDuesApprover', new Employee());

        $employee                           =    Employee::where('office_email', $request->employee_id)->first();
        $employeeExitDetail                 =    new EmployeeExitDetail();
        $employeeExitDetail->employee_id    =    $employee->id;
        $employeeExitDetail->reason         =    $request->reason;
        $employeeExitDetail->exit_date      =    $request->exit_date;
        $employeeExitDetail->action_by      =    auth()->user()->id;
        $employeeExitDetail->save();

        $user                               =    User::find($employee->user_id);
        $user->is_active                    =    0;
        $user->save();

        $employee->update(['is_active' => 0]);
        $approver_ids                       =    User::havingRole('No Dues Approver');
        $link                               =    route("noDuesRequests");
        $message                            =    'Employee no dues requested.';
        send_notification($approver_ids, $message, $link);

        return redirect()->route('exitList')->with('success', 'No Dues Intiated.');
    }

    public function uploadExperience(Request $request)
    {
        $this->authorize('hrNoDuesApprover', new Employee());
        $employee       =   Employee::withoutGlobalScopes()->with('employeeExitDetail')->find($request->employee_id);
        if (request()->file('experience_file')) {
            $fileName   =   'experience_file' . Carbon::now()->timestamp . '.' . $request->file('experience_file')->getClientOriginalExtension();
            $request->file('experience_file')->move(storage_path('app/documents/employee/' . $request->employee_id), $fileName);

            $employee->employeeExitDetail->update(['experience_file' => $fileName]);
        }

        return redirect()->back()->with('success', 'Experience Uploaded');
    }

    public function noDuesRequests()
    {
        if (!auth()->user()->can('hrNoDuesApprover', new Employee()) && !auth()->user()->can('itNoDuesApprover', new Employee()) && !auth()->user()->can('managerNoDuesApprover', new Employee())) {
            abort(403);
        }

        $managerIds     = User::havingRole('manager');
        $employees      = Employee::withoutGlobalScopes();

        if (in_array(auth()->user()->id, $managerIds) && !auth()->user()->hasRole('HR') && auth()->user()->employee->department->name != 'IT') {
            $user                   =       auth()->user();
            $managerDepartmentId    =       $user->employee->managerDepartments->pluck('id')->toArray();
            $employees->where('department_id', $managerDepartmentId);
        }
        $data['employees']  = $employees->whereHas('employeeExitDetail', function ($query) {
            $query->whereNull('dept_no_due')->orWhereNull('it_no_due')->orWhereNull('hr_no_due');
        })
            ->with('employeeExitDetail')->get()->sortBy('employeeExitDetail.exit_date');
        $data['actions']    = ['0' => 'Pending', '1' => 'Completed'];
        return view('employee.noDuesRequests', $data);
    }

    public function noDuesSubmit(Request $request, $employee)
    {
        $employee   = Employee::withoutGlobalScopes()->with('employeeExitDetail')->find($employee);
        if (request()->has('dept_no_due')) {
            $employee->employeeExitDetail->update(['dept_no_due' => $request->dept_no_due]);
        }
        if (request()->has('it_no_due')) {
            $employee->employeeExitDetail->update(['it_no_due' => $request->it_no_due]);
        }
        if (request()->has('hr_no_due')) {
            $employee->employeeExitDetail->update(['hr_no_due' => $request->hr_no_due]);
            if ($employee->user->user_type == 'Office Junior') {
                $employee->employeeExitDetail->update(['dept_no_due' => 1]);
                $employee->employeeExitDetail->update(['it_no_due' =>  1]);
            }
        }

        return redirect()->back()->with('success', 'Submitted Successfully.');
    }

    // set birthday ReadOn
    function setBirthdayReadOn(Request $request)
    {
        Employee::find($request->id)->update(['birthday_reminder' => '1']);
    }

    function resetBirthdayReadOn()
    {
        Employee::where('birthday_reminder', '1')->update(['birthday_reminder' => null]);
    }

    public function showRecentJoinedUser(Request $request)
    {

        $this->authorize('hrEmployeeList', new Employee());
        $dateFrom           =       Carbon::now()->startOfMonth();
        $dateTo             =       Carbon::now()->endOfMonth();

        if (!empty($request->dateFrom)) {
            $dateFrom       =       $request->dateFrom;
        }
        if (!empty($request->dateTo)) {
            $dateTo         =       $request->dateTo;
        }

        $data['employees']  =       Employee::with('department')->whereDate('join_date', '>=', $dateFrom)
            ->whereDate('join_date', '<=', $dateTo)->get();

        return view('user.recentUsers', $data);
    }

    public function getEmail($employee_id)
    {
        $employee       = Employee::find($employee_id);
        return $employee->office_email;
    }

    public function onboardDashboard(Request $request)
    {
        ini_set('max_execution_time', "-1");
        ini_set("memory_limit", "-1");
        $employee                         =   Employee::pluck('onboard_status', 'onboard_status')->toArray();
        $askedForDocuments                =   Employee::withoutGlobalScope('guest')->where('onboard_status', 'asked for documents')->orderBy('created_at', 'desc');
        $documentSubmittedEmployees       =   Employee::withoutGlobalScope('guest')->with('user')->where('onboard_status', 'documents submitted')->orderBy('created_at', 'desc');
        $contractPending                  =   Employee::withoutGlobalScope('guest')->where('onboard_status', 'contract pending')->orderBy('created_at', 'desc');
        $onboardEmployees                 =   Employee::withoutGlobalScope('guest')->with('user')->whereIn('onboard_status', ['onboard', 'training'])->orderBy('created_at', 'desc');
        $employees  =   Employee::all();

        if (request()->has('email')) {

            $askedForDocuments           =  $askedForDocuments->where('email', 'like', '%' . $request->email . '%');
            $contractPending             =  $contractPending->where('personal_email', 'like', '%' . $request->email . '%');
            $onboardEmployees            =  $onboardEmployees->where('personal_email', 'like', '%' . $request->email . '%');
            $documentSubmittedEmployees  =  $documentSubmittedEmployees->where('person_email', 'like', '%' . $request->email . '%');
        }
        $data['onboardStatuses']                                    =   ['Interviewed', 'Selected'];

        if ($request->ajax()) {
            $count                                                  = !empty(request()->count) ? request()->count : 3;
            $render['totalAskedForDocuments']                       =  $askedForDocuments->count();
            $render['totalContractPendingEmployees']                =  $contractPending->count();
            $render['totalOnboardEmployees']                        =  $onboardEmployees->count();
            $render['totalDocumentSubmittedEmployees']              =  $documentSubmittedEmployees->count();
            $render['contractPendings']                             =  $contractPending->limit($count)->get();
            $render['onboardEmployees']                             =  $onboardEmployees->limit($count)->get();
            $render['askedForDocuments']                            =  $askedForDocuments->limit($count)->get();
            $render['documentSubmittedEmployees']                   =  $documentSubmittedEmployees->limit($count)->get();

            if ($request->has('employee_load')) {

                return $this->loadMoreEmployees($render);
            }

            $employees['askedForDocuments']                         =   view('onboard.askedForDocuments', $render)->render();
            $employees['documentSubmittedEmployees']                =   view('onboard.documentSubmittedEmployees', $render)->render();
            $employees['contractPending']                           =   view('onboard.contractPending', $render)->render();
            $employees['onboardEmployees']                          =   view('onboard.onboardEmployees', $render)->render();

            return $employees;
        }
        return view('onboard.dashboard', $data);
    }

    private function loadMoreEmployees($render)
    {

        switch (request()->employee_load) {

            case 'askedForDocuments':
                return  view('onboard.askedForDocuments', $render)->render();
                break;
            case 'documentSubmittedEmployees':
                return  view('onboard.documentSubmittedEmployees', $render)->render();
                break;
            case 'contractPending':
                return  view('onboard.contractPending', $render)->render();
                break;
            case 'waitingDocumentEmployees':
                return  view('onboard.waitingDocumentEmployees', $render)->render();
                break;
            case 'documentSubmittedEmployees':
                return  view('onboard.documentSubmittedEmployees', $render)->render();
                break;
            default:
                break;
        }
    }
    public function  onboardStatusUpdateForm(Request $request)
    {
        if ($request->type == "interview") {

            $data['employee']       = Interview::firstWhere('id', $request->id);
        } else {

            $data['employee']       = Employee::withoutGlobalScope('guest')->firstWhere('id', $request->id);
        }

        $data['type']               =   $request->type;
        $data['onboard_statuses']   =   config('employee.onboard_status');
        $data['action']             =   ['1' => 'Selected', '0' => 'Rejected'];
        $view = view('onboard.statusUpdateForm', $data)->render();
        return $view;
    }
    public function barcode(Request $request)
    {
        $path = storage_path("app/documents/employee/$request->id/" . $request->barcode);

        if (!File::exists($path)) {

            abort(404);
        }

        $file               =   File::get($path);
        $type               =   File::mimeType($path);
        $response           =   Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function onboardStatusUpdate(Request $request)
    {
        switch ($request->type) {
            case 'interview':
                $interview                  =  Interview::find($request->id);
                $interview->is_selected     =   $request->is_selected;
                $interview->save();
                if ($request->is_selected) {
                    $interviewController    = new InterviewController();
                    $interviewController->createCredentials($interview);
                }
                break;
            case "document submitted":
                $employee                   =   Employee::withoutGlobalScope('guest')->find($request->id);
                $employee->onboard_status   =   'onboard';
                $employee->save();
            default:
                break;
        }
        return back()->with('success', 'onboard status changed');
    }


    public function onboardForm(Request $request)
    {
        $data['employee']           =       Employee::withoutGlobalScopes(['guest', 'is_active'])->findOrFail($request->id);
        $data['submitRoute']        =       'updateEmployee';
        $data['shifts']             =       ShiftType::pluck('name', 'id')->toArray();
        $data['departments']        =       Department::pluck('name', 'id')->toArray();
        $data['onboardStatus']      =       ['onboard' => 'onboard', 'leaver' => 'leaver', 'fired' => 'fired', 'training' => 'training'];
        return view('employee.onBoardForm', $data);
    }

    public function sendDocumentLink(Request $request)
    {
        $employee                   =  Employee::withoutGlobalScope('guest')->with('user')->find($request->id);
        $user                       = $employee->user;
        $data['link']               = route('predetails.create');
        $data['user']               = $user;
        Mail::send('email.documentlink', $data, function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Document Link');
        });

        $employee->onboard_status   = "waiting on document";
        $employee->save();
        return back()->with('success', 'Document Link Send Successfully');
    }

    public function sendDocumentReminder(Request $request)
    {
        $employee                   =  Employee::withoutGlobalScope('guest')->with('user')->find($request->id);
        $user                       = $employee->user;
        $data['link']               = route('onboardForm', ['id' => $employee->id]);
        $data['user']               = $user;
        $to                         = $user->email;
        $data['subject']            = 'Documents Fill Reminder';
        $data['message']            = $data['subject'];
        $message                    = (new Action($user, $data, 'Documents Fill Reminder', 'email.employeeSelected'))->onQueue('emails');
        $this->mailer->to($to)->later(Carbon::now()->addSeconds(10), $message);

        return back()->with('success', 'Document Reminder Send Successfully');
    }

    public function barcodeList(Request $request)
    {
        $this->authorize('barcodeListView', new Employee());
        $employees                     =    Employee::whereHas('user', function ($user) {
                                                $user = $user->where('user_type', 'Employee');
                                            });
        $employeeNames                  =   Employee::get();

        if (request()->has('department_id')) {
            $employees                 =    $employees->where('department_id', $request->department_id);
            $employeeNames             =    $employeeNames->where('department_id', $request->department_id);
        }
        if (request()->has('name')) {
            $employees                 =    $employees->where('name', $request->name);
        }
        if(request()->has('id_card'))
        {
            $employees                 =    $employees->where('id_card','<>',null );
        }
        $data['department_id']         =    Department::pluck('name', 'id')->toArray();
        $data['names']                 =    $employeeNames->pluck('name', 'name')->toArray();
        $data['employees']             =    $employees->paginate(20);

        return view('employee.barcodeList', $data);
    }


    public function storeIdCard(Request $request )
    {
        $employee       = Employee::findOrFail($request->id);

        if($request->has('id_card'))
        {
            $idCard      = $this->Image_prefix . Carbon::now()->timestamp . '.' . $request->file('id_card')->getClientOriginalExtension();
            $request->file('id_card')->move(storage_path('app/documents/employee/'.$employee->id), $idCard);

            $employee['id_card']      =   $idCard;
            $employee->save();
        }

        return redirect()->back()->with('success','Id Card Uploaded');
    }

    public function getBarCodeImage(Request $request)
    {
        ini_set('max_execution_time', -1);
        $employee = Employee::find($request->employee_id);

        // $fileName = 'barcode/' . $employee->name . '.jpeg';
        // //   dd($fileName);
        // Browsershot::html($request->html)->save($fileName);
        // $image = Response::download($fileName);
        // //  if (File::exists($fileName)) {
        // //     //File::delete($image_path);
        // //     unlink($fileName);
        // // }
        // return $image;


            $employee          =    Employee::find($request->employee_id);
            $generatorPNG      =    new BarcodeGeneratorPNG();
            $content           =    $generatorPNG->getBarcode("$employee->biometric_id", $generatorPNG::TYPE_CODE_128);


            $data = $content;
            $file = $employee->aadhaar_name. '.png';
            $destinationPath = storage_path() . "/documents/$employee->user_id/";
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            File::put($destinationPath . $file, $data);

            return Response::download($destinationPath.$file);
    }


}
