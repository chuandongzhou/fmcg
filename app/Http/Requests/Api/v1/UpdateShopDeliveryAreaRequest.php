<?php

namespace App\Http\Requests\Api\v1;


class UpdateShopDeliveryAreaRequest extends UserRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'city_id' => 'required',
            'district_id' => 'required',
            'min_money' => 'numeric'
        ];

    }
}
