<?php

namespace App\Rules;

use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AllowedLeaves implements Rule
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
        $type = request()->get('type');
        $from_date = request()->get('from_date');
        $to_date = request()->get('to_date');
        $duration = request()->get('duration');
        $employee_department_id = Auth :: user()->employee->department_id;
        if ($type != 'Short Leave') {   //check as per number of allowed leaves in a day.

            $department_employee_count = Employee :: where('department_id', $employee_department_id)
                                        ->count();

            $query = Leave :: whereHas('employee.department', function(Builder $query) use 
                            ($employee_department_id) {
                            $query->where('id', $employee_department_id);
                        })
                        ->whereDate('from_date', '<=', $from_date)
                        ->whereDate('to_date', '>=', $from_date)
                        ->orWhereDate('from_date', '<=', $to_date)
                        ->whereDate('to_date', '>=', $to_date)
                        ->where([['type', $type],['duration', $duration]]);
                        
            $existing_leaves = $query->count();
            
            $percent = ($existing_leaves/$department_employee_count) * 100;
            $allowed_percent = config('constants.leaves_per_department');
            
            return $percent < $allowed_percent;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The maximum allowed leaves in selected dates has already been taken. Please choose another dates/type.';
    }
}
