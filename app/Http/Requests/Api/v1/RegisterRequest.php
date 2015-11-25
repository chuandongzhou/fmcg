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
            'user_name' => 'required|alpha_num|between:4,16|unique:user',
            'password' => 'required|min:6|confirmed',
            'type' => 'required|in:1,2,3',
            'logo' => 'sometimes|required',
            'name' => 'required|unique:shop',
            'contact_person' => 'required',
            'contact_info' => ['required' , 'regex:/^(0?1[0-9]\d{9})$|^((0(10|2[1-9]|[3-9]\d{2}))-?[1-9]\d{6,7})$/'] ,
            'spreading_code'=> 'alpha_num|max:20',
            'address[address]' => 'required|max:60',
            'address[city_id]' => 'required',
            'area' => 'sometimes|required|max:200',
            'license' => 'required',
            'license_num' => 'required|numeric|unique:shop|digits:15',
            'business_license' => 'required',
            'agency_contract' => 'sometimes|required'
        ];
    }
}