<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\WarehouseKeeper\DispatchTruckController;
use App\Models\DispatchTruck;
use App\Models\DispatchTruckReturnOrder;
use App\Models\OrderGoods;
use App\Models\DeliveryMan;
use App\Models\VersionRecord;
use App\Models\Order;
use App\Services\DispatchTruckService;
use App\Services\InventoryService;
use DB;
use Hash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Api\v1\DeliveryRequest;
use App\Http\Requests\Api\v1\DeliveryLoginRequest;
use App\Http\Requests\Api\v1\UpdatePasswordRequest;
use App\Http\Requests\Api\v1\UpdateOrderRequest;
use App\Services\DeliveryService;
use App\Services\RedisService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Gate;

class DeliveryController extends Controller
{
    /**
     * DeliveryManController constructor.
     */
    /*public function __construct()
    {
        $this->middleware('deposit', ['except' =>['login', 'logout']]);
    }*/

    /**
     *
     *返回配送人员登陆页面 测试用
     */
    public function index()
    {
        return view('api.login');
    }

    /**
     *
     * 配送人员登录
     *
     * @param \App\Http\Requests\Api\v1\DeliveryLoginRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     *
     */
    public function login(DeliveryLoginRequest $request)
    {

        $userName = $request->input('user_name');
        $password = $request->input('password');
        if (empty($userName) || empty($password)) {
            return $this->invalidParam('password', '账号或密码不能为空');
        }

        $deliveryMan = DeliveryMan::where('user_name', $userName)->first();
        if (!$deliveryMan || !$deliveryMan->status) {
            return $this->invalidParam('password', '账号不存在或不能使用');
        }

        //检测账号是否过期
        if ($deliveryMan->is_expire) {
            return $this->invalidParam('password', '账号已过期');
        }

        $password = md5($password);

        if ($password != $deliveryMan->password) {
            return $this->invalidParam('password', '密码错误');
        }
        $nowTime = Carbon::now();

        $deliveryMan->last_login_at = $nowTime;
        if ($deliveryMan->save()) {
            delivery_auth()->login($deliveryMan, true);
            $deliveryMan->setAppends(['shop_name']);
            return $this->success($deliveryMan);

        }


    }

    /**
     * 所有已分配订单信息 // 未配送订单
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function orders(DeliveryRequest $request)
    {
        $idName = $request->input('id_name', null);
        $dtv = delivery_auth()->user()->dispatchTruck()->whereNull('back_time')->where('status',
            cons('dispatch_truck.status.delivering'))->get();
        $orders = Order::
        whereNull('delivery_finished_at')
            ->whereIn('dispatch_truck_id', $dtv->pluck('id'))
            ->useful()
            ->noInvalid()
            ->ofOrderIdName($idName)
            ->with('user.shop', 'shippingAddress.address', 'coupon')
            ->orderBy('dispatch_truck_sort')
            ->get();
        $orders->each(function ($order) {
            $order->is_pay = $order->pay_status == 1 ? 1 : 0;
            $order->pieces = cons()->valueLang('goods.pieces');
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
                'can_change_price',
                'user_shop_name',
                'after_rebates_price'
            ]);
        });
        return $this->success($orders);


    }

    /**
     * 当前发车单详情
     *
     * @param $dtv_id
     * @return array|\WeiHeng\Responses\Apiv1Response
     */
    public function nowDispatchVoucherDetail()
    {
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
        $dtv = delivery_auth()->user()->dispatchTruck()->with($with)->whereNull('back_time')->first();
        if (!$dtv) {
            return $this->error('没有找到数据');
        }
        $dtv->addHidden(['returnOrders']);
        $dtv->setAppends(['status_name']);
        foreach ($dtv->orders as $order) {
            $order->addHidden(['orderGoods', 'salesmanVisitOrder']);
            $order->shippingAddress->setAppends(['address_name']);
            $order->shippingAddress->addHidden(['address']);
        };
        $dtv->order_goods_statis = DispatchTruckService::goodsStatistical($dtv->orders);
        $dtv->return_order_goods_statis = DispatchTruckService::returnOrderGoodsStatistical($dtv->returnOrders ?? []);
        return $this->success($dtv->toArray());
    }

