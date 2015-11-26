<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/10/19
 * Time: 15:02
 */
namespace App\Http\Requests\Api\v1;

class BackupPasswordRequest extends Request
{

    public function rules()
    {
        return [
            'user_name' => 'required|alpha_num|between:4,16|unique:user',
            'password' => 'required|min:6|confirmed',
            'backup_mobile' => 'required|regex:/^(0?1[0-9]\d{9})$/',
            'license_num' => 'required|numeric|unique:shop|digits:15'
        ];
    }
}