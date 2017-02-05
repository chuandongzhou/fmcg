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
    public function formatDelivery($delivery, $deliveryManId)
    {
        $goods = [];
        $deliveryMan = [];
        foreach ($delivery as $order) {

            $orderDelivery = empty($deliveryManId) ? '' : count($order->deliveryMan->lists('name')->toArray());
            foreach ($order->orderGoods as $orderGoods) {
                $pieces = $orderGoods->pieces . '';

                $goods[$orderDelivery][$orderGoods->goods_name][$order->user_type_name][$pieces]['num'] = isset($goods[$orderDelivery][$orderGoods->goods_name][$order->user_type_name][$orderGoods->pieces]['num']) ? (int)$goods[$orderDelivery][$orderGoods->goods_name][$order->user_type_name][$orderGoods->pieces]['num'] + (int)$orderGoods->num : $orderGoods->num;
                $goods[$orderDelivery][$orderGoods->goods_name][$order->user_type_name][$pieces]['price'] = isset($goods[$orderDelivery][$orderGoods->goods_name][$order->user_type_name][$orderGoods->pieces]['price']) ? bcadd($goods[$orderDelivery][$orderGoods->goods_name][$order->user_type_name][$orderGoods->pieces]['price'],
                    $orderGoods->total_price, 2) : $orderGoods->total_price;
            }
            if (!empty($order->systemTradeInfo)) {
                //非现金交易
                if ($order->deliveryMan) {

                    $type = $order->systemTradeInfo->pay_type . '';
                    foreach ($order->deliveryMan->toArray() as $delivery) {
                        if (!empty($deliveryManId) && $delivery['id'] != $deliveryManId) {
                            continue;
                        }
                        $deliveryMan[$delivery['name']]['first_time'] = isset($deliveryMan[$delivery['name']]['first_time']) ? $deliveryMan[$delivery['name']]['first_time'] : $order->delivery_finished_at;
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
                        if (!empty($deliveryManId) && $delivery['id'] != $deliveryManId) {
                            continue;
                        }
                        $deliveryMan[$delivery['name']]['first_time'] = isset($deliveryMan[$delivery['name']]['first_time']) ? $deliveryMan[$delivery['name']]['first_time'] : $order->delivery_finished_at;
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
    public function format($delivery, $deliveryId, $name)
    {
        $goods = [];
        $deliveryMan = [];
        //商品统计
        foreach ($delivery as $order) {
            $orderDelivery = count($order->deliveryMan->lists('name')->toArray()) . '';
            $deliveryKey = array_search($orderDelivery . '', array_column($goods, 'deliveryManNum'));
            //配送人数是否在结果集
            if ($deliveryKey === false) {
                $delveryArray = array(
                    'deliveryManNum' => $orderDelivery,
                    'allGoods' => array()
                );
                $goods[] = $delveryArray;
                $deliveryKey = array_search($orderDelivery . '', array_column($goods, 'deliveryManNum'));
            }

            $typeName = cons()->valueLang('user.type', cons('user.type.' . $order->user_type_name));
            foreach ($order->orderGoods as $orderGoods) {
                $key = array_search($orderGoods['goods']['name'],
                    array_column($goods[$deliveryKey]['allGoods'], 'name'));
                if ($key === false) {
                    //未在结果集的商品名称
                    $arrs = array(
                        'name' => $orderGoods['goods']['name'],
                        'data' => array(
                            array(
                                'pieces' => $orderGoods->pieces,
                                'num' => $orderGoods->num,
                                'amount' => $orderGoods->total_price
                            )
                        )
                    );
                    $goods[$deliveryKey]['allGoods'][] = $arrs;
                } else {
                    $piecesKey = array_search($orderGoods->pieces,
                        array_column($goods[$deliveryKey]['allGoods'][$key]['data'], 'pieces'));
                    if ($piecesKey === false) {
                        //未在结果集的商品单位
                        $add = array(
                            'pieces' => $orderGoods->pieces,
                            'num' => $orderGoods->num,
                            'amount' => $orderGoods->total_price
                        );
                        $goods[$deliveryKey]['allGoods'][$key]['data'][] = $add;
                    } else {
                        //已在结果集的商品单位
                        $goods[$deliveryKey]['allGoods'][$key]['data'][$piecesKey]['num'] += (int)$orderGoods->num;
                        $goods[$deliveryKey]['allGoods'][$key]['data'][$piecesKey]['amount'] = bcadd($goods[$deliveryKey]['allGoods'][$key]['data'][$piecesKey]['amount'],
                            $orderGoods->total_price, 2);
                    }
                }
            }
            //配送统计
            foreach ($order->deliveryMan->toArray() as $delivery) {
                if (!empty($deliveryId) && $delivery['id'] != $deliveryId) {
                    continue;
                }
                if (empty($deliveryMan)) {
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
                    $deliveryMan['name'] = $name;
                    $deliveryMan['num'] = 1;
                    $deliveryMan['price'] = $order->price;
                    $deliveryMan['detail'] = $payType;
                } else {
                    //已添加到结果集的配送人员
                    if ($order['systemTradeInfo'] != null) {
                        //非现金交易
                        $pkey = array_search($order->systemTradeInfo->pay_type,
                            array_column($deliveryMan['detail'], 'pay_type'));
                        if ($pkey === false) {
                            //未在结果集的交易方式
                            $deliveryDetail = array(
                                'pay_type' => $order->systemTradeInfo->pay_type,
                                'amount' => $order->systemTradeInfo->amount
                            );
                            $deliveryMan['detail'][] = $deliveryDetail;
                            $deliveryMan['num'] += 1;
                            $deliveryMan['price'] = bcadd($deliveryMan['price'],
                                $order->systemTradeInfo->amount, 2);

                        } else {
                            //已在结果集的交易方式

                            $deliveryMan['detail'][$pkey]['amount'] = bcadd($deliveryMan['detail'][$pkey]['amount'],
                                $order->systemTradeInfo->amount, 2);
                            $deliveryMan['num'] += 1;
                            $deliveryMan['price'] = bcadd($deliveryMan['price'],
                                $order->systemTradeInfo->amount, 2);

                        }

                    } else {
                        //现金交易
                        $k = array_search(0, array_column($deliveryMan['detail'], 'pay_type'));
                        if ($k === false) {
                            //未在结果集的交易方式
                            $deliveryDetail = array(
                                'pay_type' => 0,
                                'amount' => $order->price
                            );
                            $deliveryMan['detail'][] = $deliveryDetail;
                            $deliveryMan['num'] += 1;
                            $deliveryMan['price'] = bcadd($deliveryMan['price'],
                                $order->price,
                                2);
                        } else {
                            //已在结果集的交易方式

                            $deliveryMan['detail'][$k]['amount'] = bcadd($deliveryMan['detail'][$k]['amount'],
                                $order->price, 2);
                            $deliveryMan['num'] += 1;
                            $deliveryMan['price'] = bcadd($deliveryMan['price'],
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

        $search['start_at'] = !empty($search['start_at']) ? (new Carbon($search['start_at']))->startOfDay() : '';
        $search['end_at'] = !empty($search['end_at']) ? (new Carbon($search['end_at']))->endOfDay() : '';
        $search['delivery_man_id'] = !empty($search['delivery_man_id']) ? $search['delivery_man_id'] : '';

        $delivery = Order::ofSell(auth()->id())->whereNotNull('delivery_finished_at')->ofDeliverySearch($search)->ofOrderGoods()->with('systemTradeInfo',
            'deliveryMan')->orderBy('delivery_finished_at', 'asc')->get();
        $deliveryNum = array();
        foreach ($delivery as $order) {
            $num = $order->deliveryMan->lists('name')->count();
            array_search($num, $deliveryNum) === false ? array_push($deliveryNum, $num) : '';
        };
        if (!empty($search['num'])) {
            $delivery = $delivery->filter(function ($item) use ($search) {
                return $item->deliveryMan->lists('name')->count() == $search['num'];
            });

        }
        $res = array(
            'deliveryNum' => $deliveryNum,
            'delivery' => $this->formatDelivery($delivery, $search['delivery_man_id'])
        );


        return $res;
    }
}