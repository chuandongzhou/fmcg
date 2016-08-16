<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 17:57
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\v1\UpdateOrderRequest;
use App\Models\DeliveryMan;
use App\Models\Goods;
use App\Models\Order;
use App\Models\Shop;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\RedisService;
use App\Services\ShopService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class OrderController extends Controller
{
    /**
     * 买家查询订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getListOfBuy()
    {
        $orders = Order::OfBuy(auth()->id())->WithExistGoods(['shop.user', 'user', 'coupon'],
            [
                'goods.id',
                'name',
                'price_retailer',
                'price_retailer_pick_up',
                'pieces_retailer',
                'min_num_retailer',
                'specification_retailer',
                'price_wholesaler',
                'price_wholesaler_pick_up',
                'pieces_wholesaler',
                'min_num_wholesaler',
                'specification_wholesaler',
                'bar_code'
            ])->paginate();

        return $this->success($this->_hiddenOrdersAttr($orders));
    }

    /**
     * 查询待付款订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getNonPayment()
    {
        $orders = Order::ofBuy(auth()->id())->nonPayment()->paginate();

        return $this->success($this->_hiddenOrdersAttr($orders));
    }

    /**
     * 获取待收货信息--买家操作
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getNonArrived()
    {
        $orders = Order::ofBuy(auth()->id())->nonArrived()->paginate();

        return $this->success($this->_hiddenOrdersAttr($orders));
    }

    /**
     * 获取买家待确认订单
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getWaitConfirmByUser()
    {
        $orders = Order::ofBuy(auth()->id())->WaitConfirm()->paginate();
        return $this->success($this->_hiddenOrdersAttr($orders));
    }

    /**
     * 获取卖家待确认订单
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getWaitConfirmBySeller()
    {
        $orders = Order::ofSell(auth()->id())->with('user.shop', 'goods.images.image')->WaitConfirm()->paginate();
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

        $order = $this->_orderLoadData($order);

        $order->trade_no = $this->_getTradeNoByOrder($order);

        return $order ? $this->success($this->_hiddenOrderAttr($order)) : $this->error('订单不存在');
    }


    /**
     * 卖家查询订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getListOfSell()
    {
        $orders = Order::OfSell(auth()->id())->with('user.shop', 'goods', 'coupon')->orderBy('id',
            'desc')->where('is_cancel', cons('order.is_cancel.off'))->paginate();

        return $this->success($this->_hiddenOrdersAttr($orders, false));
    }

    /**
     * 获取待发货订单列表--卖家操作
     *
     * @return mixed
     */
    public function getNonSend()
    {
        $orders = Order::ofSell(auth()->id())->nonSend()->paginate();

        return $this->success($this->_hiddenOrdersAttr($orders, false));
    }

    /**
     * 获取待收款订单列表,针对货到付款
     *
     * @return mixed
     */
    public function getPendingCollection()
    {
        $orders = Order::ofSell(auth()->id())->getPayment()->paginate();

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
        $order = Order::OfSell(auth()->id())->find($orderId);

        $order = $this->_hiddenOrderAttr($this->_orderLoadData($order), false);

        $order->orderChangeRecode = $order->orderChangeRecode->reverse()->each(function ($recode) use ($order) {
            $recode->name = auth()->id() ? $order->shop->name : $order->deliveryMan->name;
        });
        $order->trade_no = $this->_getTradeNoByOrder($order);

        $order->addHidden(['order_change_recode']);

        return $order ? $this->success($order) : $this->error('订单不存在');
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

        $orders = Order::with('shop.user', 'user')->whereIn('id', $orderIds)->nonCancel()->get();

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
                $redisKey = 'push:seller:' . $order->shop->user->id;
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
        if (!$order->can_confirm || $order->shop_id != auth()->user()->shop->id) {
            return $this->error('订单不能确认');
        }
        if ($order->fill(['status' => cons('order.status.non_send'), 'confirm_at' => Carbon::now()])->save()) {
            return $this->success('订单确认成功');
        }
        return $this->error('订单确认失败，请重试');
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
        $orders = Order::where('user_id', auth()->id())->whereIn('id', $orderIds)->nonCancel()->get();

        if ($orders->isEmpty()) {
            return $this->error('请选择订单');
        }

        $failIds = [];
        foreach ($orders as $order) {
            if (!$order->can_confirm_arrived || $order->user_id != auth()->id()) {
                if ($orders->count() == 1) {
                    return $this->error('确认收货失败');
                }
                $failIds[] = $order->id;
                continue;
            }
            if (($tradeModel = $order->systemTradeInfo) && $order->fill([
                    'status' => cons('order.status.finished'),
                    'finished_at' => Carbon::now()
                ])->save()
            ) {
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
        $orders = Order::OfSell(auth()->id())->whereIn('id', $orderIds)->nonCancel()->get();
        if ($orders->isEmpty()) {
            return $this->error('确认收款失败');
        }

        $failIds = []; //失败订单id
        $nowTime = Carbon::now();
        foreach ($orders as $order) {
            if (!$order->can_confirm_collections) {
                if (count($orders) == 1) {
                    return $this->error('确认收款失败');
                }
                $failIds[] = $order->id;
                continue;
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
     * 批量修改发货状态。不区分付款状态
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putBatchSend(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $deliveryManId = intval($request->input('delivery_man_id'));
        //判断送货人员是否是该店铺的
        if (!DeliveryMan::where('shop_id', auth()->user()->shop()->pluck('id'))->find($deliveryManId)) {
            return $this->error('操作失败');
        }
        $orders = Order::OfSell(auth()->id())->whereIn('id', $orderIds)->nonCancel()->get();

        if ($orders->isEmpty()) {
            return $this->error('操作失败');
        }

        //通知买家订单已发货
        $failIds = [];
        foreach ($orders as $order) {
            if (!$order->can_send || $order->shop_id != auth()->user()->shop->id) {
                if (count($orders) == 1) {
                    return $this->error('操作失败');
                }
                $failIds[] = $order->id;
                continue;
            }

            $redisKey = 'push:user:' . $order->user_id;
            $redisVal = '您的订单' . $order->id . ',' . cons()->lang('push_msg.send');
            (new RedisService)->setRedis($redisKey, $redisVal, cons('push_time.msg_life'));

            $order->fill([
                'delivery_man_id' => $deliveryManId,
                'status' => cons('order.status.send'),
                'send_at' => Carbon::now()
            ])->save();
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
        $today = Carbon::today();
        $builder = Order::OfSell(auth()->id())->nonCancel();

        for ($i = 6; $i >= 0; --$i) {
            $start = $today->copy()->addDay(-$i);
            $end = $start->copy()->endOfDay();
            $time = [$start, $end];
            $statistics['sevenDay'][] = [
                'new' => with(clone $builder)->whereBetween('created_at', $time)->count(),
                'finish' => with(clone $builder)->whereBetween('finished_at', $time)->count()
            ];
        }
        $statistics['today'] = $statistics['sevenDay'][6];
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
     * @return $this|\Illuminate\View\View
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
            return $this->error('确认失败');
        }

        if ($confirmedGoods->update(['status' => 1])) {
            return $this->success('确认成功');
        } else {
            return $this->error('确认订单失败');
        }
    }

    /**
     * 提交订单
     *
     * @param \Illuminate\Http\Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSubmitOrder(Request $request)
    {

        $data = $request->all();

        $result = (new OrderService)->orderSubmitHandle($data);

        return $result ? $this->success($result) : $this->error('提交订单时遇到问题');
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
        $order = Order::OfSell(auth()->id())->find(intval($request->input('order_id')));
        if (!$order || !$order->can_change_price) {
            return $this->error('订单不存在或不能修改');
        }
        $attributes = $request->all();
        $flag = (new OrderService)->changeOrder($order, $attributes, auth()->id());
        return $flag ? $this->success('修改成功') : $this->error('修改失败,稍后再试!');
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

        $result = ShopService::getShopMinMoneyByShippingAddress($shops, $shippingAddress);

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
            $order->load(['goods.images', 'deliveryMan', 'shippingAddress.address', 'orderRefund']);
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
     * @param $order
     * @param bool $buyer
     * @return mixed
     */
    private function _hiddenOrderAttr($order, $buyer = true)
    {
        $order->goods->each(function ($goods) {
            $goods->addHidden(['introduce']);
        });
        if (!$buyer) {
            $order->user->setVisible(['id', 'shop', 'type']);
            $order->user->shop->setVisible(['name'])->setAppends([]);
        }

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
            'after_rebates_price'
        ]);
        $buyer && $order->shop->setAppends([]);

        return $order;
    }
}