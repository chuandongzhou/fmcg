<?php

namespace App\Http\Controllers\Api\V1\Business;


use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\v1\DeleteMortgageGoodsRequest;
use App\Http\Requests\Api\v1\UpdateSalesmanVisitOrderGoodsRequest;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\SalesmanVisitOrder;
use App\Models\SalesmanVisitOrderGoods;
use App\Services\BusinessService;
use App\Services\ShippingAddressService;
use Illuminate\Http\Request;
use Gate;
use DB;

class SalesmanVisitOrderController extends Controller
{

    /**
     * get orderForms by salesmanId
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function orderForms(Request $request)
    {
        $salesmenId = salesman_auth()->id();

        $data = $request->only(['status', 'start_date', 'end_date']);
        $data = array_merge($data, ['type' => cons('salesman.order.type.order')]);

        $orders = (new BusinessService())->getOrders([$salesmenId], $data, ['salesmanCustomer', 'salesman', 'order']);
        return $this->success(['orders' => $orders->toArray()]);
    }

    /**
     * get returnOrders by salesmanId
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function returnOrders(Request $request)
    {
        $salesmenId = salesman_auth()->id();

        $data = $request->only(['status', 'start_date', 'end_date']);
        $data = array_merge($data, ['type' => cons('salesman.order.type.return_order')]);

        $orders = (new BusinessService())->getOrders([$salesmenId], $data);
        return $this->success(['orders' => $orders->toArray()]);
    }

    /**
     * 订单操作
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SalesmanVisitOrder $salesmanVisitOrder
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(Request $request, SalesmanVisitOrder $salesmanVisitOrder)
    {
        if (Gate::denies('validate-salesman-order', $salesmanVisitOrder)) {
            return $this->error('订单不存在');
        }
        $attributes = $request->except('salesman_id', 'start_date', 'end_date');


        if ($salesmanVisitOrder->can_sync && isset($attributes['status'])) {
            $this->_syncOrders([$salesmanVisitOrder]);
        }

        return $salesmanVisitOrder->fill($attributes)->save() ? $this->success('操作成功') : $this->error('订单不存在');
    }

    /**
     * 订单商品修改
     *
     * @param \App\Http\Requests\Api\v1\UpdateSalesmanVisitOrderGoodsRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function updateOrderGoods(UpdateSalesmanVisitOrderGoodsRequest $request)
    {
        if ($orderId = $request->input('order_id')) {
            $salesmanVisitOrder = SalesmanVisitOrder::find($orderId);

            $result = $this->_updateOrderGoods($salesmanVisitOrder, $request);

        } else {
            $goodsId = $request->input('id');
            $orderGoods = SalesmanVisitOrderGoods::with('salesmanVisitOrder')->find($goodsId);
            if (is_null($orderGoods)) {
                return $this->error('订单不存在');
            }
            $salesmanVisitOrder = $orderGoods->salesmanVisitOrder;
            $result = $this->_updateOrderGoods($salesmanVisitOrder, $request, $orderGoods);
        }

        return $result === 'success' ? $this->success('修改成功') : $this->error('修改订单时出现问题');
    }


    /**
     * 订单批量通过
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function batchPass(Request $request)
    {
        $orderIds = $request->input('order_id');

        if (empty($orderIds)) {
            return $this->error('请选择要通过的订单');
        }
        $orders = SalesmanVisitOrder::whereIn('id', $orderIds)->get();

        if (Gate::denies('validate-salesman-order', $orders)) {
            return $this->error('存在不合法订单');
        }

        // 订货单才同步
        $result = $orders->sum('type') == 0 ? $this->_syncOrders($orders) : 'success';

        if ($result == 'success' && SalesmanVisitOrder::whereIn('id',
                $orderIds)->update(['status' => cons('salesman.order.status.passed')])
        ) {
            return $this->success('操作成功');
        }

        return $this->error('操作失败，请重试');

    }

    /**
     * 订单同步
     *
     * @param \App\Models\SalesmanVisitOrder $salesmanVisitOrder
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function sync(SalesmanVisitOrder $salesmanVisitOrder)
    {
        if (Gate::denies('validate-salesman-order', $salesmanVisitOrder)) {
            return $this->error('存在不合法订单');
        }

        $result = $this->_syncOrders([$salesmanVisitOrder]);

        return $result === 'success' ? $this->success('同步成功') : $this->error($result['error']);
    }

    /**
     * 订单批量同步
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function batchSync(Request $request)
    {
        $orderIds = $request->input('order_id');
        if (is_null($orderIds)) {
            return $this->error('请选择要同步的订单');
        }
        $orders = SalesmanVisitOrder::whereIn('id', $orderIds)->with('orderGoods', 'salesmanCustomer')->get();

        if (Gate::denies('validate-salesman-order', $orders)) {
            return $this->error('存在不合法订单');
        }
        $result = $this->_syncOrders($orders);

        return $result === 'success' ? $this->success('同步成功') : $this->error($result['error']);
    }

    /**
     * 获取订货单信息
     *
     * @param $id
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function orderDetail($id)
    {
        $order = SalesmanVisitOrder::with([
            'orderGoods.goods' => function ($query) {
                $query->select('id', 'name');
            }
        ])->find($id);

        $order->type == cons('salesman.order.type.order') && $order->load('mortgageGoods', 'order.deliveryMan');

        return $this->success(compact('order'));
    }


    /**
     * 更新订单所有内容 （删除后添加）
     *
     * @param \Illuminate\Http\Request $request
     * @param $orderId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function updateAll(Request $request, $orderId)
    {
        /*$attributes = [
            'goods' => [
                [
                    'id' => 324,
                    'pieces' => 11,
                    'price'  => 169,
                    'num'    => 2
                ]
            ],
            "mortgage" => [
                [
                    "id"  => 324,
                    "num"       => 1
                ],
            ],
            'display_fee' => 100,
            'order_remark'=>'测试用',
            'display_remark'=>'测试用'
        ];*/

