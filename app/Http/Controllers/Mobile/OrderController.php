<?php

namespace App\Http\Controllers\Mobile;


use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * 订货单
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\WeiHeng\Responses\IndexResponse
     */
    public function index(Request $request)
    {
        $orders = Order::OfBuy(auth()->id())->useful()->with([
            'shop.user',
            'user',
            'coupon',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->paginate();
        if ($request->ajax()) {
            return $this->success($this->_buildHtml($orders));
        }
        $pageName = '进货订单';
        return view('mobile.order.index', compact('orders', 'pageName'));
    }

    /**
     * 未发货
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\WeiHeng\Responses\IndexResponse
     */
    public function unSent(Request $request)
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
        if ($request->ajax()) {
            return $this->success($this->_buildHtml($orders));
        }
        $pageName = '待发货';
        return view('mobile.order.index', compact('orders', 'pageName'));
    }

    /**
     * 待付款
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\WeiHeng\Responses\IndexResponse
     */
    public function nonPayment(Request $request)
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

        if ($request->ajax()) {
            return $this->success($this->_buildHtml($orders));
        }
        $pageName = '待付款';
        return view('mobile.order.index', compact('orders', 'pageName'));
    }

    /**
     * 待确认
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\WeiHeng\Responses\IndexResponse
     */
    public function waitConfirm(Request $request)
    {
        $orders = Order::ofBuy(auth()->id())->waitConfirm()->with([
            'shop.user',
            'user',
            'coupon',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->paginate();
        if ($request->ajax()) {
            return $this->success($this->_buildHtml($orders));
        }
        $pageName = '待确认';
        return view('mobile.order.index', compact('orders', 'pageName'));
    }

    /**
     * 待收货
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\WeiHeng\Responses\IndexResponse
     */
    public function nonArrived(Request $request)
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

        if ($request->ajax()) {
            return $this->success($this->_buildHtml($orders));
        }
        $pageName = '待收货';
        return view('mobile.order.index', compact('orders', 'pageName'));
    }

    /**
     * 订单详情
     *
     * @param $orderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\WeiHeng\Responses\IndexResponse
     */
    public function detail($orderId)
    {
        $order = Order::where('user_id', auth()->id())->find($orderId);
        if (!$order) {
            return $this->error('订单不存在');
        }
        $goods = (new OrderService())->explodeOrderGoods($order);
        return view('mobile.order.detail', [
            'order' => $order,
            'mortgageGoods' => $goods['mortgageGoods'],
            'orderGoods' => $goods['orderGoods'],
        ]);
    }


    /**
     * 订单确认页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function confirmOrder()
    {
        $user = auth()->user();
        $carts = $user->carts()->where('status', 1)->with('goods')->get();
        if ($carts->isEmpty()) {
            return redirect('cart')->with('message', '购物车为空');
        }
        $shops = (new CartService($carts))->formatCarts(null, true);
        //收货地址
        $shippingAddress = $user->shippingAddress()->with('address')->get();
        $defaultAddress = $shippingAddress->where('is_default', 1)->first();

        return view('mobile.order.confirm-order', compact('shops', 'shippingAddress', 'defaultAddress'));
    }

    /**
     * 订单提交成功
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function successOrder()
    {
        return view('mobile.order.success-order');
    }


    /**
     * 订单分页html
     *
     * @param $orders
     * @return array
     */
    private function _buildHtml($orders)
    {
        $orderHtml = '';
        foreach ($orders as $order) {
            $orderHtml .= '<div class="row all-orders-list">' .
                '        <div class="col-xs-12 list-item">' .
                '   <div class="item order-title-panel">' .
                '   <div class="pull-left">订货单 <span class="color-black">' . $order->id . ($order->type ? '(自主)' : '') . ' </span></div>' .
                '    <div class="pull-right">' . $order->created_at . '</div>' .
                '   </div>' .
                '   </div>' .
                '   <div class="col-xs-12 list-item">' .
                '   <div class="item">' .
                '   <a class="pull-left shop-name">' . $order->shop_name . ' <i class="iconfont icon-jiantouyoujiantou"></i></a>' .
                '    <div class="pull-right"><spanclass="red">' . $order->status_name . ($order->pay_type == cons('pay_type . pick_up') ? '(自提)' : '') . '</span>' .
                '    </div>' .
                '    </div>' .
                '    </div>' .
                '    <div class="col-xs-12 list-item">' .
                '    <div class="row bordered">';
            foreach ($order->goods as $goods) {
                $orderHtml .= '    <a href="' . url('goods/' . $goods->id) . '">' .
                    '<div class="col-xs-12 item">' .
                    '<img src="' . $goods->image_url . '" class="pull-left commodity-img"/>' .
                    '    <div class="commodity-name pull-left">' . $goods->name . '</div>' .
                    '    <div class="right-num-panel pull-right">' .
                    '    <div>x' . $goods->pivot->num . '</div>' .
                    '    <div>¥' . $goods->pivot->price . '/' . cons()->valueLang('goods.pieces',
                        $goods->pivot->pieces) . '</div>' .
                    '    </div>' .
                    '    </div>' .
                    '    </a>';
            }
            $orderHtml .= '</div>' .
                '    </div>' .
                '    <div class="col-xs-12 list-item">' .
                '    <div class="item">' .
                '    <div class="pull-left">共' . $order->goods->count() . '件商品</div>' .
                '    <div class="pull-right text-right">' .
                '    <div>总额：<span class="red">¥' . $order->after_rebates_price . '</span></div>' .
                '    <div>';
            if ($order->can_refund) {
                $orderHtml .= '    <button class="btn btn-danger refund" data-target="#refund" data-toggle="modal" data-url="' . url('api/v1/pay/refund/' . $order->id) . '">退款 </button>';
            } elseif ($order->can_cancel) {
                $orderHtml .= '    <button type="button" class="btn btn-primary mobile-ajax" data-url="' . url('api/v1/order/cancel-sure') . '" data-method="put" data-danger="真的要取消该订单吗？" data-data=\'{"order_id":' . $order->id . ' }\'>取消 </button>';
            }
            if ($order->can_payment) {
                $orderHtml .= '<button type="button" data-target="#payModal" data-toggle="modal" class="btn btn-success" data-id="' . $order->id . '" data-price="' . $order->after_rebates_price . '">去付款  </button>';
            } elseif ($order->can_confirm_arrived) {
                $orderHtml .= '<button type="button" class="btn btn-danger mobile-ajax" data-url="' . url('api/v1/order/batch-finish-of-buy') . '" data-method="put" data-data=\'{"order_id":' . $order->id . ' }\'>确认收货 </button>';
            }
            $orderHtml .= '</div>' .
                '</div>' .
                '</div>' .
                '    </div>' .
                '   </div>';

        }
        return ['count' => $orders->count(), 'html' => $orderHtml];
    }

}
