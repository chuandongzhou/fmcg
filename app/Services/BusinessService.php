<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\MortgageGoods;
use App\Models\SalesmanVisitOrder;
use App\Models\Shop;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class BusinessService
{
    /**
     * 获取订单详情
     *
     * @param $salesmanVisitOrder
     * @return array
     */
    public function getOrderData($salesmanVisitOrder)
    {

        $orderTypeConf = cons('salesman.order');
        $orderGoodsConf = $orderTypeConf['goods']['type'];

        $data = [
            'order' => $salesmanVisitOrder
        ];

        if ($salesmanVisitOrder->type == $orderTypeConf['type']['order']) {
            $goods = $salesmanVisitOrder->orderGoods->load(['mortgageGoods', 'goods']);
            $orderGoods = $goods->filter(function ($item) use ($orderGoodsConf) {
                return $item->type == $orderGoodsConf['order'];
            });

            $mortgageGoods = $goods->filter(function ($item) use ($orderGoodsConf) {
                return $item->type == $orderGoodsConf['mortgage'];
            });
            $data['orderGoods'] = $orderGoods;
            $data['mortgageGoods'] = $mortgageGoods;
        } else {
            $data['orderGoods'] = $salesmanVisitOrder->orderGoods;
        }
        return $data;
    }

    /**
     * 获取店铺业务员订单信息
     *
     * @param $shop
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function getSalesmanOrders($shop, $startDate, $endDate)
    {
        $salesmen = $shop instanceof Shop ? $shop->salesmen() : $shop;

        $salesmen = $salesmen->with(['orders', 'visits'])->get()->each(function ($salesman) use (
            $startDate,
            $endDate
        ) {
            $orders = $salesman->orders->filter(function ($order) use ($startDate, $endDate) {
                return $order->created_at >= $startDate && $order->created_at <= $endDate;
            });

            $orderForms = $orders->filter(function ($order) {
                return $order->type == cons('salesman.order.type.order');
            });

            $visits = $salesman->visits->filter(function ($visit) use ($startDate, $endDate) {
                return $visit->created_at >= $startDate && $visit->created_at <= $endDate;
            });

            $salesman->visitCustomerCount = $visits->pluck('salesman_customer_id')->toBase()->unique()->count();
            $salesman->orderFormSumAmount = $orderForms->sum('amount');
            $salesman->orderFormCount = $orderForms->count();

            $returnOrders = $orders->reject(function ($order) {
                return $order->type == cons('salesman.order.type.order');
            });

            $salesman->returnOrderSumAmount = $returnOrders->sum('amount');
            $salesman->returnOrderCount = $returnOrders->count();
        });

        return $salesmen;
    }

    /**
     * 根据类型获取所有订单
     *
     * @param $salesmenId
     * @param $type
     * @param $withOrderGoods
     * @return mixed
     */
    public function getOrders($salesmenId, $type, $withOrderGoods = false)
    {
        $with = $withOrderGoods ? ['salesmanCustomer'] : ['salesmanCustomer', 'salesman'];
        $orders = SalesmanVisitOrder::where('type', $type)->whereIn('salesman_id',
            $salesmenId)->with($with)->orderBy('id', 'desc')->paginate();
        if ($withOrderGoods) {
            $orders->each(function ($order) {
                $orderConf = cons('salesman.order');
                if ($order->type == $orderConf['type']['order']) {
                    $orderGoods = $order->orderGoods;

                    $orderGoodsLists = $order->orderGoods = $orderGoods->filter(function ($item) use ($orderConf) {
                        return $item->type == $orderConf['goods']['type']['order'];
                    });


                    $orderGoodsIds = $orderGoodsLists->pluck('goods_id')->toBase()->unique();
                    $orderGoodsNames = Goods::whereIn('id', $orderGoodsIds)->lists('name', 'id');

                    foreach ($orderGoodsLists as $goods) {
                        $goods->goodsName = isset($orderGoodsNames[$goods->goods_id]) ? $orderGoodsNames[$goods->goods_id] : '';
                    }

                    $order->orderGoods = $orderGoodsLists->values();

                    $mortgageGoodsLists = $orderGoods->filter(function ($item) use ($orderConf) {
                        return $item->type == $orderConf['goods']['type']['mortgage'];
                    });

                    $mortgageGoodsIds = $mortgageGoodsLists->pluck('goods_id')->toBase()->unique();

                    $mortgageGoodsNames = MortgageGoods::whereIn('id', $mortgageGoodsIds)->lists('goods_name', 'id');

                    foreach ($mortgageGoodsLists as $goods) {
                        $goods->goodsName = isset($mortgageGoodsNames[$goods->goods_id]) ? $mortgageGoodsNames[$goods->goods_id] : '';
                    }

                    $order->mortgageGoods = $mortgageGoodsLists->values();

                } else {

                    $orderGoods = $order->orderGoods;
                    $orderGoodsIds = $orderGoods->pluck('goods_id')->toBase()->unique();
                    $orderGoodsNames = Goods::whereIn('id', $orderGoodsIds)->lists('name', 'id');

                    foreach ($orderGoods as $goods) {
                        $goods->goodsName = isset($orderGoodsNames[$goods->goods_id]) ? $orderGoodsNames[$goods->goods_id] : '';
                    }
                    $order->orderGoods = $orderGoods;
                }

            });
        }
        return $orders;
    }

    /**
     * 格式化访问数据
     *
     * @param $visits
     * @param bool $hasGoodsImage
     * @return array
     */
    public function formatVisit($visits, $hasGoodsImage = false)
    {
        $orderConf = cons('salesman.order');

        $visitData = [];

        foreach ($visits as $visit) {
            $customerId = $visit->salesman_customer_id;

            //拜访客户信息
            $visitData[$customerId]['visit_id'] = $visit->id;
            $visitData[$customerId]['created_at'] = $visit->created_at;
            $visitData[$customerId]['customer_name'] = $visit->salesmanCustomer->name;
            $visitData[$customerId]['contact'] = $visit->salesmanCustomer->contact;
            $visitData[$customerId]['lng'] = $visit->salesmanCustomer->business_address_lng;
            $visitData[$customerId]['lat'] = $visit->salesmanCustomer->business_address_lat;
            $visitData[$customerId]['number'] = $visit->salesmanCustomer->number;
            $visitData[$customerId]['contact_information'] = $visit->salesmanCustomer->contact_information;
            $visitData[$customerId]['shipping_address_name'] = $visit->salesmanCustomer->shipping_address_name;

            $orderForm = $visit->orders->filter(function ($item) use ($orderConf) {
                return !is_null($item) && $item->type == $orderConf['type']['order'];
            })->first();

            if (!is_null($orderForm)) {
                $orderForm->display_fee && ($visitData[$customerId]['display_fee'][] = [
                    'created_at' => $orderForm->created_at,
                    'display_fee' => $orderForm->display_fee
                ]);
            }

            //拜访商品记录
            $goodsRecodeData = [];
            foreach ($visit->goodsRecord as $record) {
                if (!is_null($record)) {
                    $goodsRecodeData[$record->goods_id] = $record;
                }
            }

            $orderGoods = [];


            $visitData[$customerId]['amount'] = 0;
            $visitData[$customerId]['return_amount'] = 0;
            $mortgageGoods = [];

            $allGoods = $visit->orders->pluck('orderGoods')->collapse();
            $goodsIds = $allGoods->pluck('goods_id')->all();

            $goodsNames = Goods::whereIn('id', $goodsIds)->lists('name', 'id');

            foreach ($allGoods as $goods) {
                $orderGoods[$goods->type][] = $goods;

                if ($goods->type == $orderConf['goods']['type']['order']) {
                    $visitData[$customerId]['statistics'][$goods->goods_id]['order_num'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['order_num']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['order_num'] + $goods->num : $goods->num;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount']) ? bcadd($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'],
                        $goods->amount, 2) : $goods->amount;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['price'] = $goods->price;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['pieces'] = cons()->valueLang('goods.pieces',
                        $goods->pieces);
                    $hasGoodsImage && ( $visitData[$customerId]['statistics'][$goods->goods_id]['image_url'] = $goods->goods_image);

                } elseif ($goods->type == $orderConf['goods']['type']['return']) {
                    $visitData[$customerId]['statistics'][$goods->goods_id]['return_order_num'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['return_order_num']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['return_order_num'] + intval($goods->num) : intval($goods->num);
                    $visitData[$customerId]['statistics'][$goods->goods_id]['return_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['return_amount']) ? bcadd($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'],
                        $goods->amount, 2) : $goods->amount;
                }
                if ($goods->type == $orderConf['goods']['type']['mortgage']) {
                    $mortgageGoods[] = $goods;
                } else {
                    $visitData[$customerId]['statistics'][$goods->goods_id]['goods_id'] = $goods->goods_id;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['goods_name'] = $goodsNames[$goods->goods_id];
                    $visitData[$customerId]['statistics'][$goods->goods_id]['stock'] = isset($goodsRecodeData[$goods->goods_id]) ? $goodsRecodeData[$goods->goods_id]->stock : 0;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['production_date'] = isset($goodsRecodeData[$goods->goods_id]) ? $goodsRecodeData[$goods->goods_id]->production_date : 0;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'] : 0;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['return_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['return_amount']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['return_amount'] : 0;
                    $visitData[$customerId]['amount'] = bcadd($visitData[$customerId]['amount'],
                        $visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'], 2);
                    $visitData[$customerId]['return_amount'] = bcadd($visitData[$customerId]['return_amount'],
                        $visitData[$customerId]['statistics'][$goods->goods_id]['return_amount'], 2);
                }
            }

            //货抵商品
            foreach ($mortgageGoods as $mortgage) {
                $date = (new Carbon($mortgage->created_at))->toDateString();
                $visitData[$customerId]['mortgage'][$date][] = [
                    'name' => $mortgage->mortgage_goods_name,
                    'num' => $mortgage->num,
                    'pieces' => cons()->valueLang('goods.pieces', $mortgage->pieces)
                ];
            }

        }

        return $visitData;
    }
}