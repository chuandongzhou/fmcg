<?php

namespace App\Models;


use Carbon\Carbon;

class SystemTradeInfo extends Model
{
    protected $table = 'system_trade_info';
    public $timestamps = false;
    protected $fillable = [
        'type',
        'pay_type',
        'account',
        'paid_at',
        'order_id',
        'trade_no',
        'pay_status',
        'amount',
        'target_fee',
        'trade_currency',
        'callback_type',
        'hmac',
        'success_at',
        'notice_at'
    ];

    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::created(function ($model) {
            $model->paid_at = Carbon::now();
            $model->success_at = Carbon::now();
        });
    }

}
