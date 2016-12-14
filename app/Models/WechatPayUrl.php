<?php

namespace App\Models;


class WechatPayUrl extends Model
{
    protected $table = 'wechat_pay_url';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'code_url',
        'created_at'
    ];

    protected $dates = ['created_at'];
}
