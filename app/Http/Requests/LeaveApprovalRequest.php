<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaveApprovalRequest extends FormRequest
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
        $rules = [
            'status' => ['required', 'in:Pending,Approved,Rejected'],
            // 'status' => ['required', 'in:Approved,Rejected'],
        ];
        if (request()->get('status') == 'Rejected') {
            $rules['reason_rejected'] = ['required', 'string', 'max:500'];
        }
        return $rules;
    }
}
