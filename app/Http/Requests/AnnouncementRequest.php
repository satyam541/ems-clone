<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
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
            'title'             =>      'required',
            'start_dt'          =>      'required',
            'end_dt'            =>      'required',
            'start_time'        =>      'required',
            'end_time'          =>      'required',
            // 'is_publish'        =>      'required',
            'description'       =>      'required',
        ];
    }
    public function messages()
    {
        return [
            'title'              =>         'title is required',
            'start_dt'           =>         'start_dt is required',
            'end_dt'             =>         'end_dt is required',
            'start_time'         =>         'start_time is required',
            'end_time'           =>         'end_time is required',
            // 'is_publish'         =>         'is_publish is required',
            'description'        =>         'description is required',
        ];
    }
    
}
