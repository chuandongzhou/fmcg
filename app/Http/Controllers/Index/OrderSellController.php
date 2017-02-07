<?php

namespace App\Http\Controllers\Index;


use App\Models\DeliveryMan;
use App\Services\OrderDownloadService;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;
use QrCode;

class OrderSellController extends OrderController
{
    /**
     * 构造方法限制终端商访问销售功能
     */
    public function __construct()
    {
        $this->middleware('retailer');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        //卖家可执行功能列表
        //订单状态
        $orderStatus = cons()->lang('order.status');
        $payStatus = array_slice(cons()->lang('order.pay_status'), 0, 1, true);
        $orderStatus = array_merge($payStatus, $orderStatus);

        $search = $request->all();
        $search['search_content'] = isset($search['search_content']) ? trim($search['search_content']) : '';
        //$search['pay_type'] = isset($search['pay_type']) ? $search['pay_type'] : '';
        //$search['status'] = isset($search['status']) ? trim($search['status']) : '';
        //$search['start_at'] = isset($search['start_at']) ? $search['start_at'] : '';
        //$search['end_at'] = isset($search['end_at']) ? $search['end_at'] : '';

        $orders = Order::OfSell(auth()->id())->WithExistGoods([
            'user.shop',
            'shippingAddress.address'
        ]);
        if (is_numeric($search['search_content'])) {
            $orders = $orders->where('id', $search['search_content']);
        } elseif ($search['search_content']) {
            $orders = $orders->ofSelectOptions($search)->ofUserShopName($search['search_content']);
        } else {
            $orders = $orders->ofSelectOptions($search);
        }
        $deliveryMan = DeliveryMan::where('shop_id', auth()->user()->shop()->pluck('id'))->lists('name', 'id');

        return view('index.order.order-sell', [
            'order_status' => $orderStatus,
            'data' => $this->_getOrderNum(),
            'orders' => $orders->orderBy('updated_at', 'desc')->paginate(),
            'delivery_man' => $deliveryMan,
            'search' => $search
        ]);
    }

    /**
     * 待发货订单
     */
    public function getWaitSend()
    {
        $orders = Order::OfSell(auth()->id())->WithExistGoods([
            'user.shop',
        ])->NonSend();
        $deliveryMan = DeliveryMan::where('shop_id', auth()->user()->shop()->pluck('id'))->lists('name', 'id');
        return view('index.order.order-sell', [
            'data' => $this->_getOrderNum($orders->count()),
            'orders' => $orders->paginate(),
            'delivery_man' => $deliveryMan
        ]);
    }

    /**
     * 待收款订单
     */
    public function getWaitReceive()
    {
        $orders = Order::ofSell(auth()->id())->WithExistGoods(['user.shop'])->getPayment();
        return view('index.order.order-sell', [
            'data' => $this->_getOrderNum(-1, $orders->count()),
            'orders' => $orders->paginate()
        ]);
    }

    /**
     * 待确认订单
     */
    public function getWaitConfirm()
    {
        $orders = Order::ofSell(auth()->id())->WithExistGoods([
            'user.shop',
        ])->waitConfirm();
        return view('index.order.order-sell', [
            'data' => $this->_getOrderNum(-1, -1, $orders->count()),
            'orders' => $orders->paginate()
        ]);
    }


    /**
     * 查询订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function getDetail(Request $request)
    {
        $order = Order::OfSell(auth()->id())->with('user.shop', 'shop.user', 'goods',
            'shippingAddress.address', 'systemTradeInfo',
            'orderChangeRecode')->find(intval($request->input('order_id')));
        if (!$order) {
            return redirect('order-sell');
        }

        $diffTime = Carbon::now()->diffInSeconds($order->updated_at);

        $goods = (new OrderService)->explodeOrderGoods($order);

        $view = 'index.order.order-sell-detail';
        $deliveryMan = DeliveryMan::where('shop_id', auth()->user()->shop_id)->lists('name', 'id');

        return view($view, [
            'order' => $order,
            'mortgageGoods' => $goods['mortgageGoods'],
            'orderGoods' => $goods['orderGoods'],
            'delivery_man' => $deliveryMan,
            'backUrl' => $diffTime > 10 ? 'javascript:history.back()' : url('order-sell')
        ]);
    }

    /**
     * 选择模版页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTemplete()
    {
        $shopId = auth()->user()->shop_id;

        $defaultTempleteId = app('order.download')->getTemplete($shopId);

        return view('index.order.sell.templete', ['defaultTempleteId' => $defaultTempleteId]);
    }

    /**
     * 导出订单word文档,只有卖家可以导出
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response|void
     */
    public function getExport(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        if (empty($orderIds)) {
            return $this->error('请选择要导出的订单', null, ['export_error' => '请选择要导出的订单']);
        }

        $status = cons('order.status');
        $result = Order::with('shippingAddress.address', 'goods', 'shop')
            ->OfSell(auth()->id())->where('status', '>=', $status['non_send'])
            ->whereIn('id', $orderIds)->get();
        if ($result->isEmpty()) {
            return $this->error('要导出的订单不存在', null, ['export_error' => '要导出的订单不存在']);
        }
        (new OrderDownloadService())->download($result);
    }

    /**
     * 查询浏览器打印订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function getBrowserExport(Request $request)
    {
        $orderId = $request->input('order_id');
        if (empty($orderId)) {
            return $this->error('请选择要导出的订单', null, ['export_error' => '请选择要导出的订单']);
        }

        $status = cons('order.status');
        $order = Order::with('shippingAddress.address', 'shop', 'user')
            ->OfSell(auth()->id())->where('status', '>=', $status['non_send'])
            ->find($orderId);
        if (is_null($order)) {
            return $this->error('订单不存在');
        }

        $orderGoods = (new OrderService())->explodeOrderGoods($order);

        $allNum = 0;
        foreach ($orderGoods['orderGoods'] as $goods) {
            $allNum += $goods->pivot->num;
        }
        $order->allNum = $allNum;
        $shopId = $order->shop_id;
        $modelId = app('order.download')->getTemplete($shopId);
        (new OrderDownloadService())->addDownloadCount($order);

        return view('index.order.sell.templet.templet-table' . $modelId,
            [
                'order' => $order,
                'orderGoods' => $orderGoods['orderGoods'],
                'mortgageGoods' => $orderGoods['mortgageGoods']
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
            'nonSend' => $nonSend >= 0 ? $nonSend : Order::OfSell($userId)->nonSend()->count(),
            //待发货
            'waitReceive' => $waitReceive >= 0 ? $waitReceive : Order::OfSell($userId)->getPayment()->count(),
            //待收款（针对货到付款）
            'waitConfirm' => $waitConfirm >= 0 ? $waitConfirm : Order::OfSell($userId)->waitConfirm()->count(),
            //待确认
        ];


        return $data;
    }


}
