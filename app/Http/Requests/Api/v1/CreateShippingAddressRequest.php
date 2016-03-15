<?php

namespace App\Http\Requests\Api\v1;


class CreateShippingAddressRequest extends Request
{

    public function authorize (){
        return auth()->user()->type <= cons('user.type.wholesaler');
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'consigner' =>'required',
            'phone'=>['required' , 'regex:/^(0?1[0-9]\d{9})$|^((0(10|2[1-9]|[3-9]\d{2}))-?[1-9]\d{6,7})$/'],
            'province_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'district_id' => 'numeric',
            'street_id' => 'numeric',
            'area_name' => 'required',
            'address' => 'required',
        ];
    }
}
