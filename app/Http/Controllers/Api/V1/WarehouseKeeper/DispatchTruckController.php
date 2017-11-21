<?php

namespace App\Http\Controllers\Api\V1\WarehouseKeeper;

use App\Models\DeliveryTruck;
use App\Models\DispatchTruck;
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
            $orderId = $request->input('order_id');
            $dtv_id = $request->input('dispatch_truck_id');
            $dispatchTruck = $truck->dispatchTruck()->where('status', '<=', $dispatchTruckStatus['wait'])->first();

            if (!$dispatchTruck) {
                $dispatchTruck = $truck->dispatchTruck()->create([
                    'status' => $dispatchTruckStatus['wait']
                ]);
            }

            if (isset($dtv_id) && $dtv_id != $dispatchTruck->id) {
                $dtv = DispatchTruck::find($dtv_id);
                if ($dtv->status > $dispatchTruckStatus['wait']) {
                    return $this->error('该订单已发车.');
                }
                if ($dtv->orders->count() < 2) {
                    $dtv->truck()->update(['status' => cons('truck.status.spare_time')]);
                    $dtv->delete();
                }
            }

            $this->updateBatch($orderId, $dispatchTruck);
            $truck->update(['status' => cons('truck.status.wait')]);
            DB::commit();
            return $this->success('保存成功');
        } catch (\Exception $e) {
            DB::rollback();
            //info($e->getMessage() .'----'. $e->getLine().'行');
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
            return $this->error('没有找到发车单!');
        }
        try {
            if ($dispatchTruck->status > cons('dispatch_truck.status.wait')) {
                return $this->error('订单已发车,不能操作');
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
        if (!is_array($orderIds)) {
            $orderIds = [$orderIds];
        }
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
     *
     * 发车
     */
    public function createDispatchTruckVoucher(Request $request)
    {
        $dispatchTruck = DispatchTruck::with('orders.orderGoods')->find($request->input('dtv_id'));

        if (!$dispatchTruck) {
            return $this->error('参数错误!');
        }
        if ($dispatchTruck->status > cons('dispatch_truck.status.wait')) {
            return $this->error('订单已发车,不能操作');
        }
        try {
            DB::beginTransaction();
            //出库
            $inventoryService = new  InventoryService();
            $inventoryNumber = $inventoryService->getInventoryNumber();
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
            return $this->error('保存失败');
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
            'truck',
            'deliveryMans',
            'orders.orderGoods.goods.goodsPieces',
            'orders.shippingAddress.address',
            'returnOrders.goods.goodsPieces',
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
        $dtv->can_back = 1;
        foreach ($dtv->orders as $order) {
            $order->addHidden(['orderGoods', 'salesmanVisitOrder']);
            $order->shippingAddress->setAppends(['address_name']);
            $order->shippingAddress->addHidden(['address']);
            if ($order->status < cons('order.status.invalid') && !$order->delivery_finished_at) {
                $dtv->can_back = 0;
            }
        };
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
        ])->condition($request->all())->whereIn('delivery_truck_id', $trucks->pluck('id'))->get()->each(function ($item
        ) {
            $item->setAppends(['order_amount', 'status_name', 'is_return_order']);
            $item->addHidden(['orders', 'returnOrders']);
            $item->can_back = 1;
            foreach ($item->orders as $order) {
                if ($order->status < cons('order.status.invalid') && !$order->delivery_finished_at) {
                    $item->can_back = 0;
                    break;
                }
            };
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
        if (!$dtv || $dtv->status > cons('truck.status.wait')) {
            return $this->error('参数错误');
        }
        if ($dtv->status > cons('dispatch_truck.status.wait')) {
            return $this->error('订单已发车,不能操作');
        }
        $newTruckId = $request->input('new_truck_id');
        $result = DB::transaction(function () use ($dtv, $newTruckId) {
            $dtv->truck()->update(['status' => cons('truck.status.spare_time')]);
            //DB::table('dispatch_truck_delivery_man')->where('dispatch_truck_id',$dtv->id)->delete();
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
                if ($dtv->returnOrders->count()) {
                    $inventoryService = new InventoryService();
                    foreach ($dtv->orders as $order) {
                        foreach ($order->orderGoods as $orderGoods){
                            $inventoryService->clearOut($orderGoods);
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

                    /*$orderArray = [];
                    $returnOrders = $dtv->returnOrders->groupBy('order_id');
                    foreach ($returnOrders as $key => $returnOrder) {
                        $tmpGoodsArray = [];
                        foreach ($returnOrder as $orderGoods) {
                            if (!array_key_exists($orderGoods->goods_id, $tmpGoodsArray)) {
                                $tmpGoodsArray[$orderGoods->goods_id] = [
                                    'goods_id' => $orderGoods->goods_id,
                                    'pieces' => GoodsService::getMinPieces($orderGoods->goods),
                                    'quantity' => $orderGoods->num * GoodsService::getPiecesSystem($orderGoods->goods,
                                            $orderGoods->pieces),
                                ];
                            } else {
                                $tmpGoodsArray[$orderGoods->goods_id]['quantity'] += $orderGoods->num * GoodsService::getPiecesSystem($orderGoods->goods,
                                        $orderGoods->pieces);
                            }
                        }
                        $orderArray[$key] = $tmpGoodsArray;
                        unset($tmpGoodsArray);
                    }
                    $inventoryService = new InventoryService();
                    foreach ($orderArray as $key => $item) {
                        foreach ($item as $value) {
                            $outRecord = $inventoryService->getOutRecordByWK($key, $value['goods_id']);
                            if (!$outRecord) {
                                return $this->error('入库错误');
                                break;
                            }
                            foreach ($outRecord as $record) {
                                $data = [
                                    'source' => cons('inventory.source.wk_return'),
                                    'inventory_number' => $inventoryService->getInventoryNumber(),
                                    'user_id' => wk_auth()->id(),
                                    'goods_id' => $record->goods_id,
                                    'shop_id' => wk_auth()->user()->shop_id,
                                    'inventory_type' => cons('inventory.inventory_type.manual'),
                                    'action_type' => cons('inventory.action_type.in'),
                                    'order_number' => '',
                                    'operate' => 'warehouseKeeper',
                                    'remark' => '订单:' . $record->order_number . ' 退货入库!',
                                    'production_date' => $record->production_date,
                                    'cost' => $record->in_cost,
                                    'pieces' => $record->in_pieces
                                ];
                                if ($record->quantity >= $value['quantity']) {
                                    $data['quantity'] = $value['quantity'];
                                    $data['surplus'] = $value['quantity'];
                                    Inventory::create($data);
                                    break;
                                } else {
                                    $data['quantity'] = $value['quantity'] - $record->quantity;
                                    $data['surplus'] = $value['quantity'] - $record->quantity;
                                    Inventory::create($data);
                                    $value['quantity'] -= $record->quantity;
                                }
                                unset($data);
                            }
                        }
                    }*/
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
            info($e->getMessage() . '----' .$e->getFile(). $e->getLine() . '行');
            return $this->error('操作失败');
        }
    }
}
