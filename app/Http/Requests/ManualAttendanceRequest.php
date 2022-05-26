<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManualAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'department_id'     => 'required',
            // 'user_id'           => 'required',
            // 'punch_in'          => 'required',
            'punch_date'        => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'department_id' => 'Department',
            'user_id'       => 'User'
        ];
    }
}
