<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;

class DeliveryService
{
    /**
     * 统计数据处理
     *
     * @param $delivery
     * @return array
     */
    public function formatDelivery($delivery)
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
                    foreach ($order->deliveryMan->toArray() as $delivery) {
                        $deliveryMan[$delivery['name']]['price'][$type] = isset($deliveryMan[$delivery['name']]['price'][$type]) ? bcadd($deliveryMan[$delivery['name']]['price'][$type],
                            $order->systemTradeInfo->amount, 2) : $order->systemTradeInfo->amount;
                        $deliveryMan[$delivery['name']]['orderNum'] = isset($deliveryMan[$delivery['name']]['orderNum']) ? (int)$deliveryMan[$delivery['name']]['orderNum'] + 1 : 1;
                        $deliveryMan[$delivery['name']]['totalPrice'] = isset($deliveryMan[$delivery['name']]['totalPrice']) ? bcadd($deliveryMan[$delivery['name']]['totalPrice'],
                            $order->systemTradeInfo->amount, 2) : $order->systemTradeInfo->amount;
                    }


                }
            } else {
                //现金交易
                if ($order->deliveryMan) {
                    foreach ($order->deliveryMan->toArray() as $delivery) {
                        $deliveryMan[$delivery['name']]['price'][0] = isset($deliveryMan[$delivery['name']]['price'][0]) ? bcadd($deliveryMan[$delivery['name']]['price'][0],
                            $order->price, 2) : $order->price;
                        $deliveryMan[$delivery['name']]['orderNum'] = isset($deliveryMan[$delivery['name']]['orderNum']) ? (int)$deliveryMan[$delivery['name']]['orderNum'] + 1 : 1;
                        $deliveryMan[$delivery['name']]['totalPrice'] = isset($deliveryMan[$delivery['name']]['totalPrice']) ? bcadd($deliveryMan[$delivery['name']]['totalPrice'],
                            $order->price, 2) : $order->price;
                    }

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
    public function format($delivery)
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
            foreach ($order->deliveryMan->toArray() as $delivery) {
                $deliveryKey = array_search($delivery['name'], array_column($deliveryMan, 'name'));

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
                        'name' => $delivery['name'],
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
                            $deliveryMan[$deliveryKey]['price'] = bcadd($deliveryMan[$deliveryKey]['price'],
                                $order->price,
                                2);
                        } else {
                            //已在结果集的交易方式

                            $deliveryMan[$deliveryKey]['detail'][$k]['amount'] = bcadd($deliveryMan[$deliveryKey]['detail'][$k]['amount'],
                                $order->price, 2);
                            $deliveryMan[$deliveryKey]['num'] += 1;
                            $deliveryMan[$deliveryKey]['price'] = bcadd($deliveryMan[$deliveryKey]['price'],
                                $order->price,
                                2);
                        }

                    }
                }


            }

        }
        return ['goods' => $goods, 'deliveryMan' => $deliveryMan];

    }

    /**
     * 网页端配送数据统计
     *
     * @param $search
     * @param $is_pc
     * @return array
     */

    public function deliveryStatistical($search)
    {
        $search['start_at'] = !empty($search['start_at']) ? $search['start_at'] : '';
        $search['end_at'] = !empty($search['end_at']) ? $search['end_at'] : '';
        $search['delivery_man_id'] = !empty($search['delivery_man_id']) ? $search['delivery_man_id'] : '';

        $delivery = Order::where('shop_id',
            auth()->user()->shop->id)->whereNotNull('delivery_finished_at')->ofDeliverySearch($search)->ofOrderGoods()->with('systemTradeInfo',
            'deliveryMan')->get();
        return $this->formatDelivery($delivery);
    }
}