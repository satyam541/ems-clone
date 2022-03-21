<?php

namespace App\Rules;

use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class LeaveTaken implements Rule
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
        $date_exists = Leave :: where('employee_id', Auth :: user()->employee->id)
                    ->whereDate('from_date', '<=', $value)
                    ->whereDate('to_date', '>=', $value)
                    ->exists();
        return !$date_exists;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You have already taken leave on selected dates.';
    }
}
