<?php

namespace App\Http\Requests\Api\v1;

class RegisterUserSendSmsRequest extends Request
{
    public function rules()
    {
        return [
            'user_name' => 'sometimes|required|alpha_num|between:4,16|unique:user',
            'type' => 'required|in:1,2,3,4',
            'backup_mobile' => 'required|regex:/^(0?1[0-9]\d{9})$/|unique:user',
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
            if (in_windows() && empty($this->input('geetest_challenge'))) {
                $validator->errors()->add('backup_mobile', '请完成验证');
            }

        });
    }
}