<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class UpdateAdminRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_name'=>'required|max:20|unique:user_name',
            'true_name'=>'required|max:32',
            'password'=>'required|max:60',
            'password_confirmation'=>'confirmed',
            'last_login_ip'=>'required',
            'last_login_time'=>'required|date',
            'created_at'=>'required|date',
            'updated_at'=>'required|date'
        ];
    }
}
