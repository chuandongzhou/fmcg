<?php

namespace App\Http\Controllers\Index;


use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderBuyController extends OrderController
{

    /**
     * 构造方法,限制供应商访问购买功能
     */
    public function __construct()
    {
        parent::__construct();
        //供应商无购买商品功能
        $this->middleware('supplier');
    }

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
        $data['nonPayment'] = Order::ofBuy($this->userId)->nonPayment()->count();//待付款
        $data['nonArrived'] = Order::ofBuy($this->userId)->nonArrived()->count();//待收货

        //默认显示所有订单订单
        $orders = Order::where('user_id', $this->userId)->with('shop.user', 'goods')->orderBy('id',
            'desc')->paginate()->toArray();

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
            $order = Order::ofBuy($this->userId)->ofSelectOptions($search)->find($orderId);
            if (!$order) {
                return $this->error('订单似乎不存在');
            }
            $orders['data'][0] = $order;
        } else {
            $orders = Order::ofBuy($this->userId)->ofSelectOptions($search)->ofSellerShopName($orderId)->paginate()->toArray();
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
        $orders = Order::ofBuy($this->userId)->ofSelectOptions($search)->paginate()->toArray();

        return $orders;
    }

    /**
     * 获取订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function getDetail(Request $request)
    {
        $detail = Order::where('user_id', $this->userId)->with('user', 'shippingAddress', 'shop', 'goods',
            'goods.images', 'deliveryMan', 'shippingAddress.address')->find($request->input('order_id'));
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
