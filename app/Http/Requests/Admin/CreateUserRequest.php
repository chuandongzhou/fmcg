<?php

namespace App\Http\Requests\Admin;


class CreateUserRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_name' => 'required|alpha_dash|between:4,16|unique:user',
            'password' => 'required|min:6|confirmed',
            'backup_mobile' => ['required', 'regex:/^(0?1[0-9]\d{9})$|^((0(10|2[1-9]|[3-9]\d{2}))-?[1-9]\d{6,7})$/'],
            'type' => 'required|in:1,2,3',
            'status' => 'in|0,1'
        ];
    }
}
