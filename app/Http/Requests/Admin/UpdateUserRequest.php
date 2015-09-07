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
            'user_name' => 'sometimes|required|alpha_dash|between:4,16|unique:user,user_name,' . $user->id,
            'password' => 'min:6|confirmed',
            'nickname' => 'required|unique:user,nickname,' . $user->id,
            'type' => 'required|in:1,2,3',
            'status' => 'sometimes|required|in:0,1'
        ];
    }
}
