<?php

namespace App\Http\Requests\Api\v1;

class SalesmanCustomerImportRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'salesman_id' => 'required|integer|exists:salesman,id',
            'province_id' => 'required|integer',
            'city_id' => 'required|integer',
            'district_id' => 'required|integer',
            'x_lng' => 'required',
            'y_lat' => 'required',
        ];
    }
}
