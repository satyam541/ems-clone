<?php

namespace App\Http\Requests;
use App\Rules\Common;
use App\Rules\CheckEmail;
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
        $employee = empty(request()->id) ? 0 : request()->id;
        return [

           'name'                           => ['required', 'string', 'max:50'],
           'office_email'                     =>   [new CheckEmail,'required','sometimes'],
        //    'personal_email'                 => ['sometimes', 'string', 'email', 'max:50',new Common,'regex: /\S+@\S+\.\S+/'],
        //    'phone'                          => ['sometimes','numeric', new Common],
        //    'department_id'                  => ['sometimes','numeric'],
           'qualification_id'               => ['sometimes'],
           'aadhaar_file'                   => ["mimetypes:application/pdf" ,"max:2000"],
           'profile_pic'                    => ['image',"max:5000"],
           'pan_file'                       => ["mimetypes:application/pdf","max:5000"],
           'cv'                             => ["mimetypes:application/pdf","max:2000"],
           'passport'                       => ["mimetypes:application/pdf","max:2000"],
           'asset_policy'                       => ["mimetypes:application/pdf","max:2000"],
        //    'cheque'                         => ["mimetypes:application/pdf","max:2000"],
           'birth_date'                     =>'sometimes',
           'biometric_id'                   => 'required|nullable|sometimes|unique:employee,biometric_id,'.$employee.',id',
           'gender'                         => ['required'],
           'contract_date'                  => ['required'],
           'shift_type_id'                  => ['required'],
        ];
    }
    public function messages()
    {
        return [

            'aadhaar_file.mimetypes'        => 'Aadhaar Card must be type of pdf ',
            'cheque.mimetypes'              => 'Cancel Cheque/Passbook must be type of pdf ',
            'pan_file'                      => 'Pan File must be type of pdf',
            'cv'                            => 'CV  must be type of pdf',
            'birth_date.required'           => 'Date of Birth is required',
            'aadhaar_file.max'              => 'Aadhaar Card must be 2MB!',
            'cheque.max'                    => 'Cancel Cheque/Passbook must be 2MB!',
            'pan_file.max'                  => 'Pan File must be 2MB!',
            'cv.max'                        => 'CV must be 2MB!',
            'profile_pic.max'               => 'Profile Pic must be 2MB',
            'passport'                      => 'Passport must be 2MB',
            'asset_policy'                      => 'Passport must be 2MB',
            'biometric_id'                  => 'Biometric ID must be Unique',
        ];
    }
    public function attributes()
    {
        return [
                'aadhaar_file'  => 'Aadhaar File',
				'pan_file'      => 'Pan File',
                'cv'            => 'CV',

        ];

    }
}
