<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\Order;
use Carbon\Carbon;

class DeliveryService
{
    /**
     * 统计数据处理
     *
     * @param $delivery
     * @param $deliveryManId
     * @return array
     */
    public function formatDelivery($delivery, $deliveryManId)
    {
        $goods = [];
        $deliveryMan = [];
        $orderGoodsTypes = cons('order.goods.type');
        foreach ($delivery as $order) {
            $orderDelivery = empty($deliveryManId) ? '' : count($order->deliveryMan->toArray());
            foreach ($order->orderGoods as $orderGoods) {
                $pieces = $orderGoods->pieces;
                $goodsItem = $orderGoods->goods ?: new Goods();
                $goodsName = $goodsItem->name;
                if ($orderGoods->type != $orderGoodsTypes['order_goods']) {
                    $goodsName = $orderGoods->type == $orderGoodsTypes['gift_goods'] ? "(赠品)  " . $goodsName : "(陈列)  " . $goodsName;
                }

                $goods[$orderDelivery][$goodsName][$order->user_type_name][$pieces]['num'] = isset($goods[$orderDelivery][$goodsName][$order->user_type_name][$orderGoods->pieces]['num']) ? (int)$goods[$orderDelivery][$goodsName][$order->user_type_name][$orderGoods->pieces]['num'] + (int)$orderGoods->num : $orderGoods->num;
                $goods[$orderDelivery][$goodsName][$order->user_type_name][$pieces]['price'] = isset($goods[$orderDelivery][$goodsName][$order->user_type_name][$orderGoods->pieces]['price']) ? bcadd($goods[$orderDelivery][$goodsName][$order->user_type_name][$orderGoods->pieces]['price'],
                    $orderGoods->total_price, 2) : $orderGoods->total_price;
            }

            if ($order->deliveryMan) {
                $systemTradeInfo = $order->systemTradeInfo;
                $type = 0;
                if ($systemTradeInfo) {
                    $type = $systemTradeInfo->pay_type;
                }

                foreach ($order->deliveryMan->toArray() as $delivery) {
                    if (!empty($deliveryManId) && $delivery['id'] != $deliveryManId) {
                        continue;
                    }
                    $deliveryMan[$delivery['name']]['first_time'] = isset($deliveryMan[$delivery['name']]['first_time']) ? $deliveryMan[$delivery['name']]['first_time'] : $order->delivery_finished_at;
                    $deliveryMan[$delivery['name']]['price'][$type] = isset($deliveryMan[$delivery['name']]['price'][$type]) ? bcadd($deliveryMan[$delivery['name']]['price'][$type],
                        $order->after_rebates_price, 2) : $order->after_rebates_price;
                    $deliveryMan[$delivery['name']]['orderNum'] = isset($deliveryMan[$delivery['name']]['orderNum']) ? (int)$deliveryMan[$delivery['name']]['orderNum'] + 1 : 1;
                    $deliveryMan[$delivery['name']]['totalPrice'] = isset($deliveryMan[$delivery['name']]['totalPrice']) ? bcadd($deliveryMan[$delivery['name']]['totalPrice'],
                        $order->after_rebates_price, 2) : $order->after_rebates_price;
                    $deliveryMan[$delivery['name']]['display_fee'] = isset($deliveryMan[$delivery['name']]['display_fee']) ? bcadd($deliveryMan[$delivery['name']]['display_fee'],
                        $order->display_fee, 2) : $order->display_fee;

                    if ($coupon = $order->coupon) {
                        $discount = $coupon->discount;
                        $deliveryMan[$delivery['name']]['discount'] = isset($deliveryMan[$delivery['name']]['discount']) ? bcadd($deliveryMan[$delivery['name']]['discount'],
                            $discount, 2) : $discount;
                    }

                }
            }

        }
        ksort($goods);
        return ['goods' => $goods, 'deliveryMan' => $deliveryMan];

    }

