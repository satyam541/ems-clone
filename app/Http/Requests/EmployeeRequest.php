<?php

namespace App\Http\Requests;
use App\Rules\Common;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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

           'name'                           => ['required', 'string', 'max:50'],
        //    'personal_email'                 => ['sometimes', 'string', 'email', 'max:50',new Common,'regex: /\S+@\S+\.\S+/'],
        //    'phone'                          => ['sometimes','numeric', new Common],
           'department_id'                  => ['sometimes','numeric'],
           'qualification_id'               => ['sometimes'],
           'aadhaar_file'                   => ["mimetypes:application/pdf" ,"max:2000"],
           'profile_pic'                    => ['image',"max:2000"],
           'pan_file'                       => ["mimetypes:application/pdf","max:2000"],
           'cv'                             => ["mimetypes:application/pdf","max:2000"],
           'birth_date'                     =>'sometimes',

        ];
    }
    public function messages()
    {
        return [

            'aadhaar_file.mimes'            => 'Aadhaar Card must be type of pdf ',
            'pan_file'                      => 'Pan File must be type of pdf',
            'cv'                            => 'CV  must be type of pdf',
            'birth_date.required'           => 'Date of Birth is required',
            'aadhaar_file.max'              => 'Aadhaar Card must be 2MB!',
            'pan_file.max'                  => 'Pan File must be 2MB!',
            'cv.max'                        => 'CV must be 2MB!',
            'profile_pic.max'               => 'Profile Pic must be 2MB'
        ];
    }
    public function attributes()
    {
        return [
                'aadhaar_file' => 'Aadhaar File',
				'pan_file' => 'Pan File',
                'cv'    =>'CV',        
				
        ];

    }
}
