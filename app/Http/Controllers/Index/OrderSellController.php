<?php

namespace App\Http\Controllers\Index;


use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderSellController extends OrderController
{
    /**
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        //卖家可执行功能列表
        //支付方式
        $payType = cons()->valueLang('pay_type');

        //订单状态
        $orderStatus = cons()->lang('order.status');
        $payStatus = array_slice(cons()->lang('order.pay_status'), 0, 1, true);
        $orderStatus = array_merge($payStatus, $orderStatus);

        $data['nonSure'] = Order::OfSell($this->userId)->NonSure()->count();//未确认
        $data['nonSend'] = Order::OfSell($this->userId)->NonSend()->count();//待发货
        $data['pendingCollection'] = Order::OfSell($this->userId)->GetPayment()->count();//待收款（针对货到付款）

        $orders = Order::OfSell($this->userId)->orderBy('id', 'desc')->paginate()->toArray();

        return view('index.order.order-sell', [
            'pay_type' => $payType,
            'order_status' => $orderStatus,
            'data' => $data,
            'orders' => $orders
        ]);
    }

    /**
     * 处理搜索按钮发送过来的请求
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getSearch(Request $request)
    {

        $search = $request->input('search_content');
        $orderId = trim($search);
        if (is_numeric($orderId)) {
            $order = Order::OfSell($this->userId)->find($orderId);
            $orders['data'][0] = $order;
        } else {
            $orders = Order::OfSell($this->userId)->ofUserType($search, $this->userId)->paginate()->toArray();
        }

        return $orders;
    }

    /**
     * 处理select发送过来的请求
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getSelect(Request $request)
    {
        $search = $request->all();
        $orders = Order::OfSell($this->userId)->OfSelectOptions($search)->paginate()->toArray();

        return $orders;
    }

    /**
     * 获取待确认订单列表
     *
     * @return mixed
     */
    public function getNonSure()
    {
        return Order::OfSell($this->userId)->NonSure()->paginate()->toArray();
    }

    /**
     * 获取待发货订单列表
     *
     * @return mixed
     */
    public function getNonSend()
    {
        return Order::OfSell($this->userId)->NonSend()->paginate()->toArray();
    }

    /**
     * 获取待收款订单列表
     *
     * @return mixed
     */
    public function getPendingCollection()
    {
        return Order::OfSell($this->userId)->GetPayment()->paginate()->toArray();
    }

    /**
     * 批量更新订单确认状态
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putBatchSure(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $status = Order::where('shop_id', $this->userId)->whereIn('id', $orderIds)->where('status',
            cons('order.status.non_sure'))->NonCancel()->update([
            'status' => cons('order.status.non_send'),
            'confirmed_at' => Carbon::now()
        ]);
        if ($status) {
            return $this->success();
        }

        return $this->error('操作失败');
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
        $status = Order::where('shop_id', $this->userId)->whereIn('id',
            $orderIds)->NonCancel()->update(['status' => cons('order.status.send'), 'send_at' => Carbon::now()]);
        if ($status) {
            return $this->success();
        }

        return $this->error('操作失败');

    }

    /**
     * 批量修改订单完成状态，无论在线支付还是货到付款都需要确认付款状态是否是付款成功
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putBatchFinish(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $status = Order::where('shop_id', $this->userId)->whereIn('id', $orderIds)->where('pay_status',
            cons('order.pay_status.payment_success'))->NonCancel()->update([
            'status' => cons('order.status.finished'),
            'finished_at' => Carbon::now()
        ]);
        if ($status) {
            return $this->success();
        }

        return $this->error('请确认买家是否付款');
    }

    public function getDetailOnline($id)
    {
        $detail = Order::where('shop_id', $this->userId)->with('shippingAddress', 'user', 'seller', 'goods',
            'goods.images')->find($id)->toArray();
        if ($detail['pay_type'] == cons('pay_type.online')) {
            return view('index.order.detail-online', [
                'order' => $detail
            ]);
        }

        return view('index.order.detail-cod', [
            'order' => $detail
        ]);
    }
}
