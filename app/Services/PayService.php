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
     * @param int $payStatus
     * @param string $bankCardNo
     * @return bool
     */
    public function addTradeInfo(
        $orders,
        $amount,
        $orderFee,
        $tradeNo,
        $payType,
        $hmac = '',
        $chargeId = '',
        $payStatus = 1,
        $bankCardNo = ''
    ) {
        //更改订单状态
        $result = DB::transaction(function () use (
            $orders,
            $amount,
            $orderFee,
            $tradeNo,
            $payType,
            $hmac,
            $chargeId,
            $payStatus,
            $bankCardNo
        ) {
            //找出所有卖家的帐号
            $shopIds = $orders->pluck('shop_id')->all();
            $shops = Shop::whereIn('id', array_unique($shopIds))->with('user')->get();
            $accountArr = [];
            foreach ($shops as $shop) {
                $accountArr[$shop->id] = $shop->user->user_name;
            }

            $orderConf = cons('order');
            $newTimestamp = Carbon::now();
            foreach ($orders as $order) {
                $orderAttr = [
                    'pay_status' => $orderConf['pay_status']['payment_success'],
                    'paid_at' => $newTimestamp
                ];
                if ($payType == 'pos') {
                    $orderAttr['status'] = $orderConf['status']['finished'];
                    $orderAttr['finished_at'] = $newTimestamp;
                }

                $order->fill($orderAttr)->save();
                $fee = ($order->price / $amount) * $orderFee;
                $fee = sprintf("%.2f", $fee);
                // 增加易宝支付log
                DB::table('yeepay_log')->insert(
                    [
                        'order_id' => $order->id,
                        'trade_no' => $tradeNo,
                        'amount' => $order->price - $fee,
                        'paid_at' => $newTimestamp
                    ]
                );
                //增加系统交易信息
                $tradeConf = cons('trade');

                $payType = array_get($tradeConf['pay_type'], $payType, head($tradeConf['pay_type']));

                $systemTradeInfoAttr = [
                    'type' => $tradeConf['type']['in'],
                    'pay_type' => $payType,
                    'account' => $accountArr[$order->shop_id],
                    'order_id' => $order->id,
                    'charge_id' => $chargeId,
                    'bank_card_no' => $bankCardNo,
                    'trade_no' => $tradeNo,
                    'pay_status' => $payStatus,
                    'amount' => $order->price - $fee,
                    'target_fee' => $fee,
                    'trade_currency' => $tradeConf['trade_currency']['rmb'],
                    'callback_type' => 'json',
                    'hmac' => $hmac,
                ];

                if ($payType == 'pos') {
                    $systemTradeInfoAttr['is_finished'] = cons('trade.is_finished.yes');
                    $systemTradeInfoAttr['finished_at'] = $newTimestamp;
                }

                SystemTradeInfo::create($systemTradeInfoAttr);
            }
            return 'success';
        });


        return $result === 'success';
    }
}