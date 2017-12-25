<?php

namespace App\Http\Controllers\Index;


use App\Http\Requests;
use App\Services\OrderService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\DeliveryMan;

class OrderBuyController extends OrderController
{

    protected $userBalance;

    /**
     * 构造方法,限制供应商访问购买功能
     */
    public function __construct()
    {
        //parent::__construct();
        //供应商无购买商品功能
        $this->middleware('forbid:wholesaler,retailer');
        $this->userBalance = (new UserService())->getUserBalance();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        //支付方式
        $payType = cons()->valueLang('pay_type');

        //订单状态
        $orderStatus = cons()->lang('order.status');
        $payStatus = array_only(cons()->lang('order.pay_status'), ['non_payment', 'refund', 'refund_success']);

        $orderStatus = array_merge($payStatus, $orderStatus);

        $search = $request->all();

        $orders = Order::ofBuy(auth()->id())->useful()->with([
            'shop.user',
            'user',
            'coupon',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ]);

        if (is_numeric($searchContent = array_get($search, 'search_content'))) {
            $orders = $orders->where('id', $searchContent);
        } elseif ($searchContent) {
            $orders = $orders->ofSelectOptions($search)->ofShopName($searchContent);
        } else {
            $orders = $orders->ofSelectOptions($search);
        }

        return view('index.order.order-buy', [
            'pay_type' => $payType,
            'order_status' => $orderStatus,
            'data' => $this->_getOrderNum(),
            'orders' => $orders->orderBy('id', 'desc')->paginate(),
            'search' => $search,
            'userBalance' => $this->userBalance['availableBalance']
        ]);
    }

    /**
     * 待支付订单
     */
    public function getWaitPay()
    {
        $orders = Order::ofBuy(auth()->id())->with([
            'shop.user',
            'user',
            'coupon',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->nonPayment();

        return view('index.order.order-buy', [
            'data' => $this->_getOrderNum($orders->count()),
            'orders' => $orders->paginate(),
            'userBalance' => $this->userBalance['availableBalance']
        ]);
    }

    /**
     * 待收货订单
     */
    public function getWaitReceive()
    {
        $orders = Order::ofBuy(auth()->id())->with([
            'shop.user',
            'user',
            'coupon',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->nonArrived();
        return view('index.order.order-buy', [
            'data' => $this->_getOrderNum(-1, $orders->count()),
            'orders' => $orders->paginate(),
            'userBalance' => $this->userBalance['availableBalance']
        ]);
    }

    /**
     * 待确认订单
     */
    public function getWaitConfirm()
    {
        $orders = Order::ofBuy(auth()->id())->with([
            'shop.user',
            'user',
            'coupon',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->waitConfirm();
        return view('index.order.order-buy', [
            'data' => $this->_getOrderNum(-1, -1, $orders->count()),
            'orders' => $orders->paginate()
        ]);
    }

    /**
     * 获取订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function getDetail(Request $request)
    {
        $order = Order::where('user_id', auth()->id())->find($request->input('order_id'));
        if (!$order) {
            return $this->error('订单不存在');
        }
        $goods = (new OrderService())->explodeOrderGoods($order);
        $deliveryMan = DeliveryMan::where('shop_id', auth()->user()->shop()->pluck('id'))->lists('name', 'id');
        return view('index.order.order-buy-detail', [
            'order' => $order,
            'mortgageGoods' => $goods['mortgageGoods'],
            'orderGoods' => $goods['orderGoods'],
            'userBalance' => $this->userBalance['availableBalance'],
            'delivery_man' => $deliveryMan
        ]);
    }

    /**
     * 获取不同状态订单数
     *
     * @param int $nonSend
     * @param int $waitReceive
     * @param int $waitConfirm
     * @return array
     */
    private function _getOrderNum($nonSend = -1, $waitReceive = -1, $waitConfirm = -1)
    {
        $userId = auth()->id();
        $data = [
            'waitPay' => $nonSend >= 0 ? $nonSend : Order::ofBuy($userId)->nonPayment()->count(),
            //待发货
            'waitReceive' => $waitReceive >= 0 ? $waitReceive : Order::ofBuy($userId)->nonArrived()->count(),
            //待收款（针对货到付款）
            'waitConfirm' => $waitConfirm >= 0 ? $waitConfirm : Order::ofBuy($userId)->waitConfirm()->count(),
            //待确认
        ];

        return $data;
    }
}
