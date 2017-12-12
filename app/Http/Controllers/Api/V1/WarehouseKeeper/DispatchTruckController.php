<?php

namespace App\Http\Controllers\Api\V1\WarehouseKeeper;

use App\Models\DeliveryTruck;
use App\Models\DispatchTruck;
use App\Models\Goods;
use App\Models\Inventory;
use App\Models\Order;
use App\Services\DispatchTruckService;
use App\Services\GoodsService;
use App\Services\InventoryService;
use App\Services\RedisService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\V1\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Cookie;

class DispatchTruckController extends Controller
{
    /**
     * 获取车辆列表
     *
     * @return mixed
     */
    public function trucks(Request $request)
    {
        $available = $request->input('available', 0);
        return wk_auth()->user()->shop->deliveryTrucks()->enable()->where(function ($query) use ($available) {
            if ($available) {
                $query->where('status', $available == 1 ? '<=' : '<', cons('truck.status.wait'));
            }
        })->get()->each(function ($truck) {
            $truck->setAppends(['status_name']);
        });
    }

    /**
     * 获取配送员列表
     *
     * @return mixed
     */
    public function deliveryMans()
    {
        return wk_auth()->user()->shop->deliveryMans->load('dispatchTruck')->each(function ($deliveryMans, $key) {
            $deliveryMans->delivery_status = 1;
            $deliveryMans->addHidden(['dispatchTruck']);
            if ($deliveryMans->dispatchTruck) {
                foreach ($deliveryMans->dispatchTruck as $dispatchTruck) {
                    if ($dispatchTruck->status < cons('dispatch_truck.status.backed')) {
                        $deliveryMans->delivery_status = 0;
                        break;
                    }
                }
            };
        });
    }

    /**
     * 添加订单到车辆
     *
     * @param \Illuminate\Http\Request $request
     * @param $truck
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function addOrder(Request $request)
    {
        $truck = DeliveryTruck::find($request->input('truck_id'));
        if (!$truck) {
            return $this->error('没找到车辆!');
        }
        if (Gate::forUser(wk_auth()->user())->denies('validate-warehouse-keeper-truck', $truck)) {
            return $this->error('没有权限!');
        }
        $truckStatus = cons('truck.status');
        $dispatchTruckStatus = cons('dispatch_truck.status');
        if ($truck->status > $truckStatus['wait']) {
            return $this->error('车辆繁忙!');
        }
        try {
            DB::beginTransaction();
            $orderId = is_array($request->input('order_id')) ? $request->input('order_id') : [$request->input('order_id')];
            $dtv_id = $request->input('dispatch_truck_id');  //已选车发车单ID
            $dispatchTruck = $truck->dispatchTruck()->where('status', '<=',
                $dispatchTruckStatus['wait'])->first(); //当前配送发车单
            //没有即生成一张发车单
            if (!$dispatchTruck) {
                $dispatchTruck = $truck->dispatchTruck()->create([
                    'status' => $dispatchTruckStatus['wait']
                ]);
            }
            if ($dispatchTruck->type == cons('dispatch_truck.type.sales')) {
                return $this->error('车销车辆,无法添加');
            }
            //处理换车
            if ($dtv_id && !empty($dtv_id) && $dtv_id != $dispatchTruck->id) {
                $dtv = DispatchTruck::find($dtv_id);
                if ($dtv->status > $dispatchTruckStatus['wait']) {
                    return $this->error('订单已发车');
                }
                if ($dtv->orders->count() < 2) {
                    $dtv->truck()->update(['status' => cons('truck.status.spare_time')]);
                    $dtv->orders()->update(['dispatch_truck_id' => 0]);
                    $dtv->delete();
                }
            }
            //批量更新订单
            $this->updateBatch($orderId, $dispatchTruck);
            $truck->update(['status' => cons('truck.status.wait')]);
            DB::commit();
            return $this->success('保存成功');
        } catch (\Exception $e) {
            DB::rollback();
            info($e->getMessage() . '----' . $e->getLine() . '行');
            return $this->error('保存失败');
        }
    }


    /**
     *
     * 添加配送员到车辆
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function addDeliveryMans(Request $request)
    {
        $truck = DeliveryTruck::find($request->input('truck_id'));
        if (!count($deliveryManIds = $request->input('delivery_mans')) || !$truck) {
            return $this->error('参数错误!');
        }
        if (Gate::forUser(wk_auth()->user())->denies('validate-warehouse-keeper-truck', $truck)) {
            return $this->error('没有权限!');
        }
        $truckStatus = cons('truck.status');
        $dispatchTruck = $truck->dispatchTruck()->where('status', '<=', $truckStatus['wait'])->first();
        if (!$dispatchTruck) {
            return $this->error('没有找到数据!');
        }
        try {
            if ($dispatchTruck->status > cons('dispatch_truck.status.wait')) {
                return $this->error('已发车,不能操作');
            }
            //添加配送员
            $dispatchTruck->deliveryMans()->sync($deliveryManIds);
            return $this->success('提交成功');
        } catch (\Exception $e) {
            return $this->error('保存失败');
        }
    }

    /**
     * 更新发车单订单排序
     *
     * @param \Illuminate\Http\Request $request
     * @param $dtv_id
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function dispatchOrderSort(Request $request, $dtv_id)
    {
        $dtv = DispatchTruck::with('orders')->find($dtv_id);
        if (!$dtv) {
            return $this->error('发车单不存在');
        }
        if ($dtv->status > cons('dispatch_truck.status.wait')) {
            return $this->error('订单已发车,不能操作');
        }
        //更新订单排序
        $this->updateBatch($request->input('order_ids'), $dtv, false);
        return $this->success('更新成功');
    }

    /**
     * 批量更新订单
     *
     * @param $orderIds
     * @param $dtv
     * @return mixed
     */
    public function updateBatch($orderIds, $dtv, $add = true)
    {
        $maxSort = $dtv->orders()->max('dispatch_truck_sort');

        $sql = "UPDATE fmcg_order SET dispatch_truck_id =" . $dtv->id . ', dispatch_truck_sort = case id ';

        foreach ($orderIds as $key => $orderId) {
            if ($add) {
                $sort = $maxSort + $key + 1;
            } else {
                $sort = $key + 1;
            }
            $sql .= sprintf("WHEN %d THEN %d ", $orderId, $sort);
        };
        $sql .= 'END WHERE id IN (' . implode(',', $orderIds) . ')';
        return DB::update(DB::raw($sql));
    }

