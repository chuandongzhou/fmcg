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
    public static function formatDelivery($delivery)
    {
        $goods = [];
        $deliveryMan = [];
        foreach ($delivery as $order) {
            foreach ($order->orderGoods as $orderGoods) {
                $pieces = $orderGoods->pieces + '';

                $goods[$orderGoods->goods->name][$pieces]['num'] = isset($goods[$orderGoods->goods->name][$orderGoods->pieces]['num']) ? (int)$goods[$orderGoods->goods->name][$orderGoods->pieces]['num'] + (int)$orderGoods->num : $orderGoods->num;
                $goods[$orderGoods->goods->name][$pieces]['price'] = isset($goods[$orderGoods->goods->name][$orderGoods->pieces]['price']) ? bcadd($goods[$orderGoods->goods->name][$orderGoods->pieces]['price'],
                    $orderGoods->total_price, 2) : $orderGoods->total_price;
            }

            if (!empty($order->systemTradeInfo)) {
                //非现金交易
                if ($order->deliveryMan) {
                    $type = $order->systemTradeInfo->pay_type + '';
                    $deliveryMan[$order->deliveryMan->name]['price'][$type] = isset($deliveryMan[$order->deliveryMan->name]['price'][$type]) ? bcadd($deliveryMan[$order->deliveryMan->name]['price'][$type],
                        $order->systemTradeInfo->amount, 2) : $order->systemTradeInfo->amount;
                    $deliveryMan[$order->deliveryMan->name]['orderNum'] = isset($deliveryMan[$order->deliveryMan->name]['orderNum']) ? (int)$deliveryMan[$order->deliveryMan->name]['orderNum'] + 1 : 1;
                    $deliveryMan[$order->deliveryMan->name]['totalPrice'] = isset($deliveryMan[$order->deliveryMan->name]['totalPrice']) ? bcadd($deliveryMan[$order->deliveryMan->name]['totalPrice'],
                        $order->systemTradeInfo->amount, 2) : $order->systemTradeInfo->amount;

                }
            } else {
                //现金交易
                if ($order->deliveryMan) {
                    $deliveryMan[$order->deliveryMan->name]['price'][0] = isset($deliveryMan[$order->deliveryMan->name]['price'][0]) ? bcadd($deliveryMan[$order->deliveryMan->name]['price'][0],
                        $order->price, 2) : $order->price;
                    $deliveryMan[$order->deliveryMan->name]['orderNum'] = isset($deliveryMan[$order->deliveryMan->name]['orderNum']) ? (int)$deliveryMan[$order->deliveryMan->name]['orderNum'] + 1 : 1;
                    $deliveryMan[$order->deliveryMan->name]['totalPrice'] = isset($deliveryMan[$order->deliveryMan->name]['totalPrice']) ? bcadd($deliveryMan[$order->deliveryMan->name]['totalPrice'],
                        $order->price, 2) : $order->price;
                }
            }

        }
        return ['goods' => $goods, 'deliveryMan' => $deliveryMan];

    }

    /**
     * 移动端配送数据处理
     *
     * @param $delivery
     * @return array
     */
    public static function format($delivery)
    {
        $goods = [];
        $deliveryMan = [];
        foreach ($delivery as $order) {
            //商品统计
            foreach ($order->orderGoods as $orderGoods) {
                $key = array_search($orderGoods['goods']['name'], array_column($goods, 'name'));

                if ($key === false) {
                    //未在结果集的商品
                    $arr = array(
                        array(
                            'pieces' => $orderGoods->pieces,
                            'num' => $orderGoods->num,
                            'amount' => $orderGoods->total_price
                        )
                    );
                    $arrs = array(
                        'name' => $orderGoods->goods->name,
                        'detail' => $arr
                    );
                    $goods[] = $arrs;
                } else {
                    //已在结果集的商品
                    $piecesKey = array_search($orderGoods->pieces,
                        array_column($goods[$key]['detail'], 'pieces'));
                    if ($piecesKey === false) {
                        //未在结果集的商品单位
                        $add = array(
                            'pieces' => $orderGoods->pieces,
                            'num' => $orderGoods->num,
                            'amount' => $orderGoods->total_price

                        );
                        $goods[$key]['detail'][] = $add;
                    } else {
                        //已在结果集的商品单位
                        $goods[$key]['detail'][$piecesKey]['num'] += (int)$orderGoods->num;
                        $goods[$key]['detail'][$piecesKey]['amount'] = bcadd($goods[$key]['detail'][$piecesKey]['amount'],
                            $orderGoods->total_price, 2);
                    }

                }
            }
            //配送统计
            $deliveryKey = array_search($order->deliveryMan->name, array_column($deliveryMan, 'name'));

            if ($deliveryKey === false) {
                //未添加配送人员到结果集
                if ($order['systemTradeInfo'] == null) {
                    //现金交易
                    $payType = array(
                        array(
                            'pay_type' => 0,
                            'amount' => $order->price
                        )
                    );
                } else {
                    //非现金交易
                    $payType = array(
                        array(
                            'pay_type' => $order->systemTradeInfo->pay_type,
                            'amount' => $order->systemTradeInfo->amount
                        )
                    );
                }
                $deliveryDetail = array(
                    'name' => $order->deliveryMan->name,
                    'num' => 1,
                    'price' => $order->price,
                    'detail' => $payType
                );
                $deliveryMan[] = $deliveryDetail;

            } else {
                //已添加到结果集的配送人员
                if ($order['systemTradeInfo'] != null) {
                    //非现金交易
                    $pkey = array_search($order->systemTradeInfo->pay_type,
                        array_column($deliveryMan[$deliveryKey]['detail'], 'pay_type'));
                    if ($pkey === false) {
                        //未在结果集的交易方式
                        $deliveryDetail = array(
                            'pay_type' => $order->systemTradeInfo->pay_type,
                            'amount' => $order->systemTradeInfo->amount
                        );
                        $deliveryMan[$deliveryKey]['detail'][] = $deliveryDetail;
                        $deliveryMan[$deliveryKey]['num'] += 1;
                        $deliveryMan[$deliveryKey]['price'] = bcadd($deliveryMan[$deliveryKey]['price'],
                            $order->systemTradeInfo->amount, 2);

                    } else {
                        //已在结果集的交易方式

                        $deliveryMan[$deliveryKey]['detail'][$pkey]['amount'] = bcadd($deliveryMan[$deliveryKey]['detail'][$pkey]['amount'],
                            $order->systemTradeInfo->amount, 2);
                        $deliveryMan[$deliveryKey]['num'] += 1;
                        $deliveryMan[$deliveryKey]['price'] = bcadd($deliveryMan[$deliveryKey]['price'],
                            $order->systemTradeInfo->amount, 2);

                    }

                } else {
                    //现金交易
                    $k = array_search(0, array_column($deliveryMan[$deliveryKey]['detail'], 'pay_type'));
                    if ($k === false) {
                        //未在结果集的交易方式
                        $deliveryDetail = array(
                            'pay_type' => 0,
                            'amount' => $order->price
                        );
                        $deliveryMan[$deliveryKey]['detail'][] = $deliveryDetail;
                        $deliveryMan[$deliveryKey]['num'] += 1;
                        $deliveryMan[$deliveryKey]['price'] = bcadd($deliveryMan[$deliveryKey]['price'], $order->price,
                            2);
                    } else {
                        //已在结果集的交易方式

                        $deliveryMan[$deliveryKey]['detail'][$k]['amount'] = bcadd($deliveryMan[$deliveryKey]['detail'][$k]['amount'],
                            $order->price, 2);
                        $deliveryMan[$deliveryKey]['num'] += 1;
                        $deliveryMan[$deliveryKey]['price'] = bcadd($deliveryMan[$deliveryKey]['price'], $order->price,
                            2);
                    }

                }


            }

        }
        return ['goods' => $goods, 'deliveryMan' => $deliveryMan];

    }
}