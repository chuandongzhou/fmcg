<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\RedisService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OrderAutoCancel extends Command
{
    /**
     * 在线支付订单自动收货.
     *
     * @var string
     */
    protected $signature = 'order:auto:cancel';


    public function handle()
    {
        //获取在线支付 && 未付款时间大于一天
        $orders = Order::where([
            'pay_type' => cons('pay_type.online'),
            'pay_status' => cons('order.pay_status.non_payment'),
        ])->nonCancel()->where('created_at', '<=', Carbon::now()->subDay())->get();

        if ($orders->isEmpty()) {
            return false;
        }

        foreach ($orders as $order) {
            $order->fill([
                'is_cancel' => cons('order.is_cancel.on'),
                'cancel_by' => 0,
                'cancel_at' => Carbon::now()
            ])->save();
            //推送通知
            $redisKey = 'push:seller:' . $order->shop->user->id;

            $redisVal = '订单:' . $order->id . cons()->lang('push_msg.cancel_by_buyer');
            (new RedisService)->setRedis($redisKey, $redisVal, cons('push_time.msg_life'));
        }

    }
}
