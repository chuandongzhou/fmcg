<?php

namespace App\Http\Controllers\Index;


use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderBuyController extends OrderController
{
    /**
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        //买家可执行功能列表
        //支付方式
        $payType = cons()->valueLang('pay_type');

        //订单状态
        $orderStatus = cons()->lang('order.status');
        $payStatus = array_slice(cons()->lang('order.pay_status'), 0, 1, true);
        $orderStatus = array_merge($payStatus, $orderStatus);

        $data['nonSure'] = Order::OfBuy($this->userId)->NonSure()->NonCancel()->count();//未确认
        $data['nonPayment'] = Order::OfBuy($this->userId)->NonPayment()->NonCancel()->count();//待付款
        $data['nonArrived'] = Order::OfBuy($this->userId)->NonArrived()->NonCancel()->count();//待收货
        //默认显示所有订单订单
        $orders = Order::OfBuy($this->userId)->orderBy('id', 'desc')->paginate()->toArray();

        return view('index.order.order-buy', [
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
            $order = Order::OfBuy($this->userId)->find($orderId);
            $orders['data'][0] = $order;
        } else {
            $orders = Order::OfBuy($this->userId)->ofSellerType($search, $this->userId)->paginate()->toArray();
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
        $orders = Order::OfBuy($this->userId)->OfSelectOptions($search)->paginate()->toArray();

        return $orders;
    }

    /**
     * 获取待付款订单列表
     *
     * @return mixed
     */
    public function getNonPayment()
    {
        return Order::OfBuy($this->userId)->NonPayment()->paginate()->toArray();
    }

    /**
     * 获取待确认订单列表
     *
     * @return mixed
     */
    public function getNonSure()
    {
        return Order::OfBuy($this->userId)->NonSure()->paginate()->toArray();
    }

    /**
     * 获取待收货订单列表
     *
     * @return mixed
     */
    public function getNonArrived()
    {
        return Order::OfBuy($this->userId)->NonArrived()->paginate()->toArray();
    }

    /**
     * 订单完成，订单必须付款成功，状态是已发货，买家才能做完成操作，否则失败，主要是防止误操作
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    public function putBatchFinish(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $status = Order::where('user_id', $this->userId)->whereIn('id', $orderIds)->where('pay_status',
            cons('order.pay_status.payment_success'))->where('status', cons('order.status.send'))->NonCancel()->update([
            'status' => cons('order.status.finished'),
            'finished_at' => Carbon::now()
        ]);
        if ($status) {
            return $this->success();
        }

        return $this->error('操作失败');
    }

    /**
     * 获取订单详情
     *
     * @param $id
     * @return \Illuminate\View\View
     */
    public function getDetailOnline($id)
    {
        $detail = Order::Where('user_id', $this->userId)->with('user', 'shoppingAddress', 'seller', 'goods',
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
