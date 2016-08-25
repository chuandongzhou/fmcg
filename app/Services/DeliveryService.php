<?php

namespace App\Services;



class DeliveryService
{
    /**
     * 数据统计
     *
     * @param $delivery
     * @return array
     */
    public static function formatDelivery($delivery){
        $goods = [];
        $deliveryMan = [];
        foreach($delivery as $order){
            foreach($order->orderGoods as $orderGoods){
                $pieces = $orderGoods->pieces+'';

                $goods[$orderGoods->goods->name][$pieces]['num'] = isset( $goods[$orderGoods->goods->name][$orderGoods->pieces]['num'])? (int)$goods[$orderGoods->goods->name][$orderGoods->pieces]['num']+(int)$orderGoods->num:$orderGoods->num;
                $goods[$orderGoods->goods->name][$pieces]['price'] = isset($goods[$orderGoods->goods->name][$orderGoods->pieces]['price'])?bcadd($goods[$orderGoods->goods->name][$orderGoods->pieces]['price'],$orderGoods->total_price,2):$orderGoods->total_price;
            }

            if(!empty($order->systemTradeInfo)) {
                if ($order->deliveryMan) {
                    $type = $order->systemTradeInfo->pay_type+'';
                    $deliveryMan[$order->deliveryMan->name]['price'][$type] = isset($deliveryMan[$order->deliveryMan->name]['price'][$type]) ? bcadd($deliveryMan[$order->deliveryMan->name]['price'][$type],
                        $order->systemTradeInfo->amount, 2) : $order->systemTradeInfo->amount;
                    $deliveryMan[$order->deliveryMan->name]['orderNum'] = isset($deliveryMan[$order->deliveryMan->name]['orderNum'])?(int)$deliveryMan[$order->deliveryMan->name]['orderNum']+1:1;
                    $deliveryMan[$order->deliveryMan->name]['totalPrice'] = isset($deliveryMan[$order->deliveryMan->name]['totalPrice'])?bcadd($deliveryMan[$order->deliveryMan->name]['totalPrice'],$order->systemTradeInfo->amount,2):$order->systemTradeInfo->amount;

                }
            }else{
                if ($order->deliveryMan) {
                    $deliveryMan[$order->deliveryMan->name]['price'][0] = isset($deliveryMan[$order->deliveryMan->name]['price'][0]) ? bcadd($deliveryMan[$order->deliveryMan->name]['price'][0],
                        $order->price, 2) : $order->price;
                    $deliveryMan[$order->deliveryMan->name]['orderNum'] = isset($deliveryMan[$order->deliveryMan->name]['orderNum'])?(int)$deliveryMan[$order->deliveryMan->name]['orderNum']+1:1;
                    $deliveryMan[$order->deliveryMan->name]['totalPrice'] = isset($deliveryMan[$order->deliveryMan->name]['totalPrice'])?bcadd($deliveryMan[$order->deliveryMan->name]['totalPrice'],$order->price,2):$order->price;
                }
            }

        }
        return ['goods'=>$goods,'deliveryMan'=>$deliveryMan];

    }
}