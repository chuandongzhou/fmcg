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
            'user_name' => 'required_with:password|digits:6|unique:delivery_man,user_name,' . $deliveryMan->id,
            'password' => 'required_with:user_name|digits_between:6,18|confirmed',
            'pos_sign' => 'min:6',
        ];
    }
}
