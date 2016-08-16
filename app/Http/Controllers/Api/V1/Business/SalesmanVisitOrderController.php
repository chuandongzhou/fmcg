<?php

namespace App\Http\Controllers\Api\V1\Business;


use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\v1\UpdateSalesmanVisitOrderGoodsRequest;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\SalesmanVisitOrder;
use App\Models\SalesmanVisitOrderGoods;
use App\Services\BusinessService;
use App\Services\ShippingAddressService;
use App\Services\ShopService;
use Carbon\Carbon;
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

        $orders = (new BusinessService())->getOrders([$salesmenId], $data, true);
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

        $orders = (new BusinessService())->getOrders([$salesmenId], $data, true);
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
        $attributes = $request->all();
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
        return SalesmanVisitOrder::whereIn('id',
            $orderIds)->update(['status' => cons('salesman.order.status.passed')]) ? $this->success('操作成功') : $this->error('操作失败，请重试');
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
        return $this->_syncOrders([$salesmanVisitOrder]);
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
        return $this->_syncOrders($orders);
    }

    /**
     * 同步订单
     *
     * @param $orders
     * @return \WeiHeng\Responses\Apiv1Response
     */
    private function _syncOrders($orders)
    {
        $result = DB::transaction(function () use ($orders) {
            $syncConf = cons('salesman.order.sync');
            $orderConf = cons('order');
            $shippingAddressService = new ShippingAddressService();
            foreach ($orders as $order) {
                if (!$order->can_sync) {
                    return ['error' => '存在不能同步的订单'];
                }
                $orderData = [
                    'user_id' => $order->customer_user_id,
                    'shop_id' => auth()->user()->shop_id,
                    'price' => $order->amount,
                    'pay_type' => $syncConf['pay_type'],
                    'pay_way' => $syncConf['pay_way'],
                    //'pay_status' => $orderConf['pay_status']['payment_success'],
                    'status' => $orderConf['status']['non_send'],
                    // 'finished_at' => Carbon::now(),
                    'shipping_address_id' => $shippingAddressService->copySalesmanCustomerShippingAddressToSnapshot($order->SalesmanCustomer),
                    'remark' => '业务同步订单'
                ];

                if (!$orderData['shipping_address_id']) {
                    return ['error' => '客户收货地址不存在'];
                }

                $orderTemp = Order::create($orderData);
                if ($orderTemp->exists) {//添加订单成功,修改orderGoods中间表信息
                    $orderGoods = [];
                    foreach ($order->orderGoods as $goods) {
                        // 添加订单商品
                        $orderGoods[] = new OrderGoods([
                            'goods_id' => $goods->goods_id,
                            'price' => $goods->price,
                            'num' => $goods->num,
                            'pieces' => $goods->pieces,
                            'total_price' => $goods->amount,
                        ]);
                    }
                    foreach ($order->mortgageGoods as $goods) {
                        // 添加抵费商品
                        $orderGoods[] = new OrderGoods([
                            'goods_id' => $goods->goods_id,
                            'price' => 0,
                            'num' => $goods->pivot->num,
                            'pieces' => $goods->pieces,
                            'total_price' => 0,
                        ]);
                    }
                    //添加抵费商品

                    if (!$orderTemp->orderGoods()->saveMany($orderGoods)) {
                        return ['error' => '同步时出现错误，请重试'];
                    }

                } else {
                    return ['error' => '同步时出现错误，请重试'];
                }
                if (!$order->fill(['is_synced' => cons('salesman.order.is_synced.synced')])->save()) {
                    return ['error' => '同步时出现错误，请重试'];
                }

            }

            return 'success';
        });

        return $result === 'success' ? $this->success('同步成功') : $this->error($result['error']);

    }

    private function _updateOrderGoods($salesmanVisitOrder, $request, $orderGoods = null)
    {
        if (is_null($salesmanVisitOrder) || Gate::denies('validate-salesman-order', $salesmanVisitOrder)) {
            return $this->error('订单不存在');
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
}
