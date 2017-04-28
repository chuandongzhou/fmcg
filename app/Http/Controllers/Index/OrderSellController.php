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
        $this->middleware('deposit');
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

        $orders = Order::OfSell(auth()->id())->useful()->WithExistGoods([
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
        $deliveryMan = DeliveryMan::active()->where('shop_id', auth()->user()->shop()->pluck('id'))->lists('name',
            'id');

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
        $orders = Order::OfSell(auth()->id())->useful()->WithExistGoods([
            'user.shop',
        ])->nonSend();
        $deliveryMan = DeliveryMan::active()->where('shop_id', auth()->user()->shop()->pluck('id'))->lists('name',
            'id');
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
        $orders = Order::ofSell(auth()->id())->getPayment()->nonCancel()->WithExistGoods(['user.shop']);
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
        $order = Order::OfSell(auth()->id())->useful()->with('user.shop', 'shop.user', 'goods',
            'shippingAddress.address', 'systemTradeInfo',
            'orderChangeRecode', 'gifts')->find(intval($request->input('order_id')));
        if (!$order) {
            return redirect('order-sell');
        }
        foreach ($order->goods as $goods) {
            $order->goods_amount += $goods->pivot['num'];
        }

        $diffTime = Carbon::now()->diffInSeconds($order->updated_at);

        $goods = (new OrderService)->explodeOrderGoods($order);

        $deliveryMan = DeliveryMan::where('shop_id', auth()->user()->shop_id)->lists('name', 'id');

        return view('index.order.order-sell-detail', [
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
        $shop = auth()->user()->shop;

        $defaultTempleteId = app('order.download')->getTemplete($shop->id);

        $tempHeaders = $shop->orderTempletes;

        return view('index.order.sell.templete',
            ['defaultTempleteId' => $defaultTempleteId, 'tempHeaders' => $tempHeaders]);
    }

    /**
     * 导出订单word文档,只有卖家可以导出
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getExport(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        if (empty($orderIds)) {
            return $this->error('请选择要导出的订单', null, ['export_error' => '请选择要导出的订单']);
        }

        $result = Order::with('shippingAddress.address', 'goods', 'shop')
            ->OfSell(auth()->id())->useful()
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
        $templeteId = $request->input('templete_id', 0);
        if (empty($orderId)) {
            return $this->error('请选择要导出的订单', null, ['export_error' => '请选择要导出的订单']);
        }

        $order = Order::with('shippingAddress.address', 'shop', 'user', 'gifts')
            ->OfSell(auth()->id())->useful()
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

        if ($templete = $order->shop->orderTempletes()->find($templeteId)) {
            $order->shop = $templete;
        }


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
            'waitReceive' => $waitReceive >= 0 ? $waitReceive : Order::OfSell($userId)->getPayment()->nonCancel()->count(),
            //待收款（针对货到付款）
            'waitConfirm' => $waitConfirm >= 0 ? $waitConfirm : Order::OfSell($userId)->waitConfirm()->nonCancel()->count(),
            //待确认
        ];


        return $data;
    }


}
