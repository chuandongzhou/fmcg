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
            $goods = $salesmanVisitOrder->orderGoods->load('goods');
            $orderGoods = $goods->filter(function ($item) use ($orderGoodsConf) {
                return $item->type == $orderGoodsConf['order'];
            });
            $data['orderGoods'] = $orderGoods;
            $data['mortgageGoods'] = $this->getOrderMortgageGoods([$salesmanVisitOrder]);
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
                    // $orderGoods = $order->orderGoods;
                    $orderGoodsIds = $order->orderGoods->pluck('goods_id')->toBase()->unique();
                    $orderGoodsNames = Goods::whereIn('id', $orderGoodsIds)->lists('name', 'id');

                    $order->orderGoods->each(function ($goods) use ($orderGoodsNames) {
                        $goods->goodsName = isset($orderGoodsNames[$goods->goods_id]) ? $orderGoodsNames[$goods->goods_id] : '';
                    });

                    $order->mortgageGoods = $this->getOrderMortgageGoods([$order]);

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
            $visitData[$customerId]['created_at'] = (string)$visit->created_at;
            $visitData[$customerId]['customer_name'] = $visit->salesmanCustomer->name;
            $visitData[$customerId]['contact'] = $visit->salesmanCustomer->contact;
            $visitData[$customerId]['lng'] = $visit->salesmanCustomer->business_address_lng;
            $visitData[$customerId]['lat'] = $visit->salesmanCustomer->business_address_lat;
            $visitData[$customerId]['number'] = $visit->salesmanCustomer->number;
            $visitData[$customerId]['contact_information'] = $visit->salesmanCustomer->contact_information;
            $visitData[$customerId]['shipping_address_name'] = $visit->salesmanCustomer->shipping_address_name;

            $orderForm = $visit->orders->filter(function ($item) use ($orderConf) {
                return !is_null($item) && $item->type == $orderConf['type']['order'];
            });

            if ($orderForm->count()) {
                $order = $orderForm->first();
                $order->display_fee && ($visitData[$customerId]['display_fee'][] = [
                    'created_at' => (string)$order->created_at,
                    'display_fee' => $order->display_fee
                ]);
            }

            //拜访商品记录
            $goodsRecord = $visit->goodsRecord;
            $goodsRecodeData = [];
            foreach ($goodsRecord as $record) {
                if (!is_null($record)) {
                    $goodsRecodeData[$record->goods_id] = $record;
                }
            }


            $visitData[$customerId]['amount'] = isset($visitData[$customerId]['amount']) ? $visitData[$customerId]['amount'] : 0;
            $visitData[$customerId]['return_amount'] = isset($visitData[$customerId]['return_amount']) ? $visitData[$customerId]['return_amount'] : 0;
            $allGoods = $orderGoods = $visit->orders->pluck('orderGoods')->collapse();

            foreach ($goodsRecord as $key => $record) {
                $tag = false;
                foreach ($orderGoods as $goods) {
                    if ($record->goods_id == $goods->goods_id && $record->salesman_visit_id == $goods->salesman_visit_id) {
                        $tag = true;
                        break;
                    }
                }
                if (!$tag) {
                    $allGoods->push($record);
                }
            }
            $goodsIds = $allGoods->pluck('goods_id')->all();
            $goodsNames = Goods::whereIn('id', $goodsIds)->lists('name', 'id');


            foreach ($allGoods as $goods) {
                if ($goods->type == $orderConf['goods']['type']['order']) {
                    $visitData[$customerId]['statistics'][$goods->goods_id]['order_num'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['order_num']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['order_num'] + $goods->num : $goods->num;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount']) ? bcadd($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'],
                        $goods->amount, 2) : $goods->amount;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['price'] = $goods->price;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['pieces'] = $goods->pieces;

                    $visitData[$customerId]['amount'] = bcadd($visitData[$customerId]['amount'], $goods->amount, 2);

                } elseif ($goods->type == $orderConf['goods']['type']['return']) {
                    $visitData[$customerId]['statistics'][$goods->goods_id]['return_order_num'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['return_order_num']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['return_order_num'] + intval($goods->num) : intval($goods->num);
                    $visitData[$customerId]['statistics'][$goods->goods_id]['return_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['return_amount']) ? bcadd($visitData[$customerId]['statistics'][$goods->goods_id]['return_amount'],
                        $goods->amount, 2) : $goods->amount;

                    $visitData[$customerId]['return_amount'] = bcadd($visitData[$customerId]['return_amount'],
                        $goods->amount, 2);
                }

                $hasGoodsImage && ($visitData[$customerId]['statistics'][$goods->goods_id]['image_url'] = $goods->goods_image);
                $visitData[$customerId]['statistics'][$goods->goods_id]['goods_id'] = $goods->goods_id;
                $visitData[$customerId]['statistics'][$goods->goods_id]['goods_name'] = $goodsNames[$goods->goods_id];
                $visitData[$customerId]['statistics'][$goods->goods_id]['stock'] = isset($goodsRecodeData[$goods->goods_id]) ? $goodsRecodeData[$goods->goods_id]->stock : 0;
                $visitData[$customerId]['statistics'][$goods->goods_id]['production_date'] = isset($goodsRecodeData[$goods->goods_id]) ? $goodsRecodeData[$goods->goods_id]->production_date : 0;
                $visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'] : 0;
                $visitData[$customerId]['statistics'][$goods->goods_id]['return_order_num'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['return_order_num']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['return_order_num'] : 0;
                $visitData[$customerId]['statistics'][$goods->goods_id]['return_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['return_amount']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['return_amount'] : 0;
                $visitData[$customerId]['statistics'][$goods->goods_id]['order_num'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['order_num']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['order_num'] : 0;

            }

            $mortgageGoods = $this->getOrderMortgageGoods($orderForm);


            //货抵商品
            foreach ($mortgageGoods as $mortgage) {
                $date = (new Carbon($mortgage['created_at']))->toDateString();
                $visitData[$customerId]['mortgage'][$date][] = $mortgage;
            }

        }

        return $visitData;
    }

    /**
     * 获取订单抵货商品
     *
     * @param $orders
     * @return array
     */
    public function getOrderMortgageGoods($orders)
    {
        $mortgagesGoods = collect([]);
        foreach ($orders as $order) {
            if ($goods = $order->mortgageGoods) {
                foreach ($goods as $good) {
                    $mortgagesGoods->push([
                        'id' => $good->id,
                        'name' => $good->goods_name,
                        'pieces' => $good->pieces,
                        'num' => $good->pivot->num,
                        'created_at' => (string)$order->created_at
                    ]);
                }

            }
        }
        return $mortgagesGoods;
    }
}