<?php

namespace App\Models;


class PayPassword extends Model
{
    protected $table = 'pay_password';

    protected $fillable = [
        'password'
    ];


    /**
     * 设置支付密码
     *
     * @param $password
     */
    public function setPasswordAttribute($password)
    {

        if ($password) {
            $this->attributes['password'] = bcrypt($password);
        }
    }
}