    /**
     * 移动端配送数据处理
     *
     * @param $delivery
     * @return array
     */
    public function format($delivery, $user)
    {
        $goods = [];
        $deliveryMan = [
            'name' => $user->name,
            'num' => $delivery->count(),
            'price' => $delivery->sum('after_rebates_price'),
            'discount' => $delivery->pluck('coupon')->sum('discount'),
            'display_fee' => $delivery->sum('display_fee')
        ];
        $orderGoodsTypes = cons('order.goods.type');

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

            //$typeName = cons()->valueLang('user.type', cons('user.type.' . $order->user_type_name));
            foreach ($order->orderGoods as $orderGoods) {
                $goodsItem = $orderGoods->goods ?: new Goods();
                $goodsName = $goodsItem->name;
                if ($orderGoods->type != $orderGoodsTypes['order_goods']) {
                    $goodsName = $orderGoods->type == $orderGoodsTypes['gift_goods'] ? "(赠品)  " . $goodsName : "(陈列)  " . $goodsName;
                }

                $key = array_search($goodsName, array_column($goods[$deliveryKey]['allGoods'], 'name'));
                if ($key === false) {
                    //未在结果集的商品名称
                    $arrs = array(
                        'name' => $goodsName,
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

            if (empty($deliveryMan['detail'])) {
                //未添加配送人员到结果集
                if ($order['systemTradeInfo'] == null) {
                    //现金交易
                    $payType = array(
                        array(
                            'pay_type' => 0,
                            'amount' => $order->after_rebates_price
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
                /*  $deliveryMan['name'] = $name;
                  $deliveryMan['num'] = 1;
                  $deliveryMan['price'] = $order->after_rebates_price;*/
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
                        /*    $deliveryMan['num'] += 1;
                            $deliveryMan['price'] = bcadd($deliveryMan['price'], $order->systemTradeInfo->amount, 2);*/

                    } else {
                        //已在结果集的交易方式

                        $deliveryMan['detail'][$pkey]['amount'] = bcadd($deliveryMan['detail'][$pkey]['amount'],
                            $order->systemTradeInfo->amount, 2);
                        /*$deliveryMan['num'] += 1;
                        $deliveryMan['price'] = bcadd($deliveryMan['price'], $order->systemTradeInfo->amount, 2);*/
                    }

                } else {
                    //现金交易
                    $k = array_search(0, array_column($deliveryMan['detail'], 'pay_type'));
                    if ($k === false) {
                        //未在结果集的交易方式
                        $deliveryDetail = array(
                            'pay_type' => 0,
                            'amount' => $order->after_rebates_price
                        );
                        $deliveryMan['detail'][] = $deliveryDetail;
                        /*  $deliveryMan['num'] += 1;
                          $deliveryMan['price'] = bcadd($deliveryMan['price'], $order->after_rebates_price, 2);*/
                    } else {
                        //已在结果集的交易方式

                        $deliveryMan['detail'][$k]['amount'] = bcadd($deliveryMan['detail'][$k]['amount'],
                            $order->after_rebates_price, 2);
                        /*   $deliveryMan['num'] += 1;
                           $deliveryMan['price'] = bcadd($deliveryMan['price'], $order->after_rebates_price, 2);*/
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
     * @param $user
     * @return array
     */

    public function deliveryStatistical($search, $user = null)
    {

        $user = $user ? $user : auth()->user();

        $delivery = Order::ofSell($user->shop_id)
            ->useful()
            ->whereNotNull('delivery_finished_at')
            ->ofDeliverySearch($search)
            ->with('systemTradeInfo', 'deliveryMan', 'coupon', 'orderGoods.goods')
            ->orderBy('delivery_finished_at', 'asc')->get();
        $deliveryNum = array();

        if (array_get($search, 'delivery_man_id')) {
            foreach ($delivery as $order) {
                $num = $order->deliveryMan->count();
                in_array($num, $deliveryNum) || array_push($deliveryNum, $num);
            }

            sort($deliveryNum);
            if (!empty($search['num'])) {
                $delivery = $delivery->filter(function ($item) use ($search) {
                    return $item->deliveryMan->lists('name')->count() == $search['num'];
                });

            }
        };
        $res = array(
            'deliveryNum' => $deliveryNum,
            'delivery' => $this->formatDelivery($delivery, array_get($search, 'delivery_man_id'))
        );


        return $res;
    }
}