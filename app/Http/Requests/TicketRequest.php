<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
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
            'department'       =>     'sometimes|required',
            'category'          =>     'required',
            'subject'           =>     'required',
            'description'       =>     'required',  
            'priority'          =>     'required',  
        ];

        if(request()->subject == 'Software')
        {
            $rules += ['remote_id' => 'required'];
        }

        return $rules;
    }
}
