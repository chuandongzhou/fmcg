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
use App\Services\CartService;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        //清除redis中买家提醒消息
        $redis = Redis::connection();
        if ($redis->exists('push:user:' . $this->userId)) {
            $redis->del('push:user:' . $this->userId);
        }
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

        return $detail ? $this->success($detail->toArray()) : $this->error('订单不存在');
    }

    /**
     * 卖家查询订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getListOfSell()
    {
        $orders = Order::exceptNonPayment()->bySellerId($this->userId)->with('user', 'goods')->orderBy('id',
            'desc')->paginate()->toArray();

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
    {dd($this->success());
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
            $redisKey = 'push:seller:' . $model->shop->user->id;

            $model->update([
                'status' => cons('order.status.finished'),
                'finished_at' => Carbon::now()
            ]);
            if (!$redis->exists($redisKey)) {
                $redis->set($redisKey, '您的订单:' . $model->id . ',', cons()->lang('push_msg.finished'));
                $redis->expire($redisKey, cons('push_time.msg_life'));
            }
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
                    //TODO: 跳转页面后期修改
                    $order->delete();

                    return $this->error('提交订单失败');
                }

            } else {
                //跳转页面后期修改
                return $this->error('提交订单失败');
            }
        }

        return $this->success('提交订单成功');

        // TODO: 跳至支付页面
        // dd($onlinePaymentOrder);

    }

}