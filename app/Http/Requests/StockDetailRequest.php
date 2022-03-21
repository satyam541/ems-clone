<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockDetailRequest extends FormRequest
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
        $id=0;
        if(request()->has('id')){$id=request()->id;}
                $rules= [
            'manufacturer'  =>  'required',
            'model_no'      =>  ['required ','unique:stock_item_details,model_no,' . $id . ',id'],  
            'warranty_from' =>  'required',
            'warranty_till' =>  'required',
            'status'        =>  'required',

        ];
        if(!empty(request()->label))
        {
            $rules+=['label'=> "unique:stock_item_details,label,$id,id"];
        }
        return $rules;
    }
    public function messages()
    {
        return [
            'label.unique'=>'The label has already taken',
       ];
    }
}
