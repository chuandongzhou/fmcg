<?php

namespace App\Http\Requests\Api\v1;


class UpdateChildUserRequest extends UserRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'between:6,18|confirmed',
            'name' => 'required|max:10',
            'phone' => 'required|numeric|digits_between:7,14',
        ];
    }
}
