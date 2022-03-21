<?php

namespace App\Rules;
use App\Models\Employee;
use Illuminate\Contracts\Validation\Rule;

class Common implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        
        $employee_id=request()->id;
        $employees=Employee::withoutGlobalScope('is_active')->get();
        $employee=$employees->where('id',$employee_id)->first();   
        if(!empty($employee) && $employee[$attribute]==$value)
        {
            return true;
        }   
        $employees=$employees->pluck($attribute,'id')->toArray();
       
        foreach($employees as $employee)
        {
          
            if($employee==$value)
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute must be unique';
    }
}
