<?php

namespace App\Http\Requests\Api\v1;

use DB;
use Cache;

class CreateShippingAddressRequest extends UserRequest
{

    public function authorize()
    {
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
            'consigner' => 'required|max:10',
            'phone' => ['required', 'regex:/^(0?1[0-9]\d{9})$|^((0(10|2[1-9]|[3-9]\d{2}))-?[1-9]\d{6,7})$/'],
            'is_default' => 'sometimes|required|boolean',
            'province_id' => 'required|numeric',
            'city_id' => 'required|numeric',
//            'district_id' => 'required|numeric',
            'street_id' => 'numeric',
            'area_name' => 'required',
            'address' => 'required|max:30',
        ];
    }

    /**
     * 自定义验证
     *
     * @param \Illuminate\Contracts\Validation\Factory $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($factory)
    {
        return $this->defaultValidator($factory)->after(function ($validator) {

            $district_id = $this->input('district_id');
            $city_id = $this->input('city_id');
            $streetId = $this->input('street_id');
            if (empty($district_id) && !empty($this->lowerLevelAddress($city_id))) {
                $validator->errors()->add('district_id', '区/县 不能为空');
            }
            if (!empty($district_id) && empty($streetId) && !empty($this->lowerLevelAddress($district_id))) {
                $validator->errors()->add('street_id', '街道 不能为空');
            }

        });
    }
}
