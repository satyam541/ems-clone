<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class ShortLeaveTime implements Rule
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
        $from_date = Carbon :: parse(date('Y-m-d') . request()->get('from_time'));
        $to_date = Carbon :: parse(date('Y-m-d'). request()->get('to_time'));
        $leave_period = $to_date->diffInHours($from_date);

        return $leave_period <= 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The duration of short leave does not exceed 1 hour.';
    }
}
