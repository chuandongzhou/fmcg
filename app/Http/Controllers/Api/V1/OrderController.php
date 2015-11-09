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
use App\Models\SystemTradeInfo;
use App\Models\User;
use App\Services\CartService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    private $userId;

    public function __construct()
    {
        $this->userId = auth()->id();
    }

    /**
     * 买家查询订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getListOfBuy()
    {
        $orders = Order::where('user_id', $this->userId)->with('shop.user', 'goods')->orderBy('id',
            'desc')->paginate()->toArray();

        return $this->success($orders);
    }

    /**
     * 查询待付款订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getNonPayment()
    {
        $orders = Order::ofBuy($this->userId)->nonPayment()->paginate()->toArray();

        return $this->success($orders);
    }


    /**
     * 获取待收货信息--买家操作
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getNonArrived()
    {
        $orders = Order::ofBuy($this->userId)->nonArrived()->paginate()->toArray();

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
        $detail = Order::where('user_id', $this->userId)->with('shippingAddress', 'goods', 'goods.images',
            'deliveryMan', 'shippingAddress.address')->find($request->input('order_id'));

        return $detail ? $this->success($detail) : $this->error('订单不存在');
    }

    /**
     * 卖家查询订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getListOfSell()
    {
        $orders = Order::bySellerId($this->userId)->with('user', 'goods')->orderBy('id', 'desc')->where('is_cancel',
            cons('order.is_cancel.off'))->paginate()->toArray();

        return $this->success($orders);
    }

    /**
     * 获取待发货订单列表--卖家操作
     *
     * @return mixed
     */
    public function getNonSend()
    {
        $orders = Order::ofSell($this->userId)->nonSend()->paginate()->toArray();

        return $this->success($orders);
    }

    /**
     * 获取待收款订单列表,针对货到付款
     *
     * @return mixed
     */
    public function getPendingCollection()
    {
        $orders = Order::ofSell($this->userId)->getPayment()->paginate()->toArray();

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
        $id = $request->input('order_id');
        $detail = Order::bySellerId($this->userId)->with('shippingAddress', 'user', 'shop.user', 'goods',
            'shippingAddress.address')->find($id);

        return $detail ? $this->success($detail->toArray()) : $this->error('订单不存在');
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
        $orders = Order::where(function ($query) {
            $query->wherehas('shop.user', function ($query) {
                $query->where('id', $this->userId);
            })->orWhere('user_id', $this->userId);
        })->where('status', cons('order.status.non_send'))->where('pay_status',
            cons('order.pay_status.non_payment'))->whereIn('id', $orderIds)->nonCancel()->get();
        $redis = Redis::connection();
        $orders->each(function ($model) use ($redis) {
            $model->update([
                'is_cancel' => cons('order.is_cancel.on'),
                'cancel_by' => $this->userId,
                'cancel_at' => Carbon::now()
            ]);
            //推送通知
            if ($model->user_id == $this->userId) {
                $redisKey = 'push:seller:' . $model->shop->user->id;
                $msg = 'buyer';
            } else {
                $redisKey = 'push:user:' . $model->user_id;
                $msg = 'seller';
            }

            if (!$redis->exists($redisKey)) {
                $redis->set($redisKey, '订单:' . $model->id . cons()->lang('push_msg.cancel_by_' . $msg));
                $redis->expire($redisKey, cons('push_time.msg_life'));
            }
        });

        return $this->success('取消成功');
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
        $orders = Order::where('user_id', $this->userId)->where('pay_type',
            cons('pay_type.online'))->where('pay_status', cons('order.pay_status.payment_success'))->where('status',
            cons('order.status.send'))->whereIn('id', $orderIds)->nonCancel()->get();
        $redis = Redis::connection();

        $orders->each(function ($model) use ($redis) {

            DB::transaction(function () use ($model, $redis) {
                $model->update([
                    'status' => cons('order.status.finished'),
                    'finished_at' => Carbon::now()
                ]);
                //更新systemTradeInfo完成状态
                $tradeModel = $model->systemTradeInfo;
                $tradeModel->update([
                    'is_finished' => cons('trade.is_finished.yes'),
                    'finished_at' => Carbon::now()
                ]);
                //更新用户balance
                $shopOwner = $model->shop->user;
                $shopOwner->balance += $tradeModel->amount;
                $shopOwner->save();
                //通知卖家
                $redisKey = 'push:seller:' . $shopOwner->id;
                if (!$redis->exists($redisKey)) {
                    $redis->set($redisKey, '您的订单:' . $model->id . ',' . cons()->lang('push_msg.finished'));
                    $redis->expire($redisKey, cons('push_time.msg_life'));
                }
            });
        });

        return $this->success('操作成功');
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
        Order::bySellerId($this->userId)->whereIn('id', $orderIds)->where('status',
            cons('order.status.send'))->where('pay_type', cons('pay_type.cod'))->nonCancel()->update([
            'pay_status' => cons('order.pay_status.payment_success'),
            'paid_at' => Carbon::now(),
            'status' => cons('order.status.finished'),
            'finished_at' => Carbon::now()
        ]);

        return $this->success('操作成功');
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
        if (!DeliveryMan::find($deliveryManId)) {
            return $this->error('操作失败');
        }
        $orderModel = Order::bySellerId($this->userId)->where(function ($query) {
            $query->where(function ($query) {
                $query->where('pay_type', cons('pay_type.online'))->where('pay_status',
                    cons('order.pay_status.payment_success'))->where('status', cons('order.status.non_send'));
            })->orwhere(function ($query) {
                $query->where('pay_type', cons('pay_type.cod'))->where('status', cons('order.status.non_send'));
            });
        })->whereIn('id', $orderIds)->nonCancel()->get();
        //通知买家订单已发货
        $redis = Redis::connection();
        $orderModel->each(function ($model) use ($redis, $deliveryManId) {
            $redisKey = 'push:user:' . $model->user_id;
            if (!$redis->exists($redisKey)) {
                $redis->set($redisKey, '您的订单' . $model->id . ',' . cons()->lang('push_msg.send'));
                $redis->expire($redisKey, cons('push_time.msg_life'));
            }
            $model->update([
                'delivery_man_id' => $deliveryManId,
                'status' => cons('order.status.send'),
                'send_at' => Carbon::now()
            ]);
        });

        return $this->success('发货成功');

    }

    /**
     * 卖家订单统计功能
     *
     */
    public function getStatistics()
    {
        $redis = Redis::connection();
        $key = 'statistics:' . $this->userId;
        if ($redis->exists($key)) {
            return $this->success(json_decode($redis->get($key), true));
        }
        //当日新增订单数,完成订单数,所有累计订单数,累计完成订单数;7日对应
        $today = Carbon::today();
        $builder = Order::bySellerId($this->userId)->nonCancel();

        for ($i = 6; $i >= 0; -- $i) {
            $start = $today->copy()->addDay(- $i);
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
        $redis->set($key, json_encode($statistics));
        $redis->expire($key, 60);

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

        $carts = auth()->user()->carts()->where('status', 1)->with('goods')->get();

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

        $data = $request->input('shop');

        $payTypes = cons('pay_type');

        $onlinePaymentOrder = [];   //  保存在线支付的订单

        foreach ($shops as $shop) {
            $payType = array_get($payTypes, $data[$shop->id]['pay_type'], head($payTypes));
            $orderData = [
                'user_id' => auth()->user()->id,
                'shop_id' => $shop->id,
                'price' => $shop->sum_price,
                'pay_type' => $payType,
                //TODO: 需要验证收货地址是否合法
                'shipping_address_id' => $data[$shop->id]['shipping_address_id'],
                'remark' => $data[$shop->id]['remark'] ? $data[$shop->id]['remark'] : ''
            ];
            $order = Order::create($orderData);
            if ($order->exists) {
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
                    }
                    // 删除购物车
                    auth()->user()->carts()->where('status', 1)->delete();
                } else {
                    $order->delete();

                    return $this->error('提交订单失败');
                }

            } else {
                return $this->error('提交订单失败');
            }
        }

        return $this->success('提交订单成功');
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
        $order = Order::bySellerId(auth()->user()->id)->find(intval($request->input('order_id')));
        if (!$order) {
            $this->error('订单不存在,请确认后再试');
        }
        //判断输入的价格是否合法
        $price = intval($request->input('price'));
        if ($price < 0) {
            $this->error('输入单价不合法,请确认后再试');
        }
        //判断待修改物品是否属于该订单
        $pivot = OrderGoods::find(intval($request->input('pivot_id')));
        if (!$pivot || $pivot->order_id != $order->id) {
            $this->error('操作失败');
        }
        $oldTotalPrice = $pivot->total_price;
        $newTotalPrice = $pivot->num * $price;
        $pivot->update(['price' => $price, 'total_price' => $newTotalPrice]);
        $order->update(['price' => $order->price - $oldTotalPrice + $newTotalPrice]);
        //通知买家订单价格发生了变化
        $redis = Redis::connection();
        $redisKey = 'push:user:' . $order->user_id;
        if (!$redis->exists($redisKey)) {
            $redis->set($redisKey, '您的订单' . $order->id . ',' . cons()->lang('push_msg.price_changed'));
            $redis->expire($redisKey, cons('push_time.msg_life'));
        }

        return $this->success('修改成功');
    }
}