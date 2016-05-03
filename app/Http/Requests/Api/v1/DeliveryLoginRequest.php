<?php

namespace App\Http\Requests\Api\v1;


class DeliveryLoginRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_name' => 'required|numeric|size:6',
            'password' => 'required',
        ];
    }
}
