<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\MortgageGoods;
use App\Models\SalesmanCustomer;
use App\Models\SalesmanCustomerDisplayList;
use App\Models\SalesmanVisit;
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
    public function getOrderData(SalesmanVisitOrder $salesmanVisitOrder)
    {
        $orderTypeConf = cons('salesman.order');

        $data = [
            'order' => $salesmanVisitOrder
        ];

        if ($salesmanVisitOrder->type == $orderTypeConf['type']['order']) {

            $data['displayFee'] = $salesmanVisitOrder->displayFees;

            $data['mortgageGoods'] = $this->getOrderMortgageGoods([$salesmanVisitOrder]);
        }
        $data['orderGoods'] = $salesmanVisitOrder->orderGoods;
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


                if (!is_null($order->displayList)) {

                    foreach ($order->displayList as $item) {
                        if ($item->mortgage_goods_id == 0) {
                            $visitData[$customerId]['display_fee'][] = [
                                'month' => $item->month,
                                'created_at' => (string)$item->created_at,
                                'display_fee' => $item->used
                            ];
                        } else {
                            $month = $item->month;
                            $mortgage = $item->mortgageGoods;
                            $visitData[$customerId]['mortgage'][$month][]  = [
                                'id' => $item->mortgage_goods_id,
                                'name' => $mortgage->goods_name,
                                'pieces' => $mortgage->pieces,
                                'num' => $item->used,
                                'month' => $item->month,
                                'created_at' => (string)$item->created_at
                            ];
                        }
                    }
                }


               /* //陈列费
                if (!is_null($order->displayFees)) {
                    foreach ($order->displayFees as $displayFee) {
                        $visitData[$customerId]['display_fee'][] = [
                            'month' => $displayFee->month,
                            'created_at' => (string)$displayFee->created_at,
                            'display_fee' => $displayFee->used
                        ];
                    }
                }

                $mortgageGoods = $this->getOrderMortgageGoods($order);

                //货抵商品
                foreach ($mortgageGoods as $mortgage) {
                    $month = $mortgage['month'];
                    $visitData[$customerId]['mortgage'][$month][] = $mortgage;
                }*/

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



        }

        return compact('visitData', 'visitStatistics');
    }

    /**
     * 格式化客户订单
     *
     * @param $orders
     * @return \Illuminate\Support\Collection
     */
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
                    $result[$customerId]['orderGoods'][$orderGoods->goods_id]['order_num'] = isset($result[$customerId]['orderGoods'][$orderGoods->goods_id]['order_num']) ? ($result[$customerId]['orderGoods'][$orderGoods->goods_id]['order_num'] + $orderGoods->num) : $orderGoods->num;
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
                        'num' => $good->pivot->used,
                        'month' => $good->pivot->month,
                        'created_at' => (string)$order->created_at
                    ]);
                }

            }
        }
        return $mortgagesGoods;
    }

    /**
     * 获取订单陈列费
     *
     * @param $orders
     * @return \Illuminate\Support\Collection
     */
    public function getOrderDisplayFees($orders)
    {
        $displayFees = collect([]);
        foreach ($orders as $order) {
            if ($displayFee = $order->displayFees) {
                foreach ($displayFee as $item) {
                    $displayFees->push([
                        'month' => $item->month,
                        'used' => $item->used,
                        'time' => (string)$item->created_at
                    ]);
                }

            }
        }
        return $displayFees;
    }

    /**
     * 获取剩余陈列费
     *
     * @param \App\Models\SalesmanCustomer $customer
     * @param $month
     * @return mixed
     */
    public function surplusDisplayFee(SalesmanCustomer $customer, $month)
    {
        $display = $customer->displaySurplus()->where([
            'month' => $month,
            'mortgage_goods_id' => 0
        ])->first();

        return is_null($display) ? $customer->display_fee : $display->surplus;
    }

    /**
     * 获取剩余陈列商品
     *
     * @param $customer
     * @param $month
     * @param null $mortgages
     * @return array
     */
    public function surplusMortgageGoods($customer, $month, $mortgages = null)
    {
        $surplus = [];

        $mortgages = is_null($mortgages) ? $customer->mortgageGoods : $mortgages;
        //获取本月剩余陈列商品
        $surplusMortgageGoods = $customer->displaySurplus()->where(['month' => $month])->whereIn('mortgage_goods_id',
            $mortgages->pluck('id'))->orderBy('id', 'desc')->get();

        foreach ($mortgages as $mortgage) {
            $flag = false;
            foreach ($surplusMortgageGoods as $item) {
                if ($item->mortgage_goods_id == $mortgage->id) {
                    $surplus[] = [
                        'id' => $mortgage->id,
                        'name' => $mortgage->goods_name,
                        'surplus' => $item->surplus,
                        'pieces_name' => $mortgage->pieces_name
                    ];
                    $flag = true;
                    break;
                }
            }
            if (!$flag) {
                $surplus[] = [
                    'id' => $mortgage->id,
                    'name' => $mortgage->goods_name,
                    'surplus' => $mortgage->pivot->total,
                    'pieces_name' => $mortgage->pieces_name
                ];
            }

        }
        return $surplus;
    }


    /**
     * 验证陈列费
     *
     * @param $displayFee
     * @param $orderAmount
     * @param \App\Models\SalesmanCustomer $customer
     * @param \App\Models\SalesmanVisitOrder|null $salesmanVisitOrder
     * @return array|bool
     */
    public function validateDisplayFee(
        $displayFee,
        $orderAmount,
        SalesmanCustomer $customer,
        SalesmanVisitOrder $salesmanVisitOrder = null
    ) {
        $totalCash = 0;
        $displayList = [];

        foreach ($displayFee as $month => $item) {
            $customerSurplus = $this->surplusDisplayFee($customer, $month);
            $orderDisplayFee = 0;
            if (!is_null($salesmanVisitOrder)) {
                $orderDisplayFee = $salesmanVisitOrder->displayList()->where([
                    'month' => $month,
                    'mortgage_goods_id' => 0
                ])->pluck('used');
            }
            if ($item > bcadd($customerSurplus, $orderDisplayFee, 2)) {
                return false;
            }
            $totalCash = bcadd($totalCash, $item, 2);
            $displayList[] = new SalesmanCustomerDisplayList([
                'salesman_customer_id' => $customer->id,
                'month' => $month,
                'used' => $item,
            ]);
        }
        if ($totalCash > $orderAmount) {
            return false;
        }
        return $displayList;
    }

    /**
     * 验证陈列商品
     *
     * @param $mortgages
     * @param \App\Models\SalesmanCustomer $customer
     * @param \App\Models\SalesmanVisitOrder|null $salesmanVisitOrder
     * @return array|bool
     */
    public function validateMortgage(
        $mortgages,
        SalesmanCustomer $customer,
        SalesmanVisitOrder $salesmanVisitOrder = null
    ) {
        $displayList = [];

        foreach ($mortgages as $month => $mortgage) {
            $customerSurplus = $this->surplusMortgageGoods($customer, $month);
            $orderMortgageGoodsNum = 0;
            foreach ($mortgage as $detail) {
                $flag = false;
                foreach ($customerSurplus as $item) {
                    if ($detail['id'] == $item['id']) {
                        if (!is_null($salesmanVisitOrder)) {
                            $orderMortgageGoodsNum = $salesmanVisitOrder->displayList()->where([
                                'month' => $month,
                                'mortgage_goods_id' => $detail['id']
                            ])->pluck('used');
                        }

                        if ($detail['num'] > bcadd($item['surplus'], $orderMortgageGoodsNum)) {
                            return false;
                        }
                        $displayList[] = new SalesmanCustomerDisplayList([
                            'salesman_customer_id' => $customer->id,
                            'month' => $month,
                            'used' => $detail['num'],
                            'mortgage_goods_id' => $item['id']
                        ]);
                        $flag = true;
                        break;
                    }
                }
                if (!$flag) {
                    return false;
                }
            }

        }
        return $displayList;

    }
}