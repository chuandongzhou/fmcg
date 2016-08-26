<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\MortgageGoods;
use App\Models\SalesmanCustomer;
use App\Models\SalesmanVisitGoodsRecord;
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

            $visitOrderForms = $orderForms->filter(function ($order) {
                return $order->salesman_visit_id > 0;
            });

            $visits = $salesman->visits->filter(function ($visit) use ($startDate, $endDate) {
                return $visit->created_at >= $startDate && $visit->created_at <= $endDate;
            });

            $salesman->visitCustomerCount = $visits->pluck('salesman_customer_id')->toBase()->unique()->count();
            $salesman->orderFormSumAmount = $orderForms->sum('amount');
            $salesman->visitOrderFormSumAmount = $visitOrderForms->sum('amount');
            $salesman->orderFormCount = $orderForms->count();
            $salesman->visitOrderFormCount = $visitOrderForms->count();

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
     * @param $data
     * @param array $with
     * @return mixed
     */
    public function getOrders($salesmenId, $data = [], $with = ['salesmanCustomer', 'salesman'])
    {

        if (isset($data['salesman_id']) && ($salesmanId = $data['salesman_id'])) {
            $exists = $salesmenId->toBase()->contains($salesmanId);
            if ($exists) {
                $orders = SalesmanVisitOrder::OfData($data)->with($with)->orderBy('id', 'desc')->paginate();
            } else {
                $orders = SalesmanVisitOrder::whereIn('salesman_id')->OfData(array_except($data,
                    'salesman_id'))->with($with)->orderBy('id', 'desc')->paginate();
            }

        } else {

            $orders = SalesmanVisitOrder::whereIn('salesman_id',
                $salesmenId)->OfData($data)->with($with)->orderBy('id', 'desc')->paginate();
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

        $visitStatistics = [];

        foreach ($visits as $visit) {
            $customerId = $visit->salesman_customer_id;
            $customer = $visit->salesmanCustomer;

            //拜访客户信息
            $visitData[$customerId]['visit_id'] = $visit->id;
            $visitData[$customerId]['created_at'] = (string)$visit->created_at;
            $visitData[$customerId]['customer_name'] = $customer->name;
            $visitData[$customerId]['contact'] = $customer->contact;
            $visitData[$customerId]['lng'] = $customer->business_address_lng;
            $visitData[$customerId]['lat'] = $customer->business_address_lat;
            $visitData[$customerId]['number'] = $customer->number;
            $visitData[$customerId]['contact_information'] = $customer->contact_information;
            $visitData[$customerId]['shipping_address_name'] = $customer->shipping_address_name;

            //订单货单
            $orderForm = $visit->orders->filter(function ($item) use ($orderConf) {
                return !is_null($item) && $item->type == $orderConf['type']['order'];
            });
            //退货单

            $returnOrderForm = $visit->orders->filter(function ($item) use ($orderConf) {
                return !is_null($item) && $item->type == $orderConf['type']['return_order'];
            });

            if ($orderForm->count()) {
                $order = $orderForm->first();
                //拜访订货单数
                $visitStatistics['order_form_count'] = isset($visitStatistics['order_form_count']) ? ++$visitStatistics['order_form_count'] : 1;
                //拜访订货金额
                $visitStatistics['order_form_amount'] = isset($visitStatistics['order_form_amount']) ? bcadd($visitStatistics['order_form_amount'],
                    $order->amount, 2) : $order->amount;

                $order->display_fee && ($visitData[$customerId]['display_fee'][] = [
                    'created_at' => (string)$order->created_at,
                    'display_fee' => $order->display_fee
                ]);
            }

            if ($returnOrder = $returnOrderForm->first()) {
                //拜访退货单数
                $visitStatistics['return_order_count'] = isset($visitStatistics['return_order_count']) ? ++$visitStatistics['return_order_count'] : 1;

                //拜访退货金额
                $visitStatistics['return_order_amount'] = isset($visitStatistics['return_order_amount']) ? bcadd($visitStatistics['return_order_amount'],
                    $returnOrder->amount, 2) : $returnOrder->amount;
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


            foreach ($allGoods as $goods) {
                if ($goods instanceof SalesmanVisitGoodsRecord) {
                    $customerTypeName = $customer->shop_id && $customer->shop ? array_search($customer->shop->user_type,
                        cons('user.type')) : 'retailer';

                }
                if ($goods->type == $orderConf['goods']['type']['order']) {
                    $visitData[$customerId]['statistics'][$goods->goods_id]['order_num'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['order_num']) ? $visitData[$customerId]['statistics'][$goods->goods_id]['order_num'] + $goods->num : $goods->num;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'] = isset($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount']) ? bcadd($visitData[$customerId]['statistics'][$goods->goods_id]['order_amount'],
                        $goods->amount, 2) : $goods->amount;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['price'] = $goods instanceof SalesmanVisitGoodsRecord ? $goods->goods->{'price_' . $customerTypeName} : $goods->price;
                    $visitData[$customerId]['statistics'][$goods->goods_id]['pieces'] = $goods instanceof SalesmanVisitGoodsRecord ? $goods->goods->{'pieces_' . $customerTypeName} : $goods->pieces;

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
                $visitData[$customerId]['statistics'][$goods->goods_id]['goods_name'] = $goods instanceof SalesmanVisitGoodsRecord ? $goods->goods->name : $goods->goods_name;
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

        return compact('visitData', 'visitStatistics');
    }

    public function formatOrdersByCustomer($orders)
    {
        $result = [];

        if ($orders->count()) {
            $customers = SalesmanCustomer::whereIn('id',
                $orders->pluck('salesman_customer_id')->toBase()->unique())->with('businessAddress',
                'shop')->get()->keyBy('id');

            foreach ($orders as $order) {
                $customerId = $order->salesman_customer_id;
                $result[$customerId]['number'] = $customers[$customerId]['number'];
                $result[$customerId]['shop_name'] = $customers[$customerId]->shop ? $customers[$customerId]->shop->name : '';
                $result[$customerId]['contact'] = $customers[$customerId]->contact;
                $result[$customerId]['contact_information'] = $customers[$customerId]->contact_information;
                $result[$customerId]['business_address'] = $customers[$customerId]->businessAddress->address_name;
                $result[$customerId]['orders'] = isset($result[$customerId]['orders']) ? $result[$customerId]['orders']->push($order) : collect([$order]);

                foreach ($order->orderGoods as $orderGoods) {
                    $result[$customerId]['orderGoods'][$orderGoods->goods_id]['name'] = $orderGoods->goods_name;
                    $result[$customerId]['orderGoods'][$orderGoods->goods_id]['order_num'] = isset($result[$customerId]['orderGoods'][$orderGoods->goods_id]['order_num']) ? ++$result[$customerId]['orderGoods'][$orderGoods->goods_id]['order_num'] : 1;
                    $result[$customerId]['orderGoods'][$orderGoods->goods_id]['order_amount'] = isset($result[$customerId]['orderGoods'][$orderGoods->goods_id]['order_amount'])
                        ? bcadd($result[$customerId]['orderGoods'][$orderGoods->goods_id]['order_amount'],
                            $orderGoods->amount, 2) : $orderGoods->amount;
                }

            }

        }

        return collect($result);
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