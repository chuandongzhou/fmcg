<?php

namespace App\Http\Requests\Admin;


class UpdateUserRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'min:6',
            'nickname' => 'required|unique:user',
            'province_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'street_id' => 'required|numeric',
            'address' => 'required|max:45',
            'group' => 'required|in:1,2',
            'status' => 'sometimes|required|in:0,1'
        ];
    }
}
