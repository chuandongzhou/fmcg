<?php

namespace App\Http\Requests\Api\v1;


class CreateDeliveryManRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'phone' => 'required|numeric|digits_between:7,14',
            'user_name' => 'digits:6|unique:delivery_man',
            'password' => 'required_with:user_name|digits_between:6,18|confirmed',
            'pos_sign' => 'required_with:user_name|min:6',
        ];
    }
}
