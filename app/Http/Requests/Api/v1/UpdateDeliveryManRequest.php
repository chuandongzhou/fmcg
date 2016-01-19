<?php

namespace App\Http\Requests\Api\v1;


class UpdateDeliveryManRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $deliveryMan = $this->route('delivery_man');
        return [
            'name' => 'required',
            'phone' => 'required|numeric|digits_between:7,14',
            'user_name' => 'digits:6|unique:delivery_man,user_name,' . $deliveryMan->id,
            'password' => 'min:6|confirmed',
            'pos_sign' => 'min:6',
        ];
    }
}
