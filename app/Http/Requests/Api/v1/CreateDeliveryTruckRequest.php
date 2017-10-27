<?php

namespace App\Http\Requests\Api\v1;

class CreateDeliveryTruckRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|between:3,16',
            'license_plate' => 'required|unique:delivery_truck|size:7'
        ];
    }
}
