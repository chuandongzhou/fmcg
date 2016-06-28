<?php

namespace App\Http\Requests\Api\v1;


class CreateSalesmanCustomerRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'shop_id' => 'integer',
            'contact' => 'required',
            'contact_information' => 'required',
            'business_area' => 'required',
            'display_fee' => 'required',
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
            if ($businessAddress= $this->input('business_address')) {
                if (!$businessAddress['address']) {
                    $validator->errors()->add('business_address[address]', '地址 不能为空');
                }
                if (!$businessAddress['city_id']) {
                    $validator->errors()->add('business_address[city_id]', '市 不能为空');
                }
            }
            if ($shippingAddress= $this->input('shipping_address')) {
                if (!$shippingAddress['address']) {
                    $validator->errors()->add('shipping_address[address]', '地址 不能为空');
                }
                if (!$shippingAddress['city_id']) {
                    $validator->errors()->add('shipping_address[city_id]', '市 不能为空');
                }
            }
        });
    }
}
