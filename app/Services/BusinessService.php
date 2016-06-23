<?php

namespace App\Services;

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
            $orderGoods = $salesmanVisitOrder->orderGoods->filter(function ($item) use ($orderGoodsConf) {
                return $item->type == $orderGoodsConf['order'];
            });

            $mortgageGoods = $salesmanVisitOrder->orderGoods->filter(function ($item) use ($orderGoodsConf) {
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
        $salesmen = $shop->salesmen()->with(['orders', 'visits'])->get()->each(function ($salesman) use ($startDate, $endDate) {
            $orders = $salesman->orders->filter(function ($order) use ($startDate, $endDate) {
                return $order->created_at >= $startDate && $order->created_at <= $endDate;
            });

            $orderForms = $orders->filter(function ($order) {
                return $order->type == cons('salesman.order.type.order');
            });


            $salesman->visitCustomerCount = $salesman->visits->pluck('salesman_customer_id')->toBase()->unique()->count();
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
}