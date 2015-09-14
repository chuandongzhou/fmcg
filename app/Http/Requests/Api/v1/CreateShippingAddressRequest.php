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
            'phone'=>'regex:/^1[34578][0-9]{9}$/',
            'province_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'district_id' => 'numeric',
            'street_id' => '|numeric',
            'detail_address' => 'required',
        ];
    }
}