    /**
     * 发车
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function createDispatchTruckVoucher(Request $request)
    {
        $dispatchTruck = DispatchTruck::with('orders.orderGoods')->find($request->input('dtv_id'));
        if (!$dispatchTruck) {
            return $this->error('参数错误!');
        }
        if ($dispatchTruck->status > cons('dispatch_truck.status.wait')) {
            return $this->error('已发车,不能操作');
        }
        try {
            DB::beginTransaction();
            if ($dispatchTruck->type == cons('dispatch_truck.type.sales')) {
                if (!$dispatchTruck->truckSalesGoods->count()) {
                    return $this->error('没有商品');
                }
                foreach ($dispatchTruck->truckSalesGoods as $salesGoods) {
                    if ($salesGoods->total_inventory < $salesGoods->pivot->surplus) {
                        return $this->error($salesGoods->name . '库存不足');
                        break;
                    }
                }

            }
            if ($dispatchTruck->type == cons('dispatch_truck.type.dispatch')) {
                if (!$dispatchTruck->orders->count()) {
                    return $this->error('没有订单');
                }
                $inventoryService = new  InventoryService();
                $inventoryNumber = $inventoryService->getInventoryNumber();
                if ($dispatchTruck->type == cons('dispatch_truck.type.dispatch')) {
                    foreach ($dispatchTruck->orders as $order) {
                        if (!$order->can_send || $order->shop_id != wk_auth()->user()->shop->id) {
                            if (count($dispatchTruck->orders) == 1) {
                                return $this->error('操作失败');
                            }
                            $failIds[] = $order->id;
                            continue;
                        }
                        $data = [
                            'inventory_number' => $inventoryNumber,
                            'inventory_type' => cons('inventory.inventory_type.system'),  //系统
                            'action_type' => cons('inventory.action_type.out'),           //出库
                            'user_id' => wk_auth()->id(),
                            'shop_id' => wk_auth()->user()->shop_id,
                            'operate' => 'warehouseKeeper',
                        ];
                        $result = $inventoryService->autoOut($order, $data);
                        if ($result == false) {
                            return $this->error($inventoryService->getError());
                        };
                        if ($order->fill(['status' => cons('order.status.send'), 'send_at' => Carbon::now()])->save()) {
                            $redisKey = 'push:user:' . $order->user_id;
                            $redisVal = '您的订单' . $order->id . ',' . cons()->lang('push_msg.send');
                            (new RedisService())->setRedis($redisKey, $redisVal, cons('push_time.msg_life'));
                        }
                    }
                }
            }
            //更新发车单
            $dispatchTruck->update([
                'status' => cons('dispatch_truck.status.delivering'),
                'dispatch_time' => Carbon::now(),
                'remark' => $request->input('remark'),
            ]);
            //添加记录
            $dispatchTruck->record()->create([
                'name' => '发车',
                'operater' => wk_auth()->user()->name,
                'time' => Carbon::now(),
            ]);
            //更新车辆状态
            $dispatchTruck->truck()->update(['status' => cons('truck.status.delivering')]);
            DB::commit();
            return $this->success('提交成功');
        } catch (\Exception $e) {
            DB::rollback();
            info($e->getMessage() . '-----' . $e->getLine() . '行');
            return $this->error($e->getMessage() . '-----' . $e->getLine() . '行');
        }
    }

    /**
     * 发车单详情
     *
     * @param $dtv_id
     * @return array|\WeiHeng\Responses\Apiv1Response
     */
    public function getDispatchTruckVoucherDetail(Request $request)
    {
        $data = $request->all();
        $with = [
            'orders' => function ($order) {
                $order->orderBy('dispatch_truck_sort')->select([
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
                ]);
            },
            'salesman',
            'truck',
            'deliveryMans',
            'orders.orderGoods.goods.goodsPieces',
            'orders.shippingAddress.address',
            'returnOrders.goods.goodsPieces',
            'truckSalesGoods.goodsPieces',
            'truckSalesGoods' => function ($goods) {
                $goods->select(['id', 'name']);
            }
        ];
        $dtv = null;
        if (array_get($data, 'dtv_id')) {
            $dtv = DispatchTruck::with($with)->find($data['dtv_id']);
        } elseif (array_get($data, 'truck_id')) {
            $dtv = DispatchTruck::with($with)->where('status', '<=',
                cons('dispatch_truck.status.delivering'))->where('delivery_truck_id', $data['truck_id'])->first();
        }
        if (!$dtv) {
            return $this->error('没有找到数据');
        }

        $dtv->addHidden(['returnOrders']);
        $dtv->setAppends(['status_name']);
        $dtv->can_back = 1;
        foreach ($dtv->orders as $order) {
            $order->addHidden(['orderGoods', 'salesmanVisitOrder']);
            $order->shippingAddress->setAppends(['address_name']);
            $order->shippingAddress->addHidden(['address']);
            if ($order->status < cons('order.status.invalid') && !$order->delivery_finished_at) {
                $dtv->can_back = 0;
            }
        };

        $dtv->truckSalesGoods->each(function ($goods) {
            $goods->surplus_string = InventoryService::calculateQuantity($goods, $goods->pivot->surplus);
        });
        $dtv->order_goods_statis = DispatchTruckService::goodsStatistical($dtv->orders);
        $dtv->return_order_goods_statis = DispatchTruckService::returnOrderGoodsStatistical($dtv->returnOrders ?? []);
        return $this->success($dtv->toArray());
    }

