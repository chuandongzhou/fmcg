<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 17:57
 */

namespace App\Http\Controllers\Api\V1;

use App\Models\DeliveryMan;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\CartService;
use App\Services\GoodsService;
use App\Services\RedisService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * 买家查询订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getListOfBuy()
    {
        $orders = Order::where('user_id', $this->user->id)->with('shop.user', 'goods.images')->orderBy('id',
            'desc')->paginate();

        return $this->success($orders);
    }

    /**
     * 查询待付款订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getNonPayment()
    {
        $orders = Order::ofBuy($this->user->id)->nonPayment()->paginate();

        return $this->success($orders);
    }


    /**
     * 获取待收货信息--买家操作
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getNonArrived()
    {
        $orders = Order::ofBuy($this->user->id)->nonArrived()->paginate();

        return $this->success($orders);
    }


    /**
     * 买家获取订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getDetailOfBuy(Request $request)
    {
        $order = Order::where('user_id', $this->user->id)->with('goods.images', 'deliveryMan',
            'shippingAddress.address')->find($request->input('order_id'));

        $order->trade_no = $this->_getTradeNoByOrder($order);

        return $order ? $this->success($order) : $this->error('订单不存在');
    }

    /**
     * 卖家查询订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getListOfSell()
    {
        $orders = Order::bySellerId($this->user->id)->with('user.shop', 'goods.images.image')->orderBy('id',
            'desc')->where('is_cancel', cons('order.is_cancel.off'))->paginate();


        $orders->each(function ($order) {
            $order->goods->each(function ($goods) {
                $goods->addHidden(['introduce', 'images_url']);
            });
            $order->user->setVisible(['id', 'shop']);
            $order->user->shop->setVisible(['name'])->setAppends([]);
        });
        return $this->success($orders);
    }

    /**
     * 获取待发货订单列表--卖家操作
     *
     * @return mixed
     */
    public function getNonSend()
    {
        $orders = Order::ofSell($this->user->id)->nonSend()->paginate();

        return $this->success($orders);
    }

    /**
     * 获取待收款订单列表,针对货到付款
     *
     * @return mixed
     */
    public function getPendingCollection()
    {
        $orders = Order::ofSell($this->user->id)->getPayment()->paginate();

        return $this->success($orders);
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
        $order = Order::bySellerId($this->user->id)->with('user', 'DeliveryMan', 'shop.user', 'goods.images.image',
            'shippingAddress.address')->find($orderId);

        //过滤字段
        $order->shop->setAppends([]);
        $order->goods->each(function ($goods) {
            $goods->addHidden(['introduce', 'images_url']);
        });

        $order->trade_no = $this->_getTradeNoByOrder($order);

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

        $orders = Order::with('shop.user')->whereIn('id', $orderIds)->nonCancel()->get();

        $failOrderIds = [];
        foreach ($orders as $order) {
            if (($order->status != cons('order.status.non_send') || $order->pay_status != cons('order.pay_status.non_payment'))
                || !($order->shop->user->id == $this->user->id || $order->user_id == $this->user->id)
            ) {
                $failOrderIds[] = $order->id;
                continue;
            }
            $order->fill([
                'is_cancel' => cons('order.is_cancel.on'),
                'cancel_by' => $this->user->id,
                'cancel_at' => Carbon::now()
            ])->save();
            //推送通知
            if ($order->user_id == $this->user->id) {
                $redisKey = 'push:seller:' . $order->shop->user->id;
                $msg = 'buyer';
            } else {
                $redisKey = 'push:user:' . $order->user_id;
                $msg = 'seller';
            }

            $redisVal = '订单:' . $order->id . cons()->lang('push_msg.cancel_by_' . $msg);
            RedisService::setRedis($redisKey, $redisVal);
        }

        return $this->success(['failOrderIds' => $failOrderIds]);
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
        $orders = Order::where('user_id', $this->user->id)->whereIn('id', $orderIds)->nonCancel()->get();

        if ($orders->isEmpty()) {
            return $this->error('确认收货失败');
        }

        $failIds = [];
        foreach ($orders as $order) {
            if ($order->pay_type != cons('pay_type.online') || $order->pay_status != cons('order.pay_status.payment_success') || $order->status != cons('order.status.send')) {
                if (count($orders) == 1) {
                    return $this->error('确认收货失败');
                }
                $failIds[] = $order->id;
                continue;
            }
            if ($order->fill(['status' => cons('order.status.finished'), 'finished_at' => Carbon::now()])->save()) {
                //更新systemTradeInfo完成状态
                $tradeModel = $order->systemTradeInfo;
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

                RedisService::setRedis($redisKey, $redisVal);
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
        $orders = Order::bySellerId($this->user->id)->whereIn('id', $orderIds)->nonCancel()->get();
        if ($orders->isEmpty()) {
            return $this->error('确认收款失败');
        }

        $failIds = []; //失败订单id
        $nowTime = Carbon::now();
        foreach ($orders as $order) {
            if ($order->status != cons('order.status.send') || $order->pay_type != cons('pay_type.cod')) {
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
        if (!DeliveryMan::where('shop_id', $this->user->shop()->pluck('id'))->find($deliveryManId)) {
            return $this->error('操作失败');
        }
        $orders = Order::bySellerId($this->user->id)->whereIn('id', $orderIds)->nonCancel()->get();

        if ($orders->isEmpty()) {
            return $this->error('操作失败');
        }

        //通知买家订单已发货
        $failIds = [];
        foreach ($orders as $order) {
            if ($order->pay_type == cons('pay_type.online') && !($order->pay_status == cons('order.pay_status.payment_success') && $order->status == cons('order.status.non_send'))) {
                if (count($orders) == 1) {
                    return $this->error('操作失败');
                }
                $failIds[] = $order->id;
                continue;
            } elseif ($order->pay_type == cons('pay_type.cod') && $order->status != cons('order.status.non_send')) {
                if (count($orders) == 1) {
                    return $this->error('操作失败');
                }
                $failIds[] = $order->id;
                continue;
            }

            $redisKey = 'push:user:' . $order->user_id;
            $redisVal = '您的订单' . $order->id . ',' . cons()->lang('push_msg.send');
            RedisService::setRedis($redisKey, $redisVal);

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
        $redis = Redis::connection();
        $key = 'statistics:' . $this->user->id;
        if ($redis->exists($key)) {
            return $this->success(json_decode($redis->get($key), true));
        }

        //当日新增订单数,完成订单数,所有累计订单数,累计完成订单数;7日对应
        $today = Carbon::today();
        $builder = Order::bySellerId($this->user->id)->nonCancel();

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

        RedisService::setRedis($key, json_encode($statistics), 60);
        return $this->success($statistics);

    }

    /**
     * 获取确认订单页
     *
     * @return \Illuminate\View\View
     */
    public function getConfirmOrder()
    {
        $carts = auth()->user()->carts()->where('status', 1)->with('goods')->get();
        $shops = (new CartService($carts))->formatCarts();
        //收货地址
        $shippingAddress = auth()->user()->shippingAddress()->with('address')->get();
        $payType = cons()->valueLang('pay_type');//支付方式
        $codPayType = cons()->valueLang('cod_pay_type');//货到付款支付方式
        return $this->success([
            'shops' => $shops,
            'shipping_address' => $shippingAddress,
            'pay_type' => $payType,
            'cod_pay_type' => $codPayType
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
        /*   $goodsId = $request->input('goods_id');

           $orderGoodsNum = array_where($orderGoodsNum, function($key, $value) use($goodsId)
           {
               return in_array($key , $goodsId);
           });*/

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
        $user = auth()->user();

        $carts = $user->carts()->where('status', 1)->with('goods')->get();
        if (empty($carts[0])) {
            return $this->error('提交订单失败');
        }
        $orderGoodsNum = [];  //存放商品的购买数量  商品id => 商品数量
        foreach ($carts as $cart) {
            $orderGoodsNum[$cart->goods_id] = $cart->num;
        }
        //验证
        $cartService = new CartService($carts);

        if (!$shops = $cartService->validateOrder($orderGoodsNum)) {
            return $this->error('提交订单失败');
        }

        $data = $request->all();

        $payTypes = cons('pay_type');
        $codPayTypes = cons('cod_pay_type');

        $onlinePaymentOrder = [];   //  保存在线支付的订单
        $payType = array_get($payTypes, $data['pay_type'], head($payTypes));

        $codPayType = $payType == $payTypes['cod'] ? array_get($codPayTypes, $data['cod_pay_type'],
            head($codPayTypes)) : 0;
        //TODO: 需要验证收货地址是否合法
        $shippingAddressId = $data['shipping_address_id'];


        $pid = 0;
        if ($shops->count() > 1) {
            $maxPid = Order::max('pid');
            $pid = $maxPid + 1;
        }

        $successOrders = [];  //保存提交成功的订单

        foreach ($shops as $shop) {
            $remark = $data['shop'][$shop->id]['remark'] ? $data['shop'][$shop->id]['remark'] : '';
            $orderData = [
                'pid' => $pid,
                'user_id' => auth()->user()->id,
                'shop_id' => $shop->id,
                'price' => $shop->sum_price,
                'pay_type' => $payType,
                'status' => cons('order.status.non_send'),
                'cod_pay_type' => $codPayType,
                'shipping_address_id' => $shippingAddressId,
                'remark' => $remark
            ];
            $order = Order::create($orderData);
            if ($order->exists) {//添加订单成功,修改orderGoods中间表信息
                $successOrders[] = $order;
                $orderGoods = [];
                foreach ($shop->cart_goods as $cartGoods) {
                    $orderGoods[] = new OrderGoods([
                        'goods_id' => $cartGoods->goods_id,
                        'price' => $cartGoods->goods->price,
                        'num' => $cartGoods->num,
                        'total_price' => $cartGoods->goods->price * $cartGoods->num,
                    ]);
                }
                if ($order->orderGoods()->saveMany($orderGoods)) {
                    if ($payType == $payTypes['online']) {
                        $onlinePaymentOrder[] = $order->id;
                    } else {
                        //货到付款订单直接通知卖家发货
                        $redisKey = 'push:seller:' . $shop->user->id;
                        $redisVal = '您的订单' . $order->id . ',' . cons()->lang('push_msg.non_send.cod');
                        RedisService::setRedis($redisKey, $redisVal);
                    }
                } else {
                    foreach ($successOrders as $successOrder) {
                        $successOrder->delete();
                    }

                    return $this->error('提交订单时遇到问题');
                }

            } else {
                return $this->error('提交订单时遇到问题');
            }
        }

        // 删除购物车
        $user->carts()->where('status', 1)->delete();
        // 增加商品销量
        GoodsService::addGoodsSalesVolume($orderGoodsNum);

        $returnArray = [
            'pay_type' => $payType,
            'type' => ""
        ];

        if ($payType == $payTypes['online']) {
            if ($pid > 0) {
                $returnArray['order_id'] = $pid;
                $returnArray['type'] = 'all';
            } else {
                $returnArray['order_id'] = $onlinePaymentOrder[0];
            }
        }

        return $this->success($returnArray);
    }

    /**
     * 修改订单价格
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putChangePrice(Request $request)
    {
        //判断该订单是否存在
        $order = Order::bySellerId($this->user->id)->find(intval($request->input('order_id')));
        if (!$order) {
            $this->error('订单不存在,请确认后再试');
        }
        //判断输入的价格是否合法
        $price = intval($request->input('price'));
        if ($price < 0) {
            $this->error('输入单价不合法,请确认后再试');
        }
        //判断待修改物品是否属于该订单
        $orderGoods = OrderGoods::find(intval($request->input('pivot_id')));
        if (!$orderGoods || $orderGoods->order_id != $order->id) {
            $this->error('操作失败');
        }
        $flag = DB::transaction(function () use ($orderGoods, $order, $price) {
            $oldTotalPrice = $orderGoods->total_price;
            $newTotalPrice = $orderGoods->num * $price;
            $orderGoods->fill(['price' => $price, 'total_price' => $newTotalPrice])->save();
            $order->fill(['price' => $order->price - $oldTotalPrice + $newTotalPrice])->save();
            //通知买家订单价格发生了变化

            $redisKey = 'push:user:' . $order->user_id;
            $redisVal = '您的订单' . $order->id . ',' . cons()->lang('push_msg.price_changed');
            RedisService::setRedis($redisKey, $redisVal);

            return true;
        });

        return $flag ? $this->success('修改成功') : $this->error('修改失败,稍后再试!');
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
}