<?php

namespace App\Http\Requests\Admin;


class UpdateShopRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $shop = $this->route('shop');
        return [
            'logo' => 'sometimes|required',
            'name' => 'required|unique:shop,name,' . $shop->id,
            'contact_person' => 'required|max:10',
            'contact_info' => ['required' , 'regex:/^(0?1[0-9]\d{9})$|^((0(10|2[1-9]|[3-9]\d{2}))-?[1-9]\d{6,7})$/'],
            'min_money'=>'required',
            'introduction' => 'max:200',
           // 'address' => 'required|max:60',
            'area' => 'sometimes|required|max:200',
            'license' => 'sometimes|required',
            'business_license' => 'sometimes|required',
            'agency_contract' => 'sometimes|required',
            //'spreading_code' => 'required|exists:promoter,spreading_code'
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
            if ($address = $this->input('address')) {
                if (!$address['address']) {
                    $validator->errors()->add('address[address]', '详细地址 不能为空');
                }
                if (!$address['city_id']) {
                    $validator->errors()->add('address[city_id]', '市 不能为空');
                }
            }
        });
    }
}
