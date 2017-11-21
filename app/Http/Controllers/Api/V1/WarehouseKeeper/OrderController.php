<?php

namespace App\Http\Controllers\Api\V1\WarehouseKeeper;

use App\Models\Goods;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\GoodsService;
use App\Services\InventoryService;
use App\Services\OrderService;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\V1\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    /**
     * 获取未发货,未出车订单
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function nonSendOrder(Request $request)
    {
        //订单号 店铺名称
        $condition = $request->input('condition', null);
        $choice = $request->input('choice', 0);
        $orders = Order::ofSell(wk_auth()->user()->shop_id)->nonSend()->with([
            'user.shop' => function ($shop) {
                $shop->select(['user_id', 'name']);
            },
            'shippingAddress' => function ($shipping) {
                $shipping->select(['id', 'consigner', 'phone']);
            },
            'dispatchTruck',
            'orderGoods.goods.goodsPieces',
            'orderGoods.goods.inventory',
            'shippingAddress.address',
            'salesmanVisitOrder.salesmanCustomer'
        ])->select([
            'id',
            'user_id',
            'status',
            'pay_status',
            'pay_type',
            'is_cancel',
            'shipping_address_id',
            'dispatch_truck_id'
        ])->where(function ($query) use ($condition) {
            if (!is_null($condition)) {
                if (is_numeric($condition)) {
                    $query->where('id', $condition);
                } else {
                    $query->ofUserShopName($condition);
                }
            }
        })->get();
        $filter_orders = collect();

        $orders = $orders->filter(function ($order) {
            if ($order->dispatch_truck_id && $order->dispatchTruck->status > cons('dispatch_truck.status.wait')) {
                return false;
            }
            return true;
        });
        $orders->each(function ($order) use ($filter_orders, $condition, $choice) {
            $order->goods_inventory = $this->validateGoodsInventory($order->orderGoods, false) ? 1 : 0;
            $order->setAppends(['user_shop_name', 'status_name']);
            $order->addHidden(['user', 'salesmanVisitOrder', 'orderGoods']);
            if ($condition || (bool)$choice == (bool)$order->dispatchTruck) {
                $filter_orders->push($order);
            }
            $order->dispatch_truck_id = $order->dispatch_truck_id ? $order->dispatch_truck_id : 0;
            $order->shippingAddress->setAppends(['address_name']);
            $order->shippingAddress->addHidden(['address']);
        });
        return $this->success([
            'filter_orders' => $filter_orders->toArray(),
            'other_count' => $orders->count() - $filter_orders->count(),
        ]);
    }

    /**
     *
     * 获取订单详情
     *
     * @param $order_id
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function detail($order_id)
    {
        $order = Order::with([
            'user.shop' => function ($shop) {
                $shop->select(['user_id', 'name']);
            },
            'shippingAddress' => function ($shipping) {
                $shipping->select(['id', 'consigner', 'phone']);
            },
            'dispatchTruck' => function ($dispatchTruck) {
                $dispatchTruck->select(['id', 'delivery_truck_id']);
            },
            'orderGoods.goods.goodsPieces',
            'orderGoods.goods.inventory',
            'shippingAddress.address',
            'salesmanVisitOrder.salesmanCustomer'
        ])->select([
            'id',
            'user_id',
            'status',
            'price',
            'coupon_id',
            'pay_status',
            'type',
            'pay_type',
            'shop_id',
            'is_cancel',
            'display_fee',
            'created_at',
            'remark',
            'shipping_address_id',
            'paid_at',
            'delivery_finished_at',
            'dispatch_truck_id'
        ])->find($order_id);
        if (!$order) {
            return $this->error('没找到订单!');
        }
        if (Gate::forUser(wk_auth()->user())->denies('validate-warehouse-keeper-order', $order)) {
            return $this->error('没有权限!');
        }
        $order->addHidden(['user', 'salesmanVisitOrder', 'orderGoods']);
        $order->setAppends(['user_shop_name', 'after_rebates_price', 'status_name', 'truck', 'delivery_mans']);
        $this->validateGoodsInventory($order->orderGoods);
        $order->orderGoods->each(function ($orderGoods) {
            $orderGoods->addHidden(['goods']);
        });
        $order->shippingAddress->setAppends(['address_name']);
        $order->shippingAddress->addHidden(['address']);
        $order->dispatchTruck && $order->dispatchTruck->addHidden(['truck', 'deliveryMans']);
        $goods = (new OrderService)->explodeOrderGoods($order->orderGoods);
        $order->goods = $goods['orderGoods'];
        $order->mortgageGoods = $goods['mortgageGoods'];
        $order->giftGoods = $goods['giftGoods'];
        return $this->success($order->toArray());
    }

    /**
     * 修改订单
     *
     * @param \Illuminate\Http\Request $request
     * @param $orderId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function modifyOrder(Request $request, $orderId)
    {
        $wkId = wk_auth()->id();
        //判断该订单是否存在
        $order = Order::useful()->noInvalid()->find($orderId);
        if($order->dispatch_status > cons('dispatch_truck.status.wait')){
            return $this->error('订单已发车,不能操作');
        }
        if (!$order || !$order->can_change_price) {
            return $this->error('订单不存在或不能修改');
        }
        $attributes = $request->all();
        $orderService = new OrderService;
        $flag = $orderService->changeOrder($order, $attributes, $wkId,'warehouseKeeper');
        return $flag ? $this->success('修改成功') : $this->error($orderService->getError());
    }

    /**
     *
     * 删除订单商品
     *
     * @param $orderGoodsId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function orderGoodsDelete($orderGoodsId)
    {
        
        $orderGoods = OrderGoods::with('order')->find($orderGoodsId);
        
        if (is_null($orderGoods) || $orderGoods->order->shop_id != wk_auth()->user()->shop_id) {
            return $this->error('订单商品不存在');
        }
        $order = $orderGoods->order;
        if($order->dispatch_status > cons('dispatch_truck.status.wait')){
            return $this->error('订单已发车,不能删除');
        }
        $result = DB::transaction(function () use ($orderGoods, $order) {
            $orderGoodsPrice = $orderGoods->total_price;
            $orderGoods->delete();
            if ($order->orderGoods->count() == 0) {
                //订单商品删完了标记为作废
                return $this->error('最后一个商品不能删除!请作废订单');
                //$order->fill(['status' => cons('order.status.invalid'), 'price' => 0])->save();
            } elseif ($orderGoodsPrice > 0) {
                $order->decrement('price', $orderGoodsPrice);
            }

            $content = '订单商品id:' . $orderGoods->goods_id . '，已被删除';
            (new OrderService())->addOrderChangeRecord($order, $content, wk_auth()->id(),'warehouseKeeper');
            //如果有业务订单修改业务订单
            if (!is_null($salesmanVisitOrder = $order->salesmanVisitOrder)) {
                $salesmanVisitOrder->fill(['amount' => $order->price])->save();

                if ($orderGoods->price == 0) {
                    //商品原价为0时更新抵费商品
                    $salesmanVisitOrderGoods = $salesmanVisitOrder->mortgageGoods()->where('goods_id',
                        $orderGoods->goods_id)->first();

                    $salesmanVisitOrderGoods && $salesmanVisitOrder->mortgageGoods()->detach($salesmanVisitOrderGoods->id);
                } else {
                    $salesmanVisitOrder->orderGoods()->where('goods_id', $orderGoods->goods_id)->delete();
                }
            }
            return 'success';
        });
        return $result == 'success' ? $this->success('删除订单商品成功') : $this->error('删除订单商品时出现问题');
    }

    /**
     * 验证商品库存
     *
     * @param $orderGoods
     * @return bool
     */
    private function validateGoodsInventory($orderGoods, $detail = true)
    {
        $tmpQuantity = [];
        foreach ($orderGoods as $key => $goods) {
            if (!array_key_exists($goods->goods_id, $tmpQuantity)) {
                $tmpQuantity[$goods->goods_id] = $goods->goods->total_inventory - ($goods->num * GoodsService::getPiecesSystem($goods->goods,
                            $goods->pieces));
            } else {
                $tmpQuantity[$goods->goods_id] -= $goods->num * GoodsService::getPiecesSystem($goods->goods,
                        $goods->pieces);
            }
        }
        if ($detail) {
            foreach ($orderGoods as $key => $goods) {
                if ($tmpQuantity[$goods->goods_id] < 0) {
                    $goods->goods_inventory = 0;
                } else {
                    $goods->goods_inventory = 1;
                };
            }
        } else {
            foreach ($tmpQuantity as $key => $quantity) {
                if ($quantity < 0) {
                    return false;
                    break;
                }
            }
            return true;
        }
    }
}