    /**
     * 获取发车历史
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function history(Request $request)
    {
        $trucks = wk_auth()->user()->shop->deliveryTrucks;
        $dispatchTruck = DispatchTruck::with([
            'truck',
            'orders' => function ($order) {
                $order->select('dispatch_truck_id', 'status', 'pay_status', 'pay_type', 'is_cancel');
            },
            'returnOrders' => function ($returnOrders) {
                $returnOrders->select('dispatch_truck_id');
            }
        ])->condition($request->all())->whereIn('delivery_truck_id', $trucks->pluck('id'))->type($request->input('type',
            0))->get()->each(function ($item
        ) {
            $item->setAppends(['order_amount', 'status_name', 'is_return_order']);
            $item->addHidden(['orders', 'returnOrders']);
            if ($item->type == cons('dispatch_truck.type.sales')) {
                $item->sold_out = 1;
                foreach ($item->truckSalesGoods as $goods) {
                    if ($goods->surplus > 0) {
                        $item->sold_out = 0;
                        break;
                    }
                };
            } else {
                $item->can_back = 1; //可否回车 0:不可,:1:可以回车
                foreach ($item->orders as $order) {
                    if ($order->status < cons('order.status.invalid') && !$order->delivery_finished_at) {
                        $item->can_back = 0;
                        break;
                    }
                };
            }
        });
        return $this->success($dispatchTruck->toArray());
    }

    /**
     * 发车单内单个订货商品统计
     *
     * @param \Illuminate\Http\Request $request
     * @param $dtv_id
     * @return array
     */
    public function dispatchGoodsStatistical(Request $request, $dtv_id)
    {
        $goodsId = $request->input('goods_id');

        $dispatchTruck = DispatchTruck::find($dtv_id);
        $tmpArray = [];
        $resultArray = [];

        $allGoods = $dispatchTruck->orders->pluck('orderGoods')->collapse();

        foreach ($allGoods as $orderGoods) {
            if ($orderGoods->order->status == cons('order.status.invalid')) {
                continue;
            }
            if ($orderGoods->goods_id == $goodsId) {
                if (!array_key_exists($orderGoods->order_id, $tmpArray)) {
                    $tmpArray[$orderGoods->order_id] = [
                        'goods' => $orderGoods->goods,
                        'quantity' => $orderGoods->num * GoodsService::getPiecesSystem($orderGoods->goods,
                                $orderGoods->pieces)
                    ];
                } else {
                    $tmpArray[$orderGoods->order_id]['quantity'] += $orderGoods->num * GoodsService::getPiecesSystem($orderGoods->goods,
                            $orderGoods->pieces);
                }
            }
        }
        foreach ($tmpArray as $key => $item) {
            $resultArray[] = [
                'order_id' => $key,
                'quantity' => InventoryService::calculateQuantity($item['goods'], $item['quantity']),
            ];
        }
        return $resultArray;
    }


