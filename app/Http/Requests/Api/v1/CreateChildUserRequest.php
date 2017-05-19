<?php

namespace App\Http\Requests\Api\v1;


class CreateChildUserRequest extends UserRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account' => 'required|between:4,18|unique:child_user',
            'password' => 'required|between:6,18|confirmed',
            'name' => 'required|max:10',
            'phone' => 'required|numeric|digits_between:7,14',
        ];
    }
}
