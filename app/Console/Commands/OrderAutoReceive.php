<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\InventoryService;
use App\Services\RedisService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;

class OrderAutoReceive extends Command
{
    /**
     * 在线支付订单自动收货.
     *
     * @var string
     */
    protected $signature = 'order:auto:receive';


    public function handle()
    {
        //获取在线支付 && 发货时间大于3天 && 未收货
        $orders = Order::where([
            'pay_type' => cons('pay_type.online'),
            'status' => cons('order.status.send'),
            'pay_status' => cons('order.pay_status.payment_success'),
        ])->where('send_at', '<=', Carbon::now()->subDays(3))->nonCancel()->get();

        if ($orders->isEmpty()) {
            return false;
        }
        DB::transaction(function () use ($orders) {
            foreach ($orders as $order) {
                if ($order->fill(['status' => cons('order.status.finished'), 'finished_at' => Carbon::now()])->save()) {
                    //更新systemTradeInfo完成状态
                    $tradeModel = $order->systemTradeInfo;
                    $tradeModel->fill([
                        'is_finished' => cons('trade.is_finished.yes'),
                        'finished_at' => Carbon::now()
                    ])->save();
                    //更新用户balance
                    $shopOwner = $order->shop->user;
                    $shopOwner->balance += $tradeModel->amount;
                    $shopOwner->save();
                    //买家入库
                    (new InventoryService())->autoIn($order->orderGoods);
                    //通知卖家
                    $redisKey = 'push:seller:' . $shopOwner->id;
                    $redisVal = '您的订单:' . $order->id . ',' . cons()->lang('push_msg.finished');

                    (new RedisService)->setRedis($redisKey, $redisVal, cons('push_time.msg_life'));
                }
        }
        });
    }
}
