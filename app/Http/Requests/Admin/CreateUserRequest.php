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
            'user_name' => 'required|between:4,8|unique:user',
            'password' => 'required|min:6|confirmed',
            'nickname' => 'required|unique:user',
            'province_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'street_id' => 'required|numeric',
            'address' => 'required|max:45',
            'group' => 'required|in:1,2',
            'status' => 'in|0,1'
        ];
    }
}
