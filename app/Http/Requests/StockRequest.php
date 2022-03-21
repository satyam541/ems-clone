<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
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
            'item_id'           =>  'required',
            'bill'              =>  'required|mimes:pdf',
            'quantity'          =>  'required|numeric',
            'price_per_item'    =>  'required|numeric',
            'total_price'       =>  'required|numeric',
            'purchase_date'     =>  'required',
            'purchase_source'   =>  'required',
        ];
        
        
    }
    
}
