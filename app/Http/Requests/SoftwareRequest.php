<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SoftwareRequest extends FormRequest
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
        $id = !empty (request()->input('id')) ? intval (request()->input('id')) : NULL;
        return [
            'name' =>['required ','unique:software,name,' . $id . ',id']
        ];
    }
}
