<?php

namespace App\Http\Requests\Api\v1;


class DeliveryLoginRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_name' => 'required|size:6',
            'password' => 'required',
        ];
    }
}
