<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/10/19
 * Time: 15:02
 */

namespace App\Http\Requests\Api\v1;


class RegisterRequest extends Request
{

    public function rules()
    {
        return [
            'user_name' => 'sometimes|required|alpha_num|between:4,16|unique:user',
            'password' => 'required|between:6,18|alpha_num|confirmed',
            'type' => 'required|in:1,2,3,4',
            'logo' => 'sometimes|required',
            'name' => 'required|unique:shop',
            'contact_person' => 'required',
            'contact_info' => [
                'sometimes',
                'required',
                'regex:/^(0?1[0-9]\d{9})$|^((0(10|2[1-9]|[3-9]\d{2}))-?[1-9]\d{6,7})$/'
            ],
            'backup_mobile' => 'required|regex:/^(0?1[0-9]\d{9})$/|unique:user',
            'spreading_code' => 'alpha_num|max:20',
            'area' => 'sometimes|required|max:200',
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

                $addressDetail = array_get($address, 'address');
                $cityId = array_get($address, 'city_id');
                $districtId = array_get($address, 'district_id');
                $streetId = array_get($address, 'street_id');

                if (!$addressDetail) {
                    $validator->errors()->add('address[address]', '详细地址 不能为空');
                }
                if (mb_strlen($addressDetail, 'utf-8') > 30) {
                    $validator->errors()->add('address[address]', '详细地址 不超过30字');
                }
                if (!$cityId) {
                    $validator->errors()->add('address[city_id]', '市 不能为空');
                }
                if ($cityId && !$districtId && !empty($this->lowerLevelAddress($cityId))) {
                    $validator->errors()->add('address[district_id]', '区/县 不能为空');
                }
                if ($districtId && !$streetId && !empty($this->lowerLevelAddress($districtId))) {
                    $validator->errors()->add('address[street_id]', '街道 不能为空');
                }
                if ($this->input('type') != cons('user.type.retailer')) {
                    if (!$this->input('license') && !$this->hasFile('license')) {
                        $validator->errors()->add('license', '营业执照 不能为空');
                    }
                    if (!$this->input('license_num')) {
                        $validator->errors()->add('license_num', '营业执照编号 不能为空');
                    }
                }
            }
        });
    }
}