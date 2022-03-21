<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class EmployeeExport implements FromCollection,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $is_active;

    function __construct() {
         
    }
    public function collection()
    {
        $employees=Employee::withoutGlobalScopes()->with('user','department','qualification','bankdetail','designation');
     
        if(request()->has('name'))
        {
            $employees   = $employees->where('name',request()->name);
        } 
        if(request()->has('office_email'))
        {
            $employees    = $employees->where('office_email',request()->office_email);
        }    
        if(request()->has('status'))
        {
            if(request()->status == 'active'){
                $employees    = $employees->where('is_active',1);
            }else{
                $employees    = $employees->where('is_active',0);
            }
            
        }    
        if(request()->has('department_id'))
        {
            $employees    = $employees->where('department_id',request()->department_id);
        } 
        return $employees->get();
    }
    public function map($employee): array
    {
        return [
        
            $employee->registration_id,
            $employee->name,
            optional($employee->user)->email,
            optional($employee->department)->name,
            $employee->join_date,
            $employee->phone,
            $employee->birth_date,
            optional($employee->qualification)->name,
            optional($employee->designation)->name,
            $employee->personal_email,
            $employee->pf_no,
            optional($employee->bankdetail)->bank_name,
            optional($employee->bankdetail)->account_holder,
            optional($employee->bankdetail)->ifsc_code,
            optional($employee->bankdetail)->account_no ,
            optional($employee->employeeEmergencyContact)->person_name,
            optional($employee->employeeEmergencyContact)->person_relation,
            optional($employee->employeeEmergencyContact)->person_contact,
            optional($employee->employeeEmergencyContact)->person_address,
            
        ];
    }
    public function headings() : array{
        return [
                'Employee Id',
                'Name',
                'Email',
                'Department',
                'Joining Date',
                'Phone',
                'Date of Birth',
                'Qualification',
                'Designation',
                'Personal Email',
                'Pf_no',
                'Bank Name',
                'Account Holder',
                'Ifsc Code',
                'Account No', 
                'Emergency Contact Name',
                'Emergency Contact Relation',
                'Emergency Contact Number',
                'Address'
           
        
        ];
    }
}
