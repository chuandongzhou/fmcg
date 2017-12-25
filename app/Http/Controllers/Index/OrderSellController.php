<?php

namespace App\Http\Controllers\Index;


use App\Models\DeliveryMan;
use App\Services\OrderDownloadService;
use App\Services\OrderService;
use App\Services\GoodsService;
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
        $this->middleware('forbid:retailer');
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
        $payStatus = array_only(cons()->lang('order.pay_status'), ['refund', 'refund_success']);
        $orderStatus = array_merge(['wait_receive' => '未收款'], $payStatus, $orderStatus);

        $search = $request->all();

        $orders = Order::ofSell(auth()->user()->shop_id)->useful()->with([
            'user.shop',
            'shippingAddress.address',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            },
            'goods.images'
        ]);
        if (is_numeric($searchContent = array_get($search, 'search_content'))) {
            $orders = $orders->where('id', $searchContent);
        } else {
            $orders = $orders->ofSelectOptions($search)->ofUserShopName($searchContent);
        }
        //已作废 、已发货、已完成、退款成功按操作时间倒序
        if (array_get($search, 'status') == 'send') {
            $orders->orderBy('send_at', 'DESC');
        } else {
            $orders->orderBy('updated_at', 'DESC')->orderBy('id', 'DESC');
        }
        $deliveryMan = DeliveryMan::active()->where('shop_id', auth()->user()->shop_id)->lists('name',
            'id');
        return view('index.order.order-sell', [
            'order_status' => $orderStatus,
            'data' => $this->_getOrderNum(),
            'orders' => $orders->paginate(),
            'delivery_man' => $deliveryMan,
            'search' => $search
        ]);
    }

    /**
     * 待发货订单
     */
    public function getWaitSend()
    {
        $orders = Order::ofSell(auth()->user()->shop_id)->useful()->with([
            'user.shop',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
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
        $orders = Order::ofSell(auth()->user()->shop_id)->getPayment()->useful()->with([
            'user.shop',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ]);
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
        $orders = Order::ofSell(auth()->user()->shop_id)->with([
            'user.shop',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
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
        $order = Order::ofSell(auth()->user()->shop_id)->useful()->with('user.shop', 'shop.user', 'goods',
            'shippingAddress.address', 'systemTradeInfo',
            'orderChangeRecode', 'gifts', 'applyPromo')->find(intval($request->input('order_id')));
        if (!$order) {
            return redirect('order-sell');
        }
        $diffTime = Carbon::now()->diffInSeconds($order->updated_at);

        $goods = (new OrderService)->explodeOrderGoods($order);
        $goods['orderGoods']->each(function ($goods) use (&$goods_quantity) {
            $goods_quantity += $goods->pivot->num;
        });
        $deliveryMan = DeliveryMan::where('shop_id', auth()->user()->shop_id)->lists('name', 'id');
        return view('index.order.order-sell-detail', [
            'order' => $order,
            'goods_quantity' => $goods_quantity,
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
            ->ofSell(auth()->user()->shop_id)->useful()
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

        $order = Order::with('shippingAddress.address', 'applyPromo.promo', 'shop', 'user', 'gifts',
            'goods.goodsPieces')
            ->ofSell(auth()->user()->shop_id)->useful()
            ->find($orderId);
        if (is_null($order)) {
            return $this->error('订单不存在');
        }

        $orderGoods = (new OrderService())->explodeOrderGoods($order);
        $GoodsServer = new GoodsService();
        foreach ($order->goods as $item) {
            if ((cons('user.type.' . $order->user_type_name) == 1 && $item->pivot->pieces != $item->pieces_retailer) || (cons('user.type.' . $order->user_type_name) == 2 && $item->pivot->pieces != $item->pieces_wholesaler)) {
                $item->{'specification_' . $order->user_type_name} = $GoodsServer->getPiecesSystem2($item,
                    $item->pivot->pieces);
            }
        }
        $orderGoodsNum = $giftGoodsNum = 0;
        foreach ($orderGoods['orderGoods'] as $goods) {
            $orderGoodsNum += $goods->pivot->num;
        }
        $order->allNum = $orderGoodsNum + $order->gifts->sum('pivot.num') ?? 0;
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
        $shopId = auth()->user()->shop_id;
        $data = [
            'nonSend' => $nonSend >= 0 ? $nonSend : Order::OfSell($shopId)->nonSend()->count(),
            //待发货
            'waitReceive' => $waitReceive >= 0 ? $waitReceive : Order::OfSell($shopId)->getPayment()->useful()->count(),
            //待收款（针对货到付款）
            'waitConfirm' => $waitConfirm >= 0 ? $waitConfirm : Order::OfSell($shopId)->waitConfirm()->useful()->count(),
            //待确认
        ];
        return $data;
    }

    /**
     * 代下单
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getReplace()
    {
        return view('index.order.order-sell-replace');
    }
    
    
}