    /**
     * 发车单内单个退货商品统计
     *
     * @param \Illuminate\Http\Request $request
     * @param $dtv_id
     * @return array
     */
    public function dispatchReturnGoodsStatistical(Request $request, $dtv_id)
    {
        $goodsId = $request->input('goods_id');

        $dispatchTruck = DispatchTruck::with(['returnOrders.goods'])->find($dtv_id);
        $tmpArray = [];
        $resultArray = [];
        foreach ($dispatchTruck->returnOrders as $returnOrder) {

            if ($returnOrder->goods_id == $goodsId) {
                if (!array_key_exists($returnOrder->order_id, $tmpArray)) {
                    $tmpArray[$returnOrder->order_id] = [
                        'goods' => $returnOrder->goods,
                        'quantity' => $returnOrder->num * GoodsService::getPiecesSystem($returnOrder->goods,
                                $returnOrder->pieces)
                    ];
                } else {
                    $tmpArray[$returnOrder->order_id]['quantity'] += $returnOrder->num * GoodsService::getPiecesSystem($returnOrder->goods,
                            $returnOrder->pieces);
                }
            }
        }
        foreach ($tmpArray as $key => $item) {
            $resultArray[] = [
                'order_id' => $key,
                'quantity' => InventoryService::calculateQuantity($item['goods'], $item['quantity']),
            ];
        }
        return $resultArray;
    }

