<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Http\Controllers\Api\V1\Controller;
use App\Models\ConfirmOrderDetail;
use App\Models\DispatchTruck;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\Salesman;
use App\Models\SalesmanVisitOrderGoods;
use App\Services\GoodsService;
use App\Services\OrderService;
use App\Services\ShippingAddressService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Gate;
use DB;


class DispatchTruckController extends Controller
{

    //记录最低单位商品数量
    protected $minLevelGoodsNum;

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function index(Request $request)
    {
        $salesmanUser = salesman_auth()->user();
        $dispatchTrucks = $salesmanUser->dispatchTrucks()->with('truck')->orderBy('id', 'DESC')->paginate()->toArray();
        return $this->success(compact('dispatchTrucks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $dispatchId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(Request $request, $dispatchId)
    {
//        $data = $request->all();

        $data = [
            'salesman_customer_id' => 35,
            'goods' => [
                [
                    'id' => 6,
                    'price' => '169',
                    'num' => 6,
                    'pieces' => 0
                ]
            ],
            "gifts" => [
                [
                    "id" => 6,
                    "num" => 13,
                    "pieces" => 2
                ]
            ],

            'order_remark' => '',
            'x_lng' => '',
            'y_lat' => '',
            'address' => ''
        ];
        // dd($data);

        $salesman = salesman_auth()->user();

        $dispatchTruck = DispatchTruck::with('truckSalesGoods.goodsPieces')->find($dispatchId);

        if (Gate::forUser($salesman)->denies('validate-salesman-dispatch-truck', $dispatchTruck)) {
            return $this->error('车销单不存在');
        }
        if ($dispatchTruck->status != cons('dispatch_truck.status.delivering')) {
            return $this->error('该车销单不能订货');
        }

        $customer = $salesman->customers()->find($data['salesman_customer_id']);

        if (is_null($customer)) {
            return $this->error('客户不存在');
        }

        // 验证车销单库存是否足够
        $orderGoodsDetail = $this->_formatGoodsNum($data['goods'], $data['gifts']); //商品加赠品总量

        $inventoryFill = $this->_validateInventory($dispatchTruck->truckSalesGoods, $orderGoodsDetail['result']);

        if (!$inventoryFill) {
            return $this->error('商品库存不足');
        }

        $visit = $salesman->visits()->create([
                'salesman_customer_id' => $data['salesman_customer_id'],
                'x_lng' => isset($data['x_lng']) ? $data['x_lng'] : '',
                'y_lat' => isset($data['y_lat']) ? $data['y_lat'] : '',
                'address' => isset($data['address']) ? $data['address'] : '',
                'shop_id' => $salesman->shop_id,
            ]
        );
        $result = DB::transaction(function () use (
            $salesman,
            $data,
            $customer,
            $visit,
            $dispatchTruck,
            $orderGoodsDetail,
            $dispatchId
        ) {
            try {
                $orderConf = cons('salesman.order');
                if ($visit->exists) {
                    if (!empty($data['goods'])) {
                        //有商品下单
                        $salesmanOrderData['salesman_visit_id'] = $visit->id;
                        $salesmanOrderData['salesman_customer_id'] = $data['salesman_customer_id'];
                        $salesmanOrderData['order_remark'] = isset($data['order_remark']) ? $data['order_remark'] : '';
                        $salesmanOrderData['type'] = $orderConf['type']['order'];
                        $salesmanOrderData['shop_id'] = $salesman->shop_id;
                        $salesmanOrderData['amount'] = $orderGoodsDetail['amount'];
                        $salesmanOrderData['status'] = cons('salesman.order.status.passed');
                        // 减去车销单库存
                        $result = $this->_descTruckGoodsInventory($dispatchTruck->truckSalesGoods,
                            $orderGoodsDetail['result']);
                        if (!$result) {
                            return false;
                        }

                        $orderForm = $salesman->orders()->create($salesmanOrderData);
                        if ($orderForm->exists) {
                            $orderGoodsArr = [];
                            foreach ($data['goods'] as $orderGoods) {
                                $goodsItem = [
                                    'goods_id' => $orderGoods['id'],
                                    'price' => $orderGoods['price'],
                                    'num' => $orderGoods['num'],
                                    'pieces' => $orderGoods['pieces'],
                                    'amount' => bcmul($orderGoods['price'], $orderGoods['num'], 2),
                                    'salesman_visit_id' => $visit->id,
                                    'type' => $orderConf['goods']['type']['order']
                                ];
                                $orderGoodsArr[] = new SalesmanVisitOrderGoods($goodsItem);
                            }
                            //订单商品
                            $orderForm->orderGoods()->saveMany($orderGoodsArr);

                            //礼物
                            if ($gifts = array_get($data, 'gifts')) {
                                $giftList = [];
                                foreach ($gifts as $gift) {
                                    $giftList[$gift['id']] = [
                                        'num' => $gift['num'],
                                        'pieces' => $gift['pieces'],
                                    ];
                                }
                                $orderForm->gifts()->sync($giftList);
                            }
                        }
                        // 添加平台订单
                        $addPlatformResult = $this->_addPlatformOrder($orderForm, $salesman,$dispatchId);
                        return $addPlatformResult === true ? 'success' : false;

                    }
                    return 'success';
                }
            }catch (\Exception $e){
                $visit->delete();
                return false;
            }
        });
        if ($result === 'success') {
            return $this->success(['id' => $visit->id]);
        } else {
//            $visit->delete();
            return $this->error(is_string($result) ? $result : '下单时出现错误');
        }
    }

    /**
     * 车销单详情
     *
     * @param $dispatchId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function detail($dispatchId)
    {
        $salesmanUser = salesman_auth()->user();
        $dispatchTruck = DispatchTruck::with('deliveryMans', 'salesman', 'orders.goods.goodsPieces',
            'orders.shippingAddress.address',
            'truckSalesGoods.goodsPieces')->find($dispatchId);

        if (Gate::forUser($salesmanUser)->denies('validate-salesman-dispatch-truck', $dispatchTruck)) {
            return $this->error('车销单不存在');
        }
        $truckSalesGoods = $this->_totalTruckSalesGoods($dispatchTruck->orders);

        $dispatchTruck->truckSoldGoods = $this->_formatTruckSalesGoods($truckSalesGoods);

        $dispatchTruck->dispatchTruckSurplus = $this->_formatTruckSurplusGoods($dispatchTruck->truckSalesGoods);

        return $this->success(compact('dispatchTruck'));
    }

    /**
     * 获取车销单上的商品
     *
     * @param $dispatchId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function dispatchGoods($dispatchId)
    {
        $salesmanUser = salesman_auth()->user();
        $dispatchTruck = DispatchTruck::find($dispatchId);

        if (Gate::forUser($salesmanUser)->denies('validate-salesman-dispatch-truck', $dispatchTruck)) {
            return $this->error('车销单不存在');
        }
        $truckSalesGoods = $this->_formatTruckSurplusGoods($dispatchTruck->truckSalesGoods, true);
        return $this->success(compact('truckSalesGoods'));
    }

    /**
     * 已销售商品总计
     *
     * @param $truckSalesOrders
     * @return array
     */
    private function _totalTruckSalesGoods($truckSalesOrders)
    {

        $salesGoods = collect();
        foreach ($truckSalesOrders as $order) {
            $order->setAppends(['user_shipping_address_name']);
            foreach ($order->goods as $goods) {
                $salesGoods->push($goods);
            }
        }
        $data = [];
        foreach ($salesGoods as $item) {
            $goodsId = $item->id;
            $pivot = $item->pivot;
            if (isset($data[$goodsId])) {
                $data[$goodsId]['order_count']++;
                $data[$goodsId]['num'][$pivot->pieces] = isset($data[$goodsId]['num'][$pivot->pieces]) ? $data[$goodsId]['num'][$pivot->pieces] + $pivot->num : $pivot->num;
            } else {
                $data[$goodsId] = [
                    'id' => $goodsId,
                    'name' => $item->name,
                    'image_url' => $item->image_url,
                    'order_count' => 1,
                    'num' => [
                        $pivot->pieces => $pivot->num
                    ],
                    'goods_pieces' => $item->goodsPieces->toArray()
                ];
                continue;
            }
        }
        return $data;
    }

    /**
     * 格式化已销售商品
     *
     * @param $truckSalesGoods
     * @return array
     */
    private function _formatTruckSalesGoods($truckSalesGoods)
    {
        $result = [];
        foreach ($truckSalesGoods as $item) {
            $item['num'] = $this->_convertGoodsNum($item['goods_pieces'], $item['num']);
            unset($item['goods_pieces']);
            $result[] = $item;
        }
        return $result;
    }

    /**
     * 获取车销单剩余商品
     *
     * @param $truckSalesGoods
     * @param bool $includeGoodsDetail
     * @return array
     */
    private function _formatTruckSurplusGoods($truckSalesGoods, $includeGoodsDetail = false)
    {
        $result = [];
        foreach ($truckSalesGoods as $goods) {
            $pieces = $goods->goodsPieces;
            $minPieces = GoodsService::getMinPieces($pieces);
            $item = [
                'id' => $goods->id,
                'image_url' => $goods->image_url,
                'name' => $goods->name,
                'surplus' => $this->_convertGoodsNum($pieces->toArray(), [$minPieces => $goods->pivot->surplus])
            ];
            if ($includeGoodsDetail) {
                $item['price_retailer'] = $goods->price_retailer;
                $item['price_wholesaler'] = $goods->price_wholesaler;
                $item['pieces_retailer'] = $goods->pieces_retailer;
                $item['pieces_wholesaler'] = $goods->pieces_wholesaler;
            }
            $result[] = $item;
        }

        return $result;
    }

    /**
     * 获取商品数量详情
     *
     * @param $goodsPieces
     * @param $goodsSalesNum
     * @return string
     */
    private function _convertGoodsNum($goodsPieces, $goodsSalesNum)
    {
        $goodsSalesArray = GoodsService::formatGoodsPieces($goodsPieces, $goodsSalesNum);

        $piecesHtml = '';
        foreach ($goodsSalesArray as $pieces => $num) {
            $piecesHtml .= $num . cons()->valueLang('goods.pieces', $pieces);
        }
        return $piecesHtml;
    }

    /**
     * 判断要下单的商品库存是否足够
     *
     * @param $truckSalesGoods '库存'
     * @param $orderGoods '要下单的商品'
     * @return bool
     */
    private function _validateInventory($truckSalesGoods, $orderGoods)
    {
        $truckSalesGoods = $truckSalesGoods->keyBy('id')->toArray();
        foreach ($orderGoods as $goodsId => $item) {
            if (!($salesGoods = array_get($truckSalesGoods, $goodsId))) {
                return false;
            }
            $goodsPieces = $salesGoods['goods_pieces'];

            $this->minLevelGoodsNum[$goodsId] = GoodsService::getTheLowLevelNum($goodsPieces, $item);

            if ($this->minLevelGoodsNum[$goodsId] > $salesGoods['pivot']['surplus']) {
                return false;
            }
        }

        return true;

    }

    /**
     * 减少车销单库存
     *
     * @param $truckSalesGoods
     * @param $orderGoods
     * @return bool
     */
    private function _descTruckGoodsInventory($truckSalesGoods, $orderGoods)
    {
        $truckSalesGoods = $truckSalesGoods->keyBy('id');
        foreach ($orderGoods as $goodsId => $item) {
            if (!($salesGoods = array_get($truckSalesGoods, $goodsId))) {
                return false;
            }
            $goodsPieces = $salesGoods->goodsPieces;

            $num = $this->minLevelGoodsNum[$goodsId] ?? GoodsService::getTheLowLevelNum($goodsPieces, $item);
            $pivot = $salesGoods->pivot;
            $pivot->update(['surplus' => $pivot->surplus - $num]);

        }

        return true;

    }

    /**
     * 获取下单商品总量
     *
     * @param $goods
     * @param $gifts
     * @return array
     */
    private function _formatGoodsNum($goods, $gifts)
    {
        $result = [];
        $amount = 0;
        foreach ($goods as $item) {
            $result[$item['id']] = [
                $item['pieces'] => $item['num']
            ];
            $amount += bcmul($item['price'], $item['num'], 2);
        }
        foreach ($gifts as $gift) {
            if (isset($result[$gift['id']])) {
                if (isset($result[$gift['id']][$gift['pieces']])) {
                    $result[$gift['id']][$gift['pieces']] += $gift['num'];
                } else {
                    $result[$gift['id']][$gift['pieces']] = $gift['num'];
                }
            } else {
                $result[$gift['id']] = [
                    $gift['pieces'] => $gift['num']
                ];
            }
        }

        return compact('result', 'amount');
    }

    /**
     * 添加平台订单
     *
     * @param $salesmanVisitOrder
     * @param \App\Models\Salesman $salesman
     * @return array|bool
     */
    private function _addPlatformOrder($salesmanVisitOrder, Salesman $salesman,$dispatchTruckId)
    {
        $syncConf = cons('salesman.order.sync');
        $orderConf = cons('order');
        $shippingAddressService = new ShippingAddressService();
        $orderData = [
            'dispatch_truck_id' => $dispatchTruckId,
            'delivery_finished_at'  => Carbon::now(),
            'user_id' => $salesmanVisitOrder->customer_user_id,
            'shop_id' => $salesman->shop_id,
            'price' => $salesmanVisitOrder->amount,
            'pay_type' => $syncConf['pay_type'],
            'pay_way' => $syncConf['pay_way'],
            'type' => cons('order.type.dispatch_truck'),
            'status' => $orderConf['status']['send'],
            'numbers' => (new OrderService())->getNumbers($salesman->shop_id),
            'shipping_address_id' => $shippingAddressService->copySalesmanCustomerShippingAddressToSnapshot($salesmanVisitOrder->SalesmanCustomer),
            'remark' => ($salesmanVisitOrder->order_remark ? '订单备注:' . $salesmanVisitOrder->order_remark . ';' : '') . ($salesmanVisitOrder->display_remark ? '陈列费备注:' . $salesmanVisitOrder->display_remark : '')
        ];
        if (!$orderData['shipping_address_id']) {
            return false;
        }
        $orderTemp = Order::create($orderData);
        if ($orderTemp->exists) {//添加订单成功,修改orderGoods中间表信息
            $orderGoods = [];
            foreach ($salesmanVisitOrder->orderGoods as $goods) {
                // 添加订单商品
                $orderGoods[] = new OrderGoods([
                    'goods_id' => $goods->goods_id,
                    'price' => $goods->price,
                    'num' => $goods->num,
                    'pieces' => $goods->pieces,
                    'total_price' => $goods->amount,
                ]);

                $confirmOrderDetail = ConfirmOrderDetail::where([
                    'goods_id' => $goods->goods_id,
                    'customer_id' => $salesmanVisitOrder->salesman_customer_id
                ])->first();
                if (empty($confirmOrderDetail)) {
                    ConfirmOrderDetail::create([
                        'goods_id' => $goods->goods_id,
                        'price' => $goods->price,
                        'pieces' => $goods->pieces,
                        'shop_id' => !empty($salesmanVisitOrder->salesmanCustomer->shop_id) ? $salesmanVisitOrder->salesmanCustomer->shop_id : 0,
                        'customer_id' => $salesmanVisitOrder->salesman_customer_id,
                    ]);
                } else {
                    $confirmOrderDetail->fill(['price' => $goods->price, 'pieces' => $goods->pieces])->save();
                }
            }

            //礼物
            if ($gifts = $salesmanVisitOrder->gifts) {
                foreach ($gifts as $gift) {
                    $orderGoods[] = new OrderGoods([
                        'type' => cons('order.goods.type.gift_goods'),
                        'goods_id' => $gift->id,
                        'price' => 0,
                        'num' => $gift->pivot->num,
                        'pieces' => $gift->pivot->pieces,
                        'total_price' => 0,
                    ]);
                }
            }

            if (!empty($orderGoods)) {
                //保存抵费商品
                if (!$orderTemp->orderGoods()->saveMany($orderGoods)) {
                    return false;
                }
            }
        } else {
            return false;
        }
        return true;
    }

}
