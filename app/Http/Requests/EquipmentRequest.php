<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EquipmentRequest extends FormRequest
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
        
        $equipment=request()->route('equipment',0);
        if(!empty($equipment)){
            $rules['employee']          =  'required';  
            $rules['status']            =  'required';
        }
        $rules                  =   array();
        $rules['entity_id']         =  'required';
        $rules['manufacturer']      =  'required';
        $rules['specifications']    =  'sometimes';
       
        if(request()->route('equipment', false))
        {
            $rules['alloted_no']        =  'required';
            $rules['repairs']           =  'sometimes';
          
        }
        else{
            $rules['quantity']          =  ['required','numeric','min:0','max:100'];
        }
        return $rules;
    }
}
