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
        $user = $this->route('user');
        return [
            'user_name' => 'sometimes|required|between:4,16|unique:user,user_name,' . $user->id,
            'password' => 'min:6|confirmed',
            'nickname' => 'required|unique:user,nickname,' . $user->id,
            'province_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'street_id' => 'required|numeric',
            'address' => 'required|max:45',
            'type' => 'required|in:1,2,3',
            'status' => 'sometimes|required|in:0,1'
        ];
    }
}
