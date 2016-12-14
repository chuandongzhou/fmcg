<?php

namespace App\Models;


class WechatPayCode extends Model
{
    protected $table = 'wechat_pay_code';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'deal_code',
        'created_at'
    ];
}