    /**
     * 切换车辆
     *
     * @param \Illuminate\Http\Request $request
     * @param $dtv_id
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function changeTruck(Request $request, $dtv_id)
    {
        $dtv = DispatchTruck::find($dtv_id);
        $newTruckId = $request->input('new_truck_id');
        if (!$dtv) {
            return $this->error('参数错误');
        }
        if ($dtv->delivery_truck_id == $newTruckId) {
            return $this->success('操作成功');
        }
        if ($dtv->status > cons('dispatch_truck.status.wait')) {
            return $this->error('已发车,不能操作');
        }

        $newTruck = DeliveryTruck::find($newTruckId);
        if (!$newTruck || $newTruck->status > cons('truck.status.wait')) {
            return $this->error('车辆信息错误!');
        }
        if ($newTruck->now_delivery_type && $newTruck->now_delivery_type != $dtv->type) {
            return $this->error('类型错误!无法切换');
        }

        $result = DB::transaction(function () use ($dtv, $newTruckId) {
            $dtv->truck()->update(['status' => cons('truck.status.spare_time')]);
            $dtv->fill(['delivery_truck_id' => $newTruckId])->save();
            $dtv->truck()->update(['status' => cons('truck.status.wait')]);
            return true;
        });
        return $result ? $this->success('操作成功') : $this->error('操作失败');
    }

    /**
     * 删除发车单内订单
     *
     * @param $order_id
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function deleteDispatchTruckOrder($order_id)
    {
        $order = Order::find($order_id);
        if (!$order || $order->dispatch_status > cons('dispatch_truck.status.wait')) {
            return $this->error('没找到此订单,或已发车!');
        }
        $dtv = DispatchTruck::find($order->dispatch_truck_id);
        if ($dtv->orders->count() == 1) {
            $dtv->truck()->update(['status' => cons('truck.status.spare_time')]);
            $dtv->deliveryMans()->detach();
            $dtv->delete();
        };
        return $order->update([
            'dispatch_truck_id' => '',
            'dispatch_truck_sort' => ''
        ]) ? $this->success('删除成功') : $this->error('删除失败');
    }

    /**
     * 回车操作
     *
     * @param $dtv_id
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function confirmTruckBack($dtv_id)
    {

        $dtv = DispatchTruck::find($dtv_id);
        if (!$dtv || $dtv->status > cons('dispatch_truck.status.delivering')) {
            return $this->error('发车单错误或已回车!');
        }
        try {
            $result = DB::transaction(function () use ($dtv) {
                $inventoryService = new InventoryService();
                if ($dtv->returnOrders->count() || $dtv->type == cons('dispatch_truck.type.sales')) {
                    foreach ($dtv->orders as $order) {
                        if ($order->status == cons('order.status.invalid')) {
                            continue;
                        }
                        if ($dtv->returnOrders->count()) {
                            $inventoryService->clearOut($order->id);
                        }
                        $data = [
                            'inventory_number' => $inventoryService->getInventoryNumber(),
                            'inventory_type' => cons('inventory.inventory_type.system'),  //系统
                            'action_type' => cons('inventory.action_type.out'),           //出库
                            'user_id' => wk_auth()->id(),
                            'shop_id' => wk_auth()->user()->shop_id,
                            'operate' => 'warehouseKeeper',
                        ];
                        $result = $inventoryService->autoOut($order, $data);
                        if ($result == false) {
                            return $this->error($inventoryService->getError());
                        };
                    }
                }
                $dtv->truck()->update(['status' => cons('truck.status.spare_time')]);
                $dtv->fill(['status' => cons('dispatch_truck.status.backed'), 'back_time' => Carbon::now()])->save();
                $dtv->record()->create([
                    'name' => '回车',
                    'operater' => wk_auth()->user()->name,
                    'time' => Carbon::now(),
                ]);
                return true;
            });
            return $result ? $this->success('操作成功') : $this->error('操作失败');
        } catch (\Exception $e) {
            info($e->getMessage() . '----' . $e->getFile() . $e->getLine() . '行');
            return $this->error('操作失败');
        }
    }


    /**
     * 创建车销单
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function createTruckSalesVoucher(Request $request)
    {
        $dispatchTruckConfig = cons('dispatch_truck');
        $truck = DeliveryTruck::find($request->input('truck_id'));
        if (Gate::forUser(wk_auth()->user())->denies('validate-warehouse-keeper-truck', $truck)) {
            return $this->error('没有权限!');
        }
        $dispatchTruck = DispatchTruck::create([
            'status' => $dispatchTruckConfig['status']['wait'],
            'type' => $dispatchTruckConfig['type']['sales'],
            'delivery_truck_id' => $truck->id
        ]);

        if ($dispatchTruck && $truck->fill(['status' => cons('truck.status.wait')])->save()) {
            return $this->success(['id' => $dispatchTruck->id]);
        }
        return $this->success('创建失败');
    }

    /**
     *
     * 获取商品列表
     *
     * @return mixed
     */
    public function goodsList(Request $request)
    {
        $name = $request->input('name');
        $list = wk_auth()->user()->shop->goods()->active()->withGoodsPieces()->select([
            'id',
            'name',
            'shop_id',
            'pieces_retailer',
            'price_retailer',
            'pieces_wholesaler',
            'price_wholesaler'
        ])->ofGoodsName($name)->get()->each(function ($item) {
            $item->setAppends(['image_url']);
        });
        return $this->success($list);
    }