    /**
     * 配送历史
     *
     * @param \App\Http\Requests\Api\v1\DeliveryRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function historyOrders(DeliveryRequest $request)
    {
        $start_at = (new Carbon($request->input('start_at')))->startOfDay();
        $end_at = (new Carbon($request->input('end_at')))->endOfDay();
        $idName = $request->input('id_name', null);
        $dtv = delivery_auth()->user()->dispatchTruck;
        $orders = Order::useful()
            ->whereNotNull('delivery_finished_at')
            ->whereIn('dispatch_truck_id', $dtv->pluck('id'))
            ->ofOrderIdName($idName)
            ->whereBetween('delivery_finished_at', [$start_at, $end_at])
            ->with('user.shop', 'shippingAddress.address')->orderBy('delivery_finished_at', 'DESC')->get();

        $historyOrder = array();
        $j = 0;
        foreach ($orders as $order) {
            $date = (new Carbon($order['delivery_finished_at']))->toDateString();
            $key = array_search($date, array_column($historyOrder, 'date'));
            $order->user && $order->user->setVisible(['shop']);
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
                'can_change_price',
                'user_shop_name',
                'after_rebates_price'
            ]);
            if ($key === false) {
                $historyOrder[$j]['date'] = $date;
                $historyOrder[$j]['data'][] = $order;
                $j++;
            } else {
                $historyOrder[$key]['data'][] = $order;
            }
        }

        return $this->success(['historyOrder' => $historyOrder]);
    }

    /**
     * 配送订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function detail(Request $request)
    {
        $order = Order::useful()->with('user.shop',
            'shippingAddress.address',
            'goods.images.image',
            'applyPromo.promo', 'gifts')->find($request->input('order_id'));
        if (is_null($order)) {
            return $this->error('订单已失效');
        }
        if (Gate::forUser(delivery_auth()->user())->denies('validate-order', $order)) {
            return $this->error('没有权限!');
        }

        $goods = (new OrderService)->explodeOrderGoods($order);
        $order->orderGoods = $goods['orderGoods'];
        $order->mortgageGoods = $goods['mortgageGoods'];
        $order->promo = is_null($order->applyPromo) ? 0 : $order->applyPromo->promo->load(['condition', 'rebate']);
        $order->is_pay = $order->pay_status == 1 ? 1 : 0;
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
            'can_change_price',
            'user_shop_name',
            'after_rebates_price',
            'invalid_reason'
        ]);
        $order->goods->each(function ($goods) {
            $goods->addHidden(['introduce', 'images_url', 'pieces']);
        });
        $order->addHidden(['applyPromo']);
        return $this->success($order);

    }

    /**
     * 处理完成配送
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function dealDelivery(Request $request)
    {
        $orderId = $request->input('order_id');
        if (empty($orderId)) {
            return $this->error('订单号不能为空');
        }

        $order = Order::useful()->noInvalid()->find($orderId);

        if (empty($order)) {
            return $this->error('该订单已失效');
        }

        if (!empty($order['delivery_finished_at'])) {
            return $this->error('配送已完成');
        }

        $nowTime = Carbon::now();
        $order->delivery_finished_at = $nowTime;
        if ($order->save()) {

            return $this->success('完成配送');
        }
        return $this->error('操作失败');
    }

    /**
     * 退出登陆
     *
     */
    public function logout()
    {
        delivery_auth()->logout();
        return $this->success();
    }

