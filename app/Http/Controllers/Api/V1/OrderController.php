<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 17:57
 */

namespace App\Http\Controllers\Api\V1;

use App\Services\CartService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
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

        dd($shippingAddress->toArray());
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

}