<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 17:57
 */

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    private $userId;

    public function __construct()
    {
        $this->userId = auth()->user()->id;
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
     * 卖家查询订单列表
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getListOfSell()
    {
        $orders = Order::bySellerId($this->userId)->with('user', 'goods')->orderBy('id',
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
     * 买家查询待确认订单
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getNonSureOfBuy()
    {
        $orders = Order::ofBuy($this->userId)->nonSure()->paginate()->toArray();

        return $this->success($orders);
    }

    /**
     * 卖家查询待确定订单
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getNonSureOfSell()
    {
        $redis = Redis::connection();
        if ($redis->exists('push:seller:' . $this->userId)) {
            $redis->del('push:seller' . $this->userId);
        }

        $orders = Order::ofSell($this->userId)->nonSure()->paginate()->toArray();

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
     * 买家获取订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getDetailOfBuy(Request $request)
    {
        $id = $request->input('order_id');
        $detail = Order::where('user_id', $this->userId)->with('shippingAddress', 'goods', 'goods.images',
            'deliveryMan', 'shippingAddress.address')->find($id);

        return $detail ? $this->success($detail->toArray()) : $this->error('订单不存在');
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
     * 确认订单消息
     *
     * @param \Illuminate\Http\Request $request
     * @return $this|\Illuminate\View\View
     */
    public function postConfirmOrder(Request $request)
    {
        $attributes = $request->all();

        $orderGoodsNum = [];  //存放商品的购买数量  商品id => 商品数量
        foreach ($attributes['goods_id'] as $goodsId) {
            if ($attributes['num'][$goodsId] > 0) {
                $orderGoodsNum[$goodsId] = $attributes['num'][$goodsId];
            }
        }

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
     * 确认订单页
     *
     * @return \Illuminate\View\View
     */
    public function getConfirmOrder()
    {
        $carts = auth()->user()->carts()->where('status', 1)->with('goods')->get();
        $shops = (new CartService($carts))->formatCarts();
        //收货地址
        $shippingAddress = auth()->user()->shippingAddress()->with('address')->get();

        return $this->success(['shops' => $shops, 'shippingAddress' => $shippingAddress]);
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

    /**
     * 批量取消订单确认状态，在线支付：确认但未付款，可取消；货到付款：确认但未发货，可取消
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putCancelSure(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $status = Order::where(function ($query) {
            $query->wherehas('shop.user', function ($query) {
                $query->where('id', $this->userId);
            })->orWhere('user_id', $this->userId);
        })->whereIn('id', $orderIds)->where('pay_status',
            cons('order.pay_status.non_payment'))->nonCancel()->nonSend()->update([
            'is_cancel' => cons('order.is_cancel.on'),
            'cancel_by' => $this->userId,
            'cancel_at' => Carbon::now()
        ]);

        return $status ? $this->success('操作成功') : $this->error('操作失败');
    }

    /**
     * 买家批量确认订单完成,仅针对在线支付订单
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putBatchFinish(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $status = Order::where('user_id', $this->userId)->whereIn('id', $orderIds)->where('pay_status',
            cons('order.pay_status.payment_success'))->where('status', cons('order.status.send'))->nonCancel()->update([
            'status' => cons('order.status.finished'),
            'finished_at' => Carbon::now()
        ]);

        return $status ? $this->success('操作成功') : $this->error('操作失败');
    }
}