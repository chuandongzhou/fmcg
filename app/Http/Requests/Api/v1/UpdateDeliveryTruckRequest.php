<?php

namespace App\Http\Requests\Api\v1;

class UpdateDeliveryTruckRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $route = $this->route('delivery_truck');
        return [
            'name' => 'required|between:3,16',
            'license_plate' => 'required|size:7|unique:delivery_truck,license_plate,' . $route->id
        ];
    }
}
