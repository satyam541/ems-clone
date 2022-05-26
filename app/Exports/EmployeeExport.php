<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class EmployeeExport implements FromCollection,WithHeadings,WithMapping,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $is_active;
    private $employees;

    function __construct($employees) {
        $this->employees = $employees;
    }
    public function collection()
    {

            //  dd($this->employees);
        // $employees=Employee::withoutGlobalScopes()->with('user','department','qualification','bankdetail','designation');
     
        
        // if(request()->has('name'))
        // {
        //     $employees   = $employees->where('name',request()->name);
        // } 
        // if(request()->has('office_email'))
        // {
        //     $employees    = $employees->where('office_email',request()->office_email);
        // }    
        // if(request()->has('status'))
        // {
        //     if(request()->status == 'active'){
        //         $employees    = $employees->where('is_active',1);
        //     }else{
        //         $employees    = $employees->where('is_active',0);
        //     }
            
        // }    
        // if(request()->has('department_id'))
        // {
        //     if(request()->status == 'exit'){
        //     $employees    = $employees->where('is_active',0)->where('department_id',request()->department_id);
        //     }
        //     else{
        //         $employees    = $employees->where('is_active',1)->where('department_id',request()->department_id);
        //     }
        // } 

        // if (request()->has('shift_type_id')) {

        //     $employees  = $employees->whereHas('user',function ($user){
        //         $user->whereHas('shiftType',function($query){
        //             $query->where('name',request()->shift_type_id);
        //         });
        //     });
        // }

        // if (request()->has('shift_type')) {

        //     $employees  = $employees->whereHas('user',function ($user){
        //         $user->whereHas('shiftType',function($query){
        //             $query->where('name',request()->shift_type);
        //         });
        //     });
        // }

        // return $employees->get();

        return $this->employees;
    }
    public function map($employees): array
    {
        return [
        
            // $employee->registration_id,
            $employees->name,
            $employees->office_email ?? '',
            $employees->biometric_id ?? '',
            $employees->join_date ?? '',
            $employees->user->user_type ?? '',
            $employees->user->off_day ?? '',
            $employees->documents->aadhaar_number ?? '',
            $employees->contract_date ?? '',
            // optional($employee->user)->email,
            // optional($employee->department)->name,
            // $employee->phone,
            // $employee->birth_date,
            // optional($employee->qualification)->name,
            // optional($employee->designation)->name,
            // $employee->personal_email,
            // $employee->pf_no,
            // optional($employee->bankdetail)->bank_name,
            // optional($employee->bankdetail)->account_holder,
            // optional($employee->bankdetail)->ifsc_code,
            // optional($employee->bankdetail)->account_no ,
            // optional($employee->employeeEmergencyContact)->person_name,
            // optional($employee->employeeEmergencyContact)->person_relation,
            // optional($employee->employeeEmergencyContact)->person_contact,
            // optional($employee->employeeEmergencyContact)->person_address,
            $employees->gender,
            $employees->user->shiftType->name."(".$employees->user->shiftType->start_time."-". $employees->user->shiftType->end_time.")" ?? '',
            // $employee->contract_date,
        ];
    }
    public function headings() : array{
        return [
                // 'Employee Id',
                'Name',
                'Tka Email',
                // 'Email',
                'Biometric',
                // 'Department',
                'Joining Date',
                'Employee Type',
                'Off Day',
                'Aadhaar Number',
                'Contract Date',
                // 'Phone',
                // 'Date of Birth',
                // 'Qualification',
                // 'Designation',
                // 'Personal Email',
                // 'Pf_no',
                // 'Bank Name',
                // 'Account Holder',
                // 'Ifsc Code',
                // 'Account No', 
                // 'Emergency Contact Name',
                // 'Emergency Contact Relation',
                // 'Emergency Contact Number',
                // 'Address',
                'Gender',
                'Shift Type',
                // 'Contract Date',
        ];
    }
}
