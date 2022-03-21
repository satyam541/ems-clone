<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\URL;

class IntervieweeApprovedRequest extends FormRequest
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
        
        return  [
            
            'offer_letter'                   => ['required','mimes:pdf'],
            'join_date'                      => ['required','after_or_equal:date'],
            'designation'                    => ['required','min:2','max:50'],  
            'department'                     => ['required','numeric'],  
           
            ];

    }

}
