<?php

namespace App\Http\Requests\Api\v1;


class DeliveryUpdateOrderRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'num' => 'required|integer|min:1',
        ];
    }
}