    /**
     * 修改订单
     *
     * @param \App\Http\Requests\Api\v1\UpdateOrderRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function updateOrder(UpdateOrderRequest $request)
    {
        $deliveryId = delivery_auth()->id();
        //判断该订单是否存在
        $order = Order::useful()->noInvalid()->find(intval($request->input('order_id')));

        if (!$order || !$order->can_change_price) {
            return $this->error('订单不存在或不能修改');
        }
        $attributes = $request->all();

        $orderService = new OrderService;
        $flag = $orderService->changeOrder($order, $attributes, $deliveryId, 'deliveryMan');
        return $flag ? $this->success('修改成功') : $this->error($orderService->getError());
    }

    /**
     * 订单统计
     *
     * @param \App\Http\Requests $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function statisticalDelivery(Request $request)
    {
        $search = $request->all();
        $user = delivery_auth()->user();
        $delivery = Order::useful()->noInvalid()
            ->whereNotNull('delivery_finished_at')
            ->whereIn('dispatch_truck_id', $user->dispatchTruck->pluck('id'))
            ->ofDeliverySearch($search)
            ->with(
                'systemTradeInfo',
                'deliveryMan',
                'coupon',
                'orderGoods.goods'
            )->get();
        $deliveryNum = array();
        foreach ($delivery as $order) {
            $num = $order->dispatchTruck->deliveryMans->lists('name')->count();
            array_search($num, $deliveryNum) === false ? array_push($deliveryNum, $num) : '';
        };
        if (!empty($search['num'])) {
            $delivery = $delivery->filter(function ($item) use ($search) {
                return $item->dispatchTruck->deliveryMans->lists('name')->count() == $search['num'];
            });
        }
        $data = (new DeliveryService)->format($delivery, $user);
        return $this->success(['deliveryNum' => $deliveryNum, 'data' => $data]);
    }

    /**
     * 删除订单商品
     *
     * @param $orderGoodsId 订单商品表ID
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function orderGoodsDelete($orderGoodsId)
    {
        $orderGoods = OrderGoods::with('order')->find($orderGoodsId);

        if (is_null($orderGoods) || $orderGoods->order->shop_id != delivery_auth()->user()->shop_id) {
            return $this->error('订单商品不存在');
        }
        $order = $orderGoods->order;
        if ($order->dispatch_status > cons('dispatch_truck.status.delivering')) {
            return $this->error('不能操作');
        }
        $result = DB::transaction(function () use ($orderGoods, $order) {
            $orderGoodsPrice = $orderGoods->total_price;
            $orderGoods->delete();
            if ($order->orderGoods->count() == 0) {
                //订单商品删完了标记为作废
                $order->fill(['status' => cons('order.status.invalid'), 'price' => 0])->save();
            } elseif ($orderGoodsPrice > 0) {
                $order->decrement('price', $orderGoodsPrice);
            }

            $content = '订单商品id:' . $orderGoods->goods_id . '，已被删除';
            (new OrderService())->addOrderChangeRecord($order, $content, delivery_auth()->id(), 'deliveryMan');
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

            DispatchTruckReturnOrder::create([
                'order_id' => $order->id,
                'dispatch_truck_id' => $order->dispatch_truck_id,
                'num' => $orderGoods->num,
                'pieces' => $orderGoods->pieces,
                'goods_id' => $orderGoods->goods_id
            ]);
            return 'success';
        });
        return $result == 'success' ? $this->success('删除订单商品成功') : $this->error('删除订单商品时出现问题');
    }

    /**
     * 修改密码
     *
     * @param  \App\Http\Requests\Api\v1\UpdatePasswordRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function modifyPassword(UpdatePasswordRequest $request)
    {
        $attributes = $request->all();

        $user = delivery_auth()->user();

        if (md5($attributes['old_password']) == $user->password) {
            if ($user->fill(['password' => $attributes['password']])->save()) {
                return $this->success('修改密码成功');
            }
            return $this->error('修改密码时遇到错误');
        }
        return $this->error('原密码错误');
    }

    /**
     * 检查最新版本
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function latestVersion()
    {
        return $this->success([
            'record' => VersionRecord::where('type', cons('push_device.delivery'))->orderBy('id', 'DESC')->first(),
            'download_url' => (new RedisService())->get('app-link:delivery'),
        ]);
    }

    /**
     * 作废订单
     *
     * @param \Illuminate\Http\Request $request
     * @param $orderId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function cancelOrder(Request $request, $orderId)
    {
        $order = Order::find($orderId);
        $reason = $request->input('reason');
        if (!$order) {
            return $this->error('订单不存在');
        }
        if (!$reason) {
            return $this->error('作废原因不能为空');
        }
        if (!$order->can_invalid || $order->dispatch_status == cons('dispatch_truck.status.backed') || $order->shop_id != delivery_auth()->user()->shop_id) {
            return $this->error('订单不能作废');
        }
        try {
            DB::beginTransaction();
            if ($order->fill(['status' => cons('order.status.invalid')])->save()) {
                $order->orderReason()->create(['type' => 1, 'reason' => $reason]);
                $orderService = new OrderService();
                // 返回优惠券
                $orderService->backCoupon($order);
                //返还陈列费
                if ($order->salesmanVisitOrder) {
                    $orderService->backDisplayFee($order->salesmanVisitOrder);
                }
                foreach ($order->orderGoods as $orderGoods) {
                    (new InventoryService())->clearOut($orderGoods);
                }
                $orderService->addOperateRecord($order, delivery_auth()->id(), delivery_auth()->user()->name, '配送员',
                    '作废订单');
            }
            DB::commit();
            return $this->success('订单作废成功');
        } catch (\Exception $e) {
            DB::rollback();
            info($e->getMessage() . '----' . $e->getFile() . '---' . $e->getLine());
            return $this->error('作废订单时出现问题');
        }
    }

}