        $order = $this->_validateOrder($orderId);
        if (!$order) {
            return $this->error('订单不存在');
        }
        $attributes = $request->all();
        $result = DB::transaction(function () use ($attributes, $order) {
            $orderConf = cons('salesman.order');
            $attributes['id'] = $order->id;
            $attributes['salesman_id'] = $order->salesman_id;
            $attributes['salesman_visit_id'] = $order->salesman_visit_id;
            $attributes['salesman_customer_id'] = $order->salesman_customer_id;
            $attributes['type'] = $orderConf['type']['order'];
            $format = $this->_formatAttribute($attributes);
            $attributes['amount'] = $format['amount'];
            $attributes['created_at'] = $order->created_at;

            $order->delete();
            $orderForm = SalesmanVisitOrder::create($attributes);

            if ($orderForm->exists) {
                $orderForm->orderGoods()->saveMany($format['orderGoodsArr']);

                $orderForm->mortgageGoods()->sync($format['mortgageGoodsArr']);
            }
            return 'success';
        });

        return $result == 'success' ? $this->success('更新订单成功') : $this->error('更新订单时出现问题');
    }

    /**
     * 删除订单
     *
     * @param $orderId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function destroy($orderId)
    {
        $order = $this->_validateOrder($orderId);

        return $order && $order->delete() ? $this->success('订单删除成功') : $this->error('订单不存在或不能删除');
    }

    /**
     * 删除订单商品
     *
     * @param $goodsId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function goodsDelete($goodsId)
    {

        $orderGoods = SalesmanVisitOrderGoods::with('salesmanVisitOrder')->find($goodsId);

        if (is_null($orderGoods)) {
            return $this->error('订单商品不存在');
        }

        $order = $this->_validateOrder($orderGoods->salesman_visit_order_id);
        if (!$order) {
            return $this->error('订单商品不存在');
        }

        $result = DB::transaction(function () use ($orderGoods, $order) {
            $orderGoodsPrice = $orderGoods->amount;
            $orderGoods->delete();
            $orderGoodsPrice > 0 && $order->decrement('amount', $orderGoodsPrice);
            return 'success';
        });

        return $result == 'success' ? $this->success('删除订单商品成功') : $this->error('删除订单商品时出现问题');
    }

    /**
     * 删除陈列商品
     *
     * @param \App\Http\Requests\Api\v1\DeleteMortgageGoodsRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function mortgageGoodsDelete(DeleteMortgageGoodsRequest $request)
    {
        $order = $this->_validateOrder($request->input('order_id'));
        if (!$order) {
            return $this->error('订单不存在');
        }
        $order->mortgageGoods()->detach($request->input('mortgage_goods_id'));
        return $this->success('删除陈列费商品成功');
    }

    /**
     * 订单验证
     *
     * @param $orderId
     * @return bool
     */
    private function _validateOrder($orderId)
    {
        $order = SalesmanVisitOrder::find($orderId);
        if (is_null($order) || $order->status == cons('salesman.order.status.passed')) {
            return false;
        }
        if (auth()->id()) {
            //网页登录的
            if (Gate::denies('validate-salesman', $order->salesman)) {
                return false;
            }

        } else {
            if ($order->salesman_id != salesman_auth()->id()) {
                return false;
            }
        }
        return $order;
    }

    /**
     * 同步订单
     *
     * @param $salesmanVisitOrders
     * @return \WeiHeng\Responses\Apiv1Response
     */
    private function _syncOrders($salesmanVisitOrders)
    {
        $result = DB::transaction(function () use ($salesmanVisitOrders) {
            $syncConf = cons('salesman.order.sync');
            $orderConf = cons('order');
            $shippingAddressService = new ShippingAddressService();
            foreach ($salesmanVisitOrders as $salesmanVisitOrder) {
                if (!$salesmanVisitOrder->can_sync) {
                    return ['error' => '存在不能同步的订单'];
                }
                $orderData = [
                    'user_id' => $salesmanVisitOrder->customer_user_id,
                    'shop_id' => auth()->user()->shop_id,
                    'price' => $salesmanVisitOrder->amount,
                    'pay_type' => $syncConf['pay_type'],
                    'pay_way' => $syncConf['pay_way'],
                    //'pay_status' => $orderConf['pay_status']['payment_success'],
                    'status' => $orderConf['status']['non_send'],
                    'display_fee' => $salesmanVisitOrder->display_fee,
                    // 'finished_at' => Carbon::now(),
                    'shipping_address_id' => $shippingAddressService->copySalesmanCustomerShippingAddressToSnapshot($salesmanVisitOrder->SalesmanCustomer),
                    'remark' => '订单备注:' . $salesmanVisitOrder->order_remark . ($salesmanVisitOrder->display_remark ? '; 陈列费备注:' . $salesmanVisitOrder->display_remark : '')
                ];

                if (!$orderData['shipping_address_id']) {
                    return ['error' => '客户收货地址不存在'];
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
                    }
                    foreach ($salesmanVisitOrder->mortgageGoods as $goods) {
                        // 添加抵费商品
                        $orderGoods[] = new OrderGoods([
                            'type' => cons('order.goods.type.mortgage_goods'),
                            'goods_id' => $goods->goods_id,
                            'price' => 0,
                            'num' => $goods->pivot->num,
                            'pieces' => $goods->pieces,
                            'total_price' => 0,
                        ]);
                    }

                    //保存订单商品
                    if (!$orderTemp->orderGoods()->saveMany($orderGoods)) {
                        return ['error' => '同步时出现错误，请重试'];
                    }

                } else {
                    return ['error' => '同步时出现错误，请重试'];
                }
                if (!$salesmanVisitOrder->fill(['order_id' => $orderTemp->id])->save()) {
                    return ['error' => '同步时出现错误，请重试'];
                }

            }

            return 'success';
        });

        return $result;

    }

    /**
     * 更新订单商品
     *
     * @param $salesmanVisitOrder
     * @param $request
     * @param null $orderGoods
     * @return \WeiHeng\Responses\Apiv1Response
     */
    private function _updateOrderGoods($salesmanVisitOrder, $request, $orderGoods = null)
    {
        if (is_null($salesmanVisitOrder) || Gate::denies('validate-salesman-order', $salesmanVisitOrder)) {
            return false;
        }

        $result = DB::transaction(function () use ($salesmanVisitOrder, $request, $orderGoods) {
            if ($orderGoods) {
                $goodsTypes = cons('salesman.order.goods.type');
                $attributes = [];
                //商品原总金额
                $goodsOldAmount = $orderGoods->amount;

                if ($orderGoods->type == $goodsTypes['order']) {
                    //订单
                    $attributes['price'] = $request->input('price');
                    $attributes['num'] = $request->input('num');
                    $attributes['pieces'] = $request->input('pieces');
                    $attributes['amount'] = bcmul($attributes['price'], intval($attributes['num']), 2);
                    if ($orderGoods->fill($attributes)->save()) {
                        $salesmanVisitOrder->fill(['amount' => $salesmanVisitOrder->amount - $goodsOldAmount + $attributes['amount']])->save();
                    }
                } elseif ($orderGoods->type == $goodsTypes['return']) {
                    //退货单
                    $attributes['num'] = $request->input('num');
                    $attributes['amount'] = $request->input('amount');
                    if ($orderGoods->fill($attributes)->save()) {
                        $salesmanVisitOrder->fill(['amount' => $salesmanVisitOrder->amount - $goodsOldAmount + $attributes['amount']])->save();
                    }
                }
            } else {
                //抵费商品
                $goodsId = $request->input('id');
                $num = $request->input('num');
                if (!$goodsId || !$salesmanVisitOrder->mortgageGoods()->find($goodsId)) {
                    return false;
                }

                $salesmanVisitOrder->mortgageGoods()->detach($goodsId);
                $salesmanVisitOrder->mortgageGoods()->attach([$goodsId => ['num' => $num]]);
            }

            return 'success';
        });
        return $result;
    }

    /**
     * 格式化订单属性
     *
     * @param $attributes
     * @return array
     */
    private function _formatAttribute($attributes)
    {
        $amount = 0;
        $orderGoodsArr = [];
        $mortgageGoodsArr = [];
        if (isset($attributes['goods'])) {
            foreach ($attributes['goods'] as $orderGoods) {
                $orderGoods['amount'] = bcmul($orderGoods['price'], $orderGoods['num'], 2);
                $orderGoods['salesman_visit_id'] = $attributes['salesman_visit_id'];
                $orderGoods['type'] = $attributes['type'];
                $orderGoods['goods_id'] = $orderGoods['id'];
                $orderGoodsArr[] = new SalesmanVisitOrderGoods($orderGoods);
                $amount = bcadd($amount, $orderGoods['amount']);
            }
        }
        if (isset($attributes['mortgage'])) {
            foreach ($attributes['mortgage'] as $mortgageGoods) {
                $mortgageGoodsArr[$mortgageGoods['id']] = [
                    'num' => $mortgageGoods['num']
                ];
            }
        }
        return compact('amount', 'orderGoodsArr', 'mortgageGoodsArr');
    }

}
