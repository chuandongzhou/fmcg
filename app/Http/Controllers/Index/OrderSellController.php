<?php

namespace App\Http\Controllers\Index;


use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderSellController extends OrderController
{
    /**
     * 构造方法限制终端商访问销售功能
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('retailer');
    }

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
        $data['nonSure'] = Order::ofSell($this->userId)->nonSure()->count();//未确认
        $data['nonSend'] = Order::ofSell($this->userId)->nonSend()->count();//待发货
        $data['pendingCollection'] = Order::ofSell($this->userId)->getPayment()->count();//待收款（针对货到付款）

        $orders = Order::ofSell($this->userId)->orderBy('id', 'desc')->paginate()->toArray();

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
            $order = Order::ofSell($this->userId)->find($orderId);
            $orders['data'][0] = $order;
        } else {
            $orders = Order::ofSell($this->userId)->ofUserType($search, $this->userType)->paginate()->toArray();
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
        $orders = Order::ofSell($this->userId)->ofSelectOptions($search)->paginate()->toArray();

        return $orders;
    }

    /**
     * 获取待确认订单列表
     *
     * @return mixed
     */
    public function getNonSure()
    {
        return Order::ofSell($this->userId)->nonSure()->paginate()->toArray();
    }

    /**
     * 获取待发货订单列表
     *
     * @return mixed
     */
    public function getNonSend()
    {
        return Order::ofSell($this->userId)->nonSend()->paginate()->toArray();
    }

    /**
     * 获取待收款订单列表
     *
     * @return mixed
     */
    public function getPendingCollection()
    {
        return Order::ofSell($this->userId)->getPayment()->paginate()->toArray();
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
        $status = Order::ofSellByShopId($this->userId)->whereIn('id', $orderIds)->where('status',
            cons('order.status.non_sure'))->nonCancel()->update([
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
        $status = Order::ofSellByShopId($this->userId)->whereIn('id',
            $orderIds)->nonCancel()->update(['status' => cons('order.status.send'), 'send_at' => Carbon::now()]);
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
        $status = Order::ofSellByShopId($this->userId)->whereIn('id', $orderIds)->where('pay_status',
            cons('order.pay_status.payment_success'))->nonCancel()->update([
            'status' => cons('order.status.finished'),
            'finished_at' => Carbon::now()
        ]);
        if ($status) {
            return $this->success();
        }

        return $this->error('请确认买家是否付款');
    }

    /**
     * 查询订单详情
     *
     * @param $id
     * @return \Illuminate\View\View
     */
    public function getDetail($id)
    {
        $detail = Order::ofSellByShopId($this->userId)->with('shippingAddress', 'user', 'shop.user', 'goods',
            'shippingAddress.address')->find($id);
        if (!$detail) {
            return $this->error('订单不存在');
        }
        $detail = $detail->toArray();

        //拼接需要调用的模板名字
        $folderName = array_flip(cons('user.type'))[$this->userType];
        $payType = $detail['pay_type'];
        $fileName = array_flip(cons('pay_type'))[$payType];

        $view = 'index.order.' . $folderName . '.detail-' . $fileName;

        return view($view, [
            'order' => $detail
        ]);
    }
}
