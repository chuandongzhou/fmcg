<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/10/19
 * Time: 15:02
 */
namespace App\Http\Requests\Api\v1;

class UpdateBackupPhoneRequest extends Request
{

    public function rules()
    {
        return [
            'backup_mobile' => 'required|regex:/^(0?1[0-9]\d{9})$/|unique:user',
            'code' => 'required'
        ];
    }
}