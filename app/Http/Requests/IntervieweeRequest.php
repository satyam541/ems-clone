<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IntervieweeRequest extends FormRequest
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
            'first_name'    =>['required','min:2','max:40','string'],
            'resume'        =>['required','mimes:pdf'],
            'phone'         =>['required','numeric','min:6999999999','max:9999999999'],
            'qualification_id' =>['required']
        ];
    }
}
