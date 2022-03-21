<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllowanceRequest extends FormRequest
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
          'name'=>['required','string','max:100','min:2'],
          'type'=>['required','string'],
          'arithmetic'=>['required','string'],
          'value'=>['required','numeric']
        ];
    }
}
