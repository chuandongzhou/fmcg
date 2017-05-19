<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/10/19
 * Time: 15:02
 */

namespace App\Http\Requests\Api\v1;


class RegisterUserRequest extends Request
{

    public function rules()
    {
        return [
            'user_name' => 'required|regex:/^[a-zA-Z0-9]+$/|between:4,16|unique:user',
            'type' => 'required|in:1,2,3',
            'backup_mobile' => 'required|regex:/^(0?1[0-9]\d{9})$/|unique:user',
            'code' => 'required'
        ];
    }
    
}