<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/10/19
 * Time: 15:02
 */
namespace App\Http\Requests\Api\v1;


class RegisterUserShopRequest extends Request
{

    public function rules()
    {
        return [
            'logo' => 'sometimes|required',
            'name' => 'required|unique:shop|max:20',
            'contact_person' => 'required|max:10',
            'contact_info' => ['required', 'regex:/^(0?1[0-9]\d{9})$|^((0(10|2[1-9]|[3-9]\d{2}))-?[1-9]\d{6,7})$/'],
            'spreading_code' => 'alpha_num|max:20',
            'address[address]' => 'max:60',
            'address[city_id]' => '',
            'area' => 'sometimes|required|max:200',
            'license' => 'required',
            'license_num' => 'required|unique:shop|between:15,18',
            'business_license' => 'required',
            'agency_contract' => 'sometimes|required'
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
                if (mb_strlen($address['address'], 'utf-8') > 30) {
                    $validator->errors()->add('address[address]', '详细地址 不超过30字');
                }
                if (!$address['city_id']) {
                    $validator->errors()->add('address[city_id]', '市 不能为空');
                }
                if ($address['city_id'] && !$address['district_id'] && !empty($this->lowerLevelAddress($address['city_id']))) {
                    $validator->errors()->add('address[district_id]', '区/县 不能为空');
                }
                if ($address['district_id'] && !$address['street_id'] && !empty($this->lowerLevelAddress($address['district_id']))) {
                    $validator->errors()->add('address[street_id]', '街道 不能为空');
                }
            }
        });
    }
}