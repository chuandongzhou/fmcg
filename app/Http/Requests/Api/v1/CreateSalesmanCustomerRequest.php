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
            'name' => 'required',
            // 'account' => 'string|exists:user,user_name',
            'contact' => 'required',
            'contact_information' => 'required',
            'business_area' => 'required|numeric',
            'display_type' => 'required',
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
                if (!$businessAddress['city_id']) {
                    $validator->errors()->add('business_address[city_id]', '市 不能为空');
                }
            }
            if ($shippingAddress = $this->input('shipping_address')) {
                if (!$shippingAddress['address']) {
                    $validator->errors()->add('shipping_address[address]', '详细地址 不能为空');
                }
                if (!$shippingAddress['city_id']) {
                    $validator->errors()->add('shipping_address[city_id]', '市 不能为空');
                }
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
                if(!($customerShopType = $this->input('type'))) {
                    $validator->errors()->add('type', '客户类型不能为空');
                }
            }
        });
    }
}