    /**
     * 获取业务员列表
     *
     * @return mixed
     */
    public function salesmanList()
    {
        return wk_auth()->user()->shop->salesmen()->with('dispatchTrucks')->select([
            'id',
            'name',
            'shop_id',
            'contact_information'
        ])->get()->each(function ($salesman) {
            $salesman->delivery_status = 1;
            $salesman->addHidden(['dispatchTrucks']);
            if ($salesman->dispatchTrucks) {
                foreach ($salesman->dispatchTrucks as $dispatchTruck) {
                    if ($dispatchTruck->status < cons('dispatch_truck.status.backed')) {
                        $salesman->delivery_status = 0;
                        break;
                    }
                }
            };
        });
    }

    /**
     * 添加商品到车销单
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function addGoods(Request $request, $truckSalesId)
    {
        $data = $request->only('goods_id', 'quantity', 'pieces');
        $truckSales = DispatchTruck::with([
            'deliveryMans',
            'salesman',
            'truck',
            'truckSalesGoods'
        ])->find($truckSalesId);
        if (!$truckSales) {
            return $this->error('车销单不存在');
        }
        if ($truckSales->type != cons('dispatch_truck.type.sales')) {
            return $this->error('配送车辆,无法添加');
        }
        try {
            DB::beginTransaction();
            $goods = Goods::find(array_get($data, 'goods_id'));
            $withPivot = [
                'quantity' => $data['quantity'],
                'surplus' => $data['quantity'] * GoodsService::getPiecesSystem($goods, $data['pieces']),
                'pieces' => $data['pieces'],
            ];
            $truckSales->truckSalesGoods()->detach($goods->id);
            $truckSales->truckSalesGoods()->attach([$goods->id => $withPivot]);
            DB::commit();
            return $this->success('添加成功');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error('添加失败');
        }
    }

    /**
     * 删除车销单商品
     *
     * @param \Illuminate\Http\Request $request
     * @param $dtv_id
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function deleteGoods(Request $request, $dtv_id)
    {
        $truckSales = DispatchTruck::find($dtv_id);
        if (!$truckSales) {
            return $this->error('车销单不存在');
        }
        if ($truckSales->type != cons('dispatch_truck.type.sales')) {
            return $this->error('无法删除');
        }
        if ($truckSales->truckSalesGoods()->detach($request->input('goods_id'))) {
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }

    /**
     * 添加业务员到车销单
     *
     * @param \Illuminate\Http\Request $request
     * @param $truckSalesId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function addSalesman(Request $request, $truckSalesId)
    {
        $truckSales = DispatchTruck::find($truckSalesId);
        if (!$truckSales) {
            return $this->error('车销单不存在');
        }
        if ($truckSales->type != cons('dispatch_truck.type.sales')) {
            return $this->error('配送车辆,无法添加');
        }
        $salesman = wk_auth()->user()->shop->salesmen()->find($request->only('salesman_id'))->first();
        if (!$salesman) {
            return $this->error('业务员错误');
        }
        if ($truckSales->update(['salesman_id' => $salesman->id])) {
            return $this->success('添加成功');
        }
        return $this->error('添加失败');
    }

    /**
     * 取消创建
     *
     * @param $dispatchTruckId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function cancelCreate($dispatchTruckId)
    {
        $dtv = DispatchTruck::find($dispatchTruckId);
        if (!$dtv || $dtv->status > cons('dispatch_truck.status.wait')) {
            return $this->error('不能取消');
        }
        try {
            DB::beginTransaction();
            $dtv->delete();
            DB::commit();
            return $this->success('取消成功');
        } catch (\Exception $e) {
            return $this->error('取消失败');
        }
    }
}
