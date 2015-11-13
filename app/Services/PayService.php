<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/11/6
 * Time: 19:07
 */
namespace App\Services;

use App\Models\Shop;
use App\Models\SystemTradeInfo;
use Carbon\Carbon;
use DB;

class PayService
{

    /**
     * 支付成功添加交易信息
     *
     * @param $orders
     * @param $amount
     * @param $orderFee
     * @param $tradeNo
     * @param $payType
     * @param string $hmac
     * @param string $chargeId
     * @return bool
     */
    public function addTradeInfo($orders, $amount, $orderFee, $tradeNo, $payType, $hmac = '', $chargeId = '')
    {
        //更改订单状态
        $result = DB::transaction(function () use ($orders, $amount, $orderFee, $tradeNo, $payType, $hmac, $chargeId) {
            //找出所有卖家的帐号
            $shopIds = $orders->pluck('shop_id')->all();
            $shops = Shop::whereIn('id', array_unique($shopIds))->with('user')->get();
            $accountArr = [];
            foreach ($shops as $shop) {
                $accountArr[$shop->id] = $shop->user->user_name;
            }

            $orderConf = cons('order');
            foreach ($orders as $order) {
                $order->fill([
                    'pay_status' => $orderConf['pay_status']['payment_success'],
                ])->save();
                $fee = ($order->price / $amount) * $orderFee;
                $fee = sprintf("%.2f", $fee);
                // 增加易宝支付log
                DB::table('yeepay_log')->insert(
                    [
                        'order_id' => $order->id,
                        'trade_no' => $tradeNo,
                        'amount' => $order->price - $fee,
                        'paid_at' => Carbon::now()
                    ]
                );
                //增加系统交易信息
                $tradeConf = cons('trade');

                $payType = array_get($tradeConf['pay_type'], $payType, head($tradeConf['pay_type']));
                SystemTradeInfo::create([
                    'type' => $tradeConf['type']['in'],
                    'pay_type' => $payType,
                    'account' => $accountArr[$order->shop_id],
                    'order_id' => $order->id,
                    'charge_id' => $chargeId,
                    'trade_no' => $tradeNo,
                    'pay_status' => $tradeConf['pay_status']['success'],
                    'amount' => $order->price - $fee,
                    'target_fee' => $fee,
                    'trade_currency' => $tradeConf['trade_currency']['rmb'],
                    'callback_type' => 'json',
                    'hmac' => $hmac,
                ]);
            }
            return 'success';
        });


        return $result === 'success';
    }
}