<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Module;

class PermissionRequest extends FormRequest
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
     * validation messages
     */
    public function messages()
    {
        return [
            'module_id.required' => 'The module field is required.',
            'module_id.exists' => 'Invalid module.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // $module = Module::where('name', request()->input('module_id'))->first();
        // $module_id = !empty($module) ? $module->id : NULL;
        $module_id = request()->has('module_id') ? request()->input('module_id') : NULL;
        $id = request()->has('id') ? request()->input('id') : NULL;
        
        return [
            'module_id' => ['required', 'exists:module,id'],
            // 'module_id' => ['required'],
            'access' => ['required', 'string', 'max:50', 
                        'unique:permission,access,' . $id . ',id,module_id,' . $module_id],
            'description' => ['nullable', 'string', 'max:500'],
        ];
        
    }
}
