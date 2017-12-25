<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 17:57
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\v1\UpdateOrderRequest;
use App\Models\Goods;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\SalesmanCustomer;
use App\Models\SalesmanVisitOrder;
use App\Models\SalesmanVisitOrderGoods;
use App\Models\Shop;
use App\Models\ConfirmOrderDetail;
use App\Services\CartService;
use App\Services\GoodsService;
use App\Services\InventoryService;
use App\Services\OrderService;
use App\Services\RedisService;
use App\Services\ShopService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class OrderController extends Controller
{
    /**
     * 构造方法限制终端商访问销售功能
     */
    public function __construct()
    {
        $this->middleware('deposit', [
            'only' => [
                'getWaitConfirmBySeller',
                'getListOfSell',
                'getNonSend',
                'getOrderCountSell',
                'getPendingCollection',
                'getDetailOfSell',
                'putBatchFinishOfSell',
                'putChangeOrder'
            ]
        ]);
    }

    /**
     * 买家查询订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getListOfBuy()
    {
        $orders = Order::OfBuy(auth()->id())->useful()->with([
            'orderReason',
            'shop.user',
            'user',
            'applyPromo',
            'coupon',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->paginate();

        return $this->success($this->_hiddenOrdersAttr($orders));
    }

    /**
     * 获取业务订单列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getBusiness(Request $request)
    {
        $shop = auth()->user()->shop;
        $salesmenId = $shop->salesmen->pluck('id');
        $data = $request->all();
        $orders = SalesmanVisitOrder::with([
            'orderGoods' => function ($goods) {
                $goods->select([
                    'salesman_visit_order_id',
                    'goods_id',
                    'num',
                    'pieces',
                    'price',
                ]);
            },
            'salesmanCustomer.businessAddress',
            'orderGoods.goods'
        ])->select([
            'id',
            'created_at',
            'amount',
            'type',
            'order_id',
            'salesman_customer_id',
            'salesman_id',
            'status',
            'shop_id'
        ])->where('shop_id', auth()->user()->shop_id)->whereIn('salesman_id',
            $salesmenId)->OfData($data)->orderBy('updated_at', 'desc')->paginate();
        $orders->each(function ($order) {
            $order->orderGoods->each(function ($goods) {
                $goods->addHidden(['goods']);
            });
            $order->customer_name = $order->salesmanCustomer->name ?? '';
            $order->addHidden(['order_status_name']);
        });
        return $this->success($orders->toArray());
    }

    /**
     * 查询待付款订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getNonPayment()
    {
        $orders = Order::ofBuy(auth()->id())->nonPayment()->with([
            'shop.user',
            'user',
            'coupon',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->paginate();

        return $this->success($this->_hiddenOrdersAttr($orders));
    }

    /**
     * 获取待收货信息--买家操作
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getNonArrived()
    {
        $orders = Order::ofBuy(auth()->id())->nonArrived()->with([
            'shop.user',
            'user',
            'coupon',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->paginate();

        return $this->success($this->_hiddenOrdersAttr($orders));
    }

    /**
     * 获取买家待确认订单
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getWaitConfirmByUser()
    {
        $orders = Order::ofBuy(auth()->id())->WaitConfirm()->with([
            'shop.user',
            'user',
            'coupon',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->paginate();
        return $this->success($this->_hiddenOrdersAttr($orders));
    }

    /**
     * 获取卖家待确认订单
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getWaitConfirmBySeller()
    {
        $orders = Order::ofSell(auth()->user()->shop_id)->with('user.shop', 'goods', 'coupon',
            'shop.user', 'orderReason')->WaitConfirm()->paginate();
        return $this->success($this->_hiddenOrdersAttr($orders, false));
    }

    /**
     * 买家获取订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getDetailOfBuy(Request $request)
    {
        $order = Order::where('user_id', auth()->id())->find($request->input('order_id'));

        if (is_null($order)) {
            return $this->error('订单不存在');
        }

        $order = $this->_orderLoadData($order);

        $order->trade_no = $this->_getTradeNoByOrder($order);
        $goods = (new OrderService())->explodeOrderGoods($order);

        return $this->success([
            'order' => $this->_hiddenOrderAttr($order),
            'mortgageGoods' => $goods['mortgageGoods'],
            'orderGoods' => $goods['orderGoods']
        ]);
    }


    /**
     *  卖家查询订单列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getListOfSell(Request $request)
    {
        //订单状态
        $orderStatus = cons()->lang('order.status');
        $payStatus = array_only(cons()->lang('order.pay_status'), ['refund', 'refund_success']);

        $status = array_merge(['wait_receive' => '未收款'], $payStatus, $orderStatus);

        $search = $request->all();

        $orders = Order::ofSell(auth()->user()->shop_id)->useful()->with([
            'orderReason',
            'user.shop',
            'shippingAddress.address',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ]);
        if (is_numeric($searchContent = array_get($search, 'search_content'))) {
            $orders = $orders->where('id', $searchContent);
        } elseif ($searchContent) {
            $orders = $orders->ofSelectOptions($search)->ofUserShopName($searchContent);
        } else {
            $orders = $orders->ofSelectOptions($search);
        }

        //已作废 、已发货、已完成、退款成功按操作时间倒序
        if (array_get($search, 'status') == 'send') {
            $orders->orderBy('send_at', 'DESC');
        } else {
            $orders->orderBy('updated_at', 'DESC');
        }

        $orders = $orders->paginate();

        return $this->success(['orders' => $this->_hiddenOrdersAttr($orders, false)->toArray(), 'status' => $status]);
    }

    /**
     * 获取待发货订单列表--卖家操作
     *
     * @return mixed
     */
    public function getNonSend()
    {
        $orders = Order::ofSell(auth()->user()->shop_id)->nonSend()->with([
            'user.shop',
            'coupon',
            'shop.user',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->paginate();

        return $this->success($this->_hiddenOrdersAttr($orders, false));
    }

    /**
     * 获取待发货订单列表--买家操作
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getUnsent()
    {
        $orders = Order::ofBuy(auth()->id())->useful()->with([
            'user.shop',
            'shop.user',
            'coupon',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->nonSend()->paginate();
        return $this->success($this->_hiddenOrdersAttr($orders, false));
    }

    /**
     * 获取订单统计--买家操作
     *
     * @return array
     */
    public function getOrderCountBuy()
    {
        $status = cons('order.status');
        $payStatus = cons('order.pay_status');
        $payType = cons('pay_type');
        $countData = Order::select(DB::raw('count(if(pay_status=' . $payStatus['non_payment'] . ' and status < ' . $status['finished'] . ' and ((status > ' . $status['non_send'] . '  and pay_type =' . $payType['cod'] . ') or (status >= ' . $status['non_send'] . '  and pay_type =' . $payType['online'] . ')),true,null)) AS waitReceive,count(if(((pay_type=' . $payType['online'] . ' and pay_status=' . $payStatus['payment_success'] . ') or (pay_type=' . $payType['cod'] . ' and pay_status<' . $payStatus['refund'] . ')) and status=' . $status['non_send'] . ',true,null)) as waitSend,count(if(status=' . $status['send'] . ',true,null)) as refund,count(if(status=' . $status['non_confirm'] . ',true,null)) as waitConfirm'))->ofBuy(auth()->id())->useful()->first();
        return [
            'waitReceive' => $countData->waitReceive,
            'waitSend' => $countData->waitSend,
            'refund' => $countData->refund,
            'waitConfirm' => $countData->waitConfirm,
        ];
    }

    /**
     * 获取订单统计--卖家操作
     *
     * @return array
     */
    public function getOrderCountSell()
    {
        $status = cons('order.status');
        $payStatus = cons('order.pay_status');
        $payType = cons('pay_type');
        $countData = Order::select(DB::raw('count(if(pay_status=' . $payStatus['non_payment'] . ',true,null)) AS waitReceive,count(if(((pay_type=' . $payType['online'] . ' and pay_status=' . $payStatus['payment_success'] . ') or (pay_type=' . $payType['cod'] . ' and pay_status<' . $payStatus['payment_success'] . ')) and status=' . $status['non_send'] . ',true,null)) as waitSend,count(if((pay_type=' . $payType['cod'] . ' and status=' . $status['send'] . ') or (pay_type=' . $payType['pick_up'] . ' and status=' . $status['non_send'] . '),true,null)) as refund,count(if(status=' . $status['non_confirm'] . ',true,null)) as waitConfirm'))->ofSell(auth()->user()->shop_id)->useful()->first();
        return [
            'waitReceive' => $countData->waitReceive,
            'waitSend' => $countData->waitSend,
            'refund' => $countData->refund,
            'waitConfirm' => $countData->waitConfirm,
        ];
    }

    /**
     * 获取待收款订单列表,针对货到付款
     *
     * @return mixed
     */
    public function getPendingCollection()
    {
        $orders = Order::ofSell(auth()->user()->shop_id)->getPayment()->useful()->with('user.shop', 'goods', 'coupon',
            'shop.user')->paginate();

        return $this->success($this->_hiddenOrdersAttr($orders, false));
    }

    /**
     * 卖家查询订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getDetailOfSell(Request $request)
    {
        $orderId = $request->input('order_id');
        $order = Order::ofSell(auth()->user()->shop_id)->useful()->find($orderId);
        if (is_null($order)) {
            return $this->error('订单不存在');
        }

        $order = $this->_hiddenOrderAttr($this->_orderLoadData($order), false);

        $order->orderChangeRecode = $order->orderChangeRecode->reverse()->each(function ($recode) use ($order) {
            $recode->setAppends(['name']);
        });
        $order->trade_no = $this->_getTradeNoByOrder($order);

        $goods = (new OrderService())->explodeOrderGoods($order);

        $order->addHidden(['order_change_recode']);

        return $this->success([
            'order' => $order,
            'mortgageGoods' => $goods['mortgageGoods'],
            'orderGoods' => $goods['orderGoods'],
        ]);
    }

    /**
     * 批量取消订单确认状态，在线支付：确认但未付款，可取消；货到付款：确认但未发货，可取消
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putCancelSure(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        if (!$orderIds) {
            return $this->error('请选择需要取消的订单');
        }
        $orders = Order::with('shop.user', 'user')->whereIn('id', $orderIds)->useful()->get();

        $failOrderIds = [];
        foreach ($orders as $order) {
            if (!$order->can_cancel || !($order->user_id == auth()->id() || $order->shop_id == auth()->user()->shop_id)) {
                if (count($orders) == 1) {
                    return $this->error('取消失败');
                }
                $failOrderIds[] = $order->id;
                continue;
            }
            $order->fill([
                'is_cancel' => cons('order.is_cancel.on'),
                'cancel_by' => auth()->id(),
                'cancel_at' => Carbon::now()
            ])->save();

            // 返回优惠券
            (new OrderService())->backCoupon($order);

            //推送通知
            if ($order->user_id == auth()->id()) {
                $redisKey = 'push:seller:' . $order->shop->user_id;
                $msg = 'buyer';
            } else {
                $redisKey = 'push:user:' . $order->user_id;
                $msg = 'seller';
            }

            $redisVal = '订单:' . $order->id . cons()->lang('push_msg.cancel_by_' . $msg);
            (new RedisService)->setRedis($redisKey, $redisVal, cons('push_time.msg_life'));


        }

        return $this->success(['failOrderIds' => $failOrderIds]);
    }

    /**
     * 卖家确认订单信息
     *
     * @param $orderId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putOrderConfirm($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return $this->error('订单不存在');
        }
        if (!$order->can_confirm || $order->shop_id != auth()->user()->shop_id) {
            return $this->error('订单不能确认');
        }

        $flag = DB::transaction(function () use ($order) {
            $shopId = $order->user->shop->id;
            foreach ($order->orderGoods as $goods) {
                $confirmOrderDetail = ConfirmOrderDetail::where([
                    'goods_id' => $goods->goods_id,
                    'shop_id' => $shopId
                ])->first();

                if (empty($confirmOrderDetail)) {
                    ConfirmOrderDetail::create([
                        'goods_id' => $goods->goods_id,
                        'price' => $goods->price,
                        'pieces' => $goods->pieces,
                        'shop_id' => $shopId,
                    ]);
                } else {
                    $confirmOrderDetail->fill(['price' => $goods->price, 'pieces' => $goods->pieces])->save();
                }
            }
            $res = $this->_syncToBusiness($order) && $order->fill([
                    'status' => cons('order.status.non_send'),
                    'confirm_at' => Carbon::now(),
                    'numbers' => (new OrderService())->getNumbers($order->shop_id)
                ])->save();
            if (!$res) {
                return false;
            }
            return true;
        });

        if ($flag) {
            return $this->success('订单确认成功');
        }
        return $this->error('订单确认失败，请重试');
    }

    /**
     * 订单作废
     *
     * @param $orderId
     * @return string|\WeiHeng\Responses\Apiv1Response
     */
    public function postInvalid(Request $request, $orderId)
    {
        $order = Order::find($orderId);
        $reason = $request->input('reason');
        if (!$order) {
            return $this->error('订单不存在');
        }

        if (!$reason) {
            return $this->error('作废原因不能为空');
        }

        if (!$order->can_invalid || $order->shop_id != auth()->user()->shop_id) {
            return $this->error('订单不能作废');
        }
        if ($order->dispatch_status >= cons('dispatch_truck.status.delivering')) {
            return $this->error('已发车订单,不能作废');
        }

        if ($order->dispatch_status < cons('dispatch_truck.status.delivering') || !$order->can_invalid || $order->shop_id != auth()->user()->shop_id) {
            $order->dispatch_truck_id = '';
        }
        try {
            DB::beginTransaction();
            $order->fill(['status' => cons('order.status.invalid')])->save();
            $order->orderReason()->create(['type' => 1, 'reason' => $reason]);
            // 返回优惠券
            $orderService = new OrderService();
            //返还陈列费
            if ($order->salesmanVisitOrder) {
                $result = $orderService->backDisplayFee($order->salesmanVisitOrder);
                if (!$result) {
                    throw new \Exception('返还陈列费时出错');
                }
            }
            $orderService->backCoupon($order);
            $orderService->addOperateRecord($order, $order->shop_id, $order->shop_name, '店铺', '作废订单');
            DB::commit();
            return $this->success('订单作废成功');
        } catch (\Exception $e) {
            DB::rollback();
            info($e->getMessage() . '----' . $e->getFile() . '---' . $e->getLine());
            return $this->error('作废订单时出现问题');
        }
    }

    /**
     * 买家批量确认订单完成,仅针对在线支付订单
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putBatchFinishOfBuy(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $orders = Order::where('user_id', auth()->id())->with('systemTradeInfo', 'goods')->whereIn('id',
            $orderIds)->useful()->get();

        if ($orders->isEmpty()) {
            return $this->error('请选择订单');
        }

        $failIds = [];
        //商品库存服务
        foreach ($orders as $order) {
            if (!$order->can_confirm_arrived || $order->user_id != auth()->id()) {
                if ($orders->count() == 1) {
                    return $this->error('确认收货失败');
                }
                $failIds[] = $order->id;
                continue;
            }

            if ($order->pay_type == cons('pay_type.online')) {
                //在线支付确认收货买家入库
                (new InventoryService())->autoIn($order);
            }

            if (($tradeModel = $order->systemTradeInfo) && $order->fill([
                    'status' => cons('order.status.finished'),
                    'finished_at' => Carbon::now()
                ])->save()
            ) {
                //更新商品销量
                GoodsService::addGoodsSalesVolume($order);
                //更新systemTradeInfo完成状态
                $tradeModel->fill([
                    'is_finished' => cons('trade.is_finished.yes'),
                    'finished_at' => Carbon::now()
                ])->save();
                //更新用户balance
                $shopOwner = $order->shop->user;
                $shopOwner->balance += $tradeModel->amount;
                $shopOwner->save();
                //通知卖家
                $redisKey = 'push:seller:' . $shopOwner->id;
                $redisVal = '您的订单:' . $order->id . ',' . cons()->lang('push_msg.finished');

                (new RedisService)->setRedis($redisKey, $redisVal, cons('push_time.msg_life'));
            } else {
                $failIds[] = $order->id;
            }
        }
        return $this->success(['failIds' => $failIds]);
    }

    /**
     * 卖家批量确认订单完成,仅针对货到付款有效
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putBatchFinishOfSell(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $orders = Order::ofSell(auth()->user()->shop_id)->with('goods')->useful()->whereIn('id', $orderIds)->get();
        if ($orders->isEmpty()) {
            return $this->error('确认收款失败');
        }

        $failIds = []; //失败订单id
        $nowTime = Carbon::now();
        //商品库存服务
        $inventoryService = new InventoryService();
        foreach ($orders as $order) {
            //更新商品销量
            GoodsService::addGoodsSalesVolume($order);
            if (!$order->can_confirm_collections) {
                if (count($orders) == 1) {
                    return $this->error('确认收款失败');
                }
                $failIds[] = $order->id;
                continue;
            }

            if ($order->pay_type == cons('pay_type.pick_up')) {
                //自提订单收款时卖家出库
                $outResult = $inventoryService->autoOut($order);
                if (!$outResult) {
                    return $this->error($inventoryService->getError());
                };
            }
            if ($order->pay_type == cons('pay_type.pick_up') || $order->pay_type == cons('pay_type.cod')) {
                //自提订单和货到付款订单收款时买家入库
                $inventoryService->autoIn($order);
            }

            if (!$order->fill([
                'pay_status' => cons('order.pay_status.payment_success'),
                'paid_at' => $nowTime,
                'status' => cons('order.status.finished'),
                'finished_at' => $nowTime
            ])->save()
            ) {
                $failIds[] = $order->id;
            }
        }
        return $this->success(['failIds' => $failIds]);
    }

    /**
     * 批量修改发货状态。不区分付款状态  (作废)---
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putBatchSend(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $deliveryManIds = array_unique((array)$request->input('delivery_man_id'));

        $deliveryMans = auth()->user()->shop->deliveryMans()->whereIn('id', $deliveryManIds)->count();

        if (count($deliveryManIds) != $deliveryMans) {
            return $this->error('配送人员错误');
        }

        $orders = Order::ofSell(auth()->user()->shop_id)->with('orderGoods')->useful()->whereIn('id', $orderIds)->get();

        if ($orders->isEmpty()) {
            return $this->error('订单不能为空');
        }

        //通知买家订单已发货
        $failIds = [];
        //商品库存服务
        $inventoryService = new InventoryService();
        foreach ($orders as $order) {
            if (!$order->can_send || $order->shop_id != auth()->user()->shop->id) {
                if (count($orders) == 1) {
                    return $this->error('操作失败');
                }
                $failIds[] = $order->id;
                continue;
            }
            //卖家出库
            $result = $inventoryService->autoOut($order);
            if ($result == false) {
                return $this->error($inventoryService->getError());
            };
            if ($order->fill(['status' => cons('order.status.send'), 'send_at' => Carbon::now()])->save()) {
                $redisKey = 'push:user:' . $order->user_id;
                $redisVal = '您的订单' . $order->id . ',' . cons()->lang('push_msg.send');
                (new RedisService)->setRedis($redisKey, $redisVal, cons('push_time.msg_life'));

                $order->deliveryMan()->sync($deliveryManIds);
            }
        }
        return $this->success(['failIds' => $failIds]);

    }

    /**
     * 卖家订单统计功能
     *
     */
    public function getStatistics()
    {
        $redisService = (new RedisService);
        $key = 'statistics:' . auth()->id();

        if ($redisService->has($key)) {
            return $this->success(json_decode($redisService->get($key), true));
        }

        //当日新增订单数,完成订单数,所有累计订单数,累计完成订单数;7日对应
        $today = Carbon::now();
        $aWeekAgo = $today->copy()->subDay(6)->startOfDay();
        $builder = Order::ofSell(auth()->user()->shop_id)->useful();

        $new = with(clone $builder)->whereBetween('created_at', [$aWeekAgo, $today])->get();
        $finish = with(clone $builder)->whereBetween('finished_at', [$aWeekAgo, $today])->get();

        $dates = list_in_days($aWeekAgo, $today);

        foreach ($dates as $day) {
            $statistics['sevenDay'][] = [
                'new' => $new->filter(function ($order) use ($day) {
                    return $order->created_at->format('Y-m-d') == $day;
                })->count(),
                'finish' => $finish->filter(function ($order) use ($day) {
                    return (new Carbon($order->finished_at))->format('Y-m-d') == $day;
                })->count(),
            ];
        }

        $statistics['today'] = end($statistics['sevenDay']);
        $statistics['all'] = [
            'count' => $builder->count(),
            'finish' => $builder->where('status', cons('order.status.finished'))->count()
        ];
        //存入redis

        (new RedisService)->setRedis($key, json_encode($statistics), 60);
        return $this->success($statistics);

    }

    /**
     * 获取确认订单页
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getConfirmOrder()
    {
        $user = auth()->user();
        $carts = $user->carts()->where('status', 1)->with('goods')->get();
        $shops = (new CartService($carts))->formatCarts(null, true);
        $payType = cons()->valueLang('pay_type');//支付类型
        $payWay = cons()->lang('pay_way');//支付方式
        return $this->success([
            'shops' => $shops,
            'pay_type' => $payType,
            'pay_way' => $payWay
        ]);
    }

    /**
     * 确认订单消息
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postConfirmOrder(Request $request)
    {
        $orderGoodsNum = $request->input('num');

        if (empty($orderGoodsNum)) {
            return $this->error('选择的商品不能为空');
        }
        $confirmedGoods = auth()->user()->carts()->whereIn('goods_id', array_keys($orderGoodsNum));

        $carts = $confirmedGoods->with('goods')->get();

        //验证
        $cartService = new CartService($carts);

        if (!$cartService->validateOrder($orderGoodsNum, true)) {
            return $this->error($cartService->getError());
        }

        if (false !== $confirmedGoods->update(['status' => 1])) {
            return $this->success('确认成功');
        } else {
            return $this->error('确认订单失败');
        }
    }

    /**
     * 提交订单
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postSubmitOrder(Request $request)
    {
        $data = $request->all();

        $orderService = new OrderService;

        $result = $orderService->orderSubmitHandle($data);

        return $result ? $this->success($result) : $this->error($orderService->getError());
    }

    /**
     * 修改订单
     *
     * @param \App\Http\Requests\Api\v1\UpdateOrderRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putChangeOrder(UpdateOrderRequest $request)
    {
        //判断该订单是否存在
        $order = Order::ofSell(auth()->user()->shop_id)->useful()->find(intval($request->input('order_id')));
        if (!$order || !$order->can_change_price) {
            return $this->error('订单不存在或不能修改');
        }
        $attributes = $request->all();

        $orderService = new OrderService;
        $flag = $orderService->changeOrder($order, $attributes, auth()->id(), 'user');

        return $flag ? $this->success('修改成功') : $this->error($orderService->getError());
    }


    /**
     * 网页端信息提示
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderPolling()
    {
        $redis = new RedisService;

        foreach (['user', 'seller', 'withdraw'] as $item) {
            $key = 'push:' . $item . ':' . auth()->id();
            if ($redis->has($key)) {
                $content = $redis->get($key);
                $redis->del($key);
                return $this->success(['type' => $item, 'data' => $content]);
            }
        }

        return $this->success([]);
    }

    /**
     * 获取支付方式
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getPayWay(Request $request)
    {
        $payWayConf = cons()->lang('pay_way');
        $payWay = $request->input('pay_type', key($payWayConf));

        return $this->success($payWayConf[$payWay]);
    }

    /**
     * 获取店铺最低配送额
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getMinMoney(Request $request)
    {
        $shippingAddressId = $request->input('shipping_address_id');
        $shopIds = $request->input('shop_id');

        $user = auth()->user();
        $shippingAddress = $user->shippingAddress()->with('address')->find($shippingAddressId);
        if (is_null($shippingAddress)) {
            return $this->error('收货地址不存在');
        }
        $shops = Shop::whereIn('id', $shopIds)->with('deliveryArea')->get();

        if ($shops->isEmpty()) {
            return $this->error('店铺不存在');
        }

        $result = (new ShopService())->getShopMinMoneyByShippingAddress($shops, $shippingAddress);

        return $this->success(['shopMinMoney' => $result]);
    }

    /**
     * 获取商品价格
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getGoodsPrice(Request $request)
    {
        $deliveryMode = $request->input('delivery_mode', 1);
        $goodsIds = $request->input('goods_id');
        $goods = Goods::whereIn('id', $goodsIds)->get();

        if ($goods->isEmpty()) {
            return $this->error('商品不存在');
        }

        $goodsPrice = [];
        $isDelivery = ($deliveryMode == cons('order.delivery_mode.delivery'));

        foreach ($goods as $item) {
            $goodsPrice[] = [
                'goods_id' => $item->id,
                'price' => $isDelivery ? $item->price : $item->pick_up_price
            ];
        }
        return $this->success(['goodsPrice' => $goodsPrice]);
    }

    /**
     * 设置订单下载模版
     *
     * @param $templeteId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postTemplete($templeteId)
    {
        $templetes = cons()->valueLang('order.templete');
        $shopId = auth()->user()->shop_id;

        $templeteId = in_array($templeteId, array_keys($templetes)) ? $templeteId : key($templetes);

        app('order.download')->setTemplete($shopId, $templeteId);

        return $this->success('选择模版成功');

    }

    /**
     * 删除订单商品
     *
     * @param $goodsId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function deleteGoodsDelete($goodsId)
    {
        $orderGoods = OrderGoods::with('order')->find($goodsId);

        if (is_null($orderGoods) || $orderGoods->order->shop_id != auth()->user()->shop_id) {
            return $this->error('订单商品不存在');
        }

        if ($orderGoods->order->dispatch_status == cons('dispatch_truck.status.delivering')) {
            return $this->error('已经发车,不能删除');
        }
        $order = $orderGoods->order;
        $result = DB::transaction(function () use ($orderGoods, $order) {
            $orderGoodsPrice = $orderGoods->total_price;

            if ($order->status == cons('order.status.send')) {
                $inventoryService = new InventoryService();
                //清除出库记录
                $inventoryService->clearOut($orderGoods);
            }
            $orderGoods->delete();
            if ($order->orderGoods()->where('type', cons('order.goods.type.order_goods'))->count() == 0) {
                //订单商品删完了标记为作废
                $order->fill(['status' => cons('order.status.invalid'), 'price' => 0])->save();
            } elseif ($orderGoodsPrice > 0) {
                $order->decrement('price', $orderGoodsPrice);
            }

            $content = '订单商品id:' . $orderGoods->goods_id . '，已被删除';
            (new OrderService())->addOrderChangeRecord($order, $content);
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
     * 根据订单获取交易流水号
     *
     * @param $order
     * @return string
     */
    private function _getTradeNoByOrder($order)
    {
        $tradeNo = $order->systemTradeInfo()->pluck('trade_no');

        return $tradeNo ? $tradeNo : '';
    }

    /**
     * 订单同步至业务
     *
     * @param $order
     * @return bool
     */
    private function _syncToBusiness($order)
    {
        if (!is_null($order->salesmanVisitOrder)) {
            return true;
        }

        $shop = $order->shop;
        $userShopId = $order->user->shop_id;

        $salesmanCustomer = SalesmanCustomer::whereIn('id', $shop->salesmenCustomer->pluck('id'))->where('shop_id',
            $userShopId)->first();
        if (is_null($salesmanCustomer)) {
            return true;
        }
        $orderData = [
            'salesman_customer_id' => $salesmanCustomer->id,
            'amount' => $order->price,
            'order_id' => $order->id,
            'salesman_id' => $salesmanCustomer->salesman_id,
            'order_remark' => $order->remark,
            'status' => cons('salesman.order.status.passed'),
            'shop_id' => $shop->id
        ];
        $result = DB::transaction(function () use ($orderData, $order) {
            $orderTemp = SalesmanVisitOrder::create($orderData);

            if ($orderTemp->exists) {
                $orderGoods = [];
                foreach ($order->orderGoods as $goods) {
                    $orderGoods[] = new SalesmanVisitOrderGoods([
                        'goods_id' => $goods->goods_id,
                        'price' => $goods->price,
                        'num' => $goods->num,
                        'pieces' => $goods->pieces,
                        'amount' => $goods->total_price,
                        'salesman_visit_order_id' => $orderTemp->id
                    ]);
                }
                //保存订单商品
                if (!$orderTemp->orderGoods()->saveMany($orderGoods)) {
                    return false;
                }
                return 'success';
            } else {
                return false;
            }

        });

        return $result === 'success';
    }

    /**
     *  订单预加载处理
     *
     * @param $order
     * @return mixed
     */
    private function _orderLoadData($order)
    {
        $payType = cons('pay_type');
        if ($order->pay_type == $payType['pick_up']) {
            $order->load(['goods.images', 'shop.shopAddress']);
        } else {
            $order->load(['goods', 'gifts', 'deliveryMan', 'shippingAddress.address', 'orderReason']);
        }
        return $order;
    }

    /**
     * @param $orders
     * @param  bool $buyer
     */
    private function _hiddenOrdersAttr($orders, $buyer = true)
    {
        $orders->each(function ($order) use ($buyer) {
            $this->_hiddenOrderAttr($order, $buyer);
        });
        return $orders;
    }

    /**
     * @param Order $order
     * @param bool $buyer
     * @return mixed
     */
    private function _hiddenOrderAttr($order, $buyer = true)
    {
        $order->goods->each(function ($goods) {
            $goods->addHidden(['introduce']);
        });
        if (!$buyer && $order->user_id > 0 && $order->user) {
            $order->user->setVisible(['id', 'shop', 'type']);
            $order->user->shop->setVisible(['name', 'contact_person', 'contact_info'])->setAppends([]);
        }
        $order->addHidden([
            'applyPromo'
        ]);
        $order->setAppends([
            'status_name',
            'payment_type',
            'step_num',
            'can_cancel',
            'can_confirm',
            'can_send',
            'can_confirm_collections',
            'can_export',
            'can_payment',
            'can_confirm_arrived',
            'after_rebates_price',
            'user_shop_name',
            'refund_reason',
            'invalid_reason',
            'promo'
        ]);
        $buyer && $order->shop && $order->shop->setAppends([]);

        return $order;
    }

}