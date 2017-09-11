<?php

namespace App\Http\Requests\Api\v1;


class CreateSalesmanCustomerRequest extends SalesmanRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:30',
            'contact' => 'required|max:10',
            'contact_information' => [
                'required',
                'regex:/^(0?1[0-9]\d{9})$|^((0(10|2[1-9]|[3-9]\d{2}))-?[1-9]\d{6,7})$/'
            ],
            'business_area' => 'required|numeric',
            'display_type' => 'sometimes|required',
            //'display_start_month' => 'sometimes|required_with:display_end_month',
            //'display_end_month' => 'sometimes|required_with:display_start_month',
            'display_fee' => 'sometimes|required|numeric|min:0',
            'salesman_id' => 'sometimes|required|integer'
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
            if ($businessAddress = $this->input('business_address')) {
                if (!$businessAddress['address']) {
                    $validator->errors()->add('business_address[address]', '详细地址 不能为空');
                }
                if (mb_strlen($businessAddress['address'], 'utf-8') > 30) {
                    $validator->errors()->add('business_address[address]', '详细地址 不超过30字');
                }
                if (!$businessAddress['city_id']) {
                    $validator->errors()->add('business_address[city_id]', '市 不能为空');
                }
                if ($businessAddress['city_id'] && !$businessAddress['district_id'] && !empty($this->lowerLevelAddress($businessAddress['city_id']))) {
                    $validator->errors()->add('business_address[district_id]', '区/县 不能为空');
                }
                /*if($businessAddress['district_id'] && !$businessAddress['street_id'] && !empty($this->lowerLevelAddress($businessAddress['district_id']))){
                    $validator->errors()->add('business_address[street_id]', '街道 不能为空');
                }*/
            }
            if ($shippingAddress = $this->input('shipping_address')) {
                if (!$shippingAddress['address']) {
                    $validator->errors()->add('shipping_address[address]', '详细地址 不能为空');
                }
                if (mb_strlen($shippingAddress['address'], 'utf-8') > 30) {
                    $validator->errors()->add('business_address[address]', '详细地址 不超过30字');
                }
                if (!$shippingAddress['city_id']) {
                    $validator->errors()->add('shipping_address[city_id]', '市 不能为空');
                }
                if ($shippingAddress['city_id'] && !$shippingAddress['district_id'] && !empty($this->lowerLevelAddress($shippingAddress['city_id']))) {
                    $validator->errors()->add('shipping_address[district_id]', '区/县 不能为空');
                }
                /*if($shippingAddress['district_id'] && !$shippingAddress['street_id'] && !empty($this->lowerLevelAddress($shippingAddress['district_id']))){
                    $validator->errors()->add('shipping_address[street_id]', '街道 不能为空');
                }*/
            }
            if ($displayType = $this->input('display_type')) {
                $month = $this->only('display_start_month', 'display_end_month');
                if (!array_get($month, 'display_start_month')) {
                    $validator->errors()->add('display_start_month', '开始月份不能为空');
                }
                if (!array_get($month, 'display_end_month')) {
                    $validator->errors()->add('display_end_month', '结束月份不能为空');
                }
            }

            $userType = salesman_auth()->check() ? salesman_auth()->user()->shop_type : (auth()->check() ? auth()->user()->type : null);
            if (!is_null($userType) && $userType == cons('user.type.supplier')) {
                if (!($customerShopType = $this->input('type'))) {
                    $validator->errors()->add('type', '客户类型不能为空');
                }
            } else {
                if (!($this->input('store_type'))) {
                    $validator->errors()->add('store_type', '商铺类型不能为空');
                }
            }
        });
    }
}
