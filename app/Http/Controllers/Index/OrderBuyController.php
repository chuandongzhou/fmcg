<?php

namespace App\Http\Controllers\Index;


use App\Http\Requests;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\Order;

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
        $this->middleware('supplier');
        $this->userBalance = (new UserService())->getUserBalance();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        //买家可执行功能列表
        //支付方式
        $payType = cons()->valueLang('pay_type');

        //订单状态
        $orderStatus = cons()->lang('order.status');
        $payStatus = array_slice(cons()->lang('order.pay_status'), 0, 1, true);
        $orderStatus = array_merge($payStatus, $orderStatus);

        $search = $request->all();
        $search['search_content'] = isset($search['search_content']) ? trim($search['search_content']) : '';
        $search['pay_type'] = isset($search['pay_type']) ? $search['pay_type'] : '';
        $search['status'] = isset($search['status']) ? trim($search['status']) : '';
        $search['start_at'] = isset($search['start_at']) ? $search['start_at'] : '';
        $search['end_at'] = isset($search['end_at']) ? $search['end_at'] : '';
        $orders = Order::ofBuy(auth()->id());
        if (is_numeric($search['search_content'])) {
            $orders = $orders->where('id', $search['search_content']);
        } elseif ($search['search_content']) {
            $orders = $orders->ofSelectOptions($search)->ofUserShopName($search['search_content']);
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
        $orders = Order::ofBuy(auth()->id())->nonPayment();
        return view('index.order.order-buy', [
            'orders' => $orders->paginate(),
            'data' => $this->_getOrderNum($orders->count()),
            'userBalance' => $this->userBalance['availableBalance']
        ]);
    }

    /**
     * 待收货订单
     */
    public function getWaitReceive()
    {
        $orders = Order::ofBuy(auth()->id())->nonArrived();
        return view('index.order.order-buy', [
            'orders' => $orders->paginate(),
            'data' => $this->_getOrderNum(-1, $orders->count())
        ]);
    }

    /**
     * 待确认订单
     */
    public function getWaitConfirm()
    {
        $orders = Order::ofBuy(auth()->id())->ofWaitConfirm(1);
        return view('index.order.order-buy', [
            'orders' => $orders->paginate(),
            'data' => $this->_getOrderNum(-1, -1, $orders->count())
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

        $viewName = str_replace('_', '-', array_search($order->pay_type, cons('pay_type')));

        //拼接需要调用的模板名字
        $view = 'index.order.buy.detail-' . $viewName;

        return view($view, [
            'order' => $order,
            'userBalance' => $this->userBalance['availableBalance']
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
            'waitConfirm' => $waitConfirm >= 0 ? $waitConfirm : Order::ofBuy($userId)->ofWaitConfirm(1)->count(),
            //待确认
        ];

        return $data;
    }
}
