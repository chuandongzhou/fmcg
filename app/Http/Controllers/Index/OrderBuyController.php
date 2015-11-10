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
        $data['nonPayment'] = Order::ofBuy($this->user->id)->nonPayment()->count();//待付款
        $data['nonArrived'] = Order::ofBuy($this->user->id)->nonArrived()->count();//待收货

        $search = $request->all();
        $search['search_content'] = isset($search['search_content']) ? trim($search['search_content']) : '';
        $search['pay_type'] = isset($search['pay_type']) ? $search['pay_type'] : '';
        $search['status'] = isset($search['status']) ? trim($search['status']) : '';
        $search['start_at'] = isset($search['start_at']) ? $search['start_at'] : '';
        $search['end_at'] = isset($search['end_at']) ? $search['end_at'] : '';
        $query = Order::ofBuy($this->user->id)->with('shop.user', 'goods')->orderBy('id', 'desc');
        if (is_numeric($search['search_content'])) {
            $orders = $query->where('id', $search['search_content'])->paginate();
        } else {
            $orders = $query->ofSelectOptions($search)->ofSellerShopName($search['search_content'])->paginate();
        }

        return view('index.order.order-buy', [
            'pay_type' => $payType,
            'order_status' => $orderStatus,
            'data' => $data,
            'orders' => $orders,
            'search' => $search
        ]);
    }

    /**
     * 获取订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function getDetail(Request $request)
    {
        $detail = Order::where('user_id', $this->user->id)->with('user', 'shippingAddress', 'shop', 'goods',
            'goods.images', 'deliveryMan', 'shippingAddress.address')->find($request->input('order_id'));
        if (!$detail) {
            return $this->error('订单不存在');
        }
        //拼接需要调用的模板名字
        $view = 'index.order.retailer.detail-' . array_flip(cons('pay_type'))[$detail->pay_type];

        return view($view, [
            'order' => $detail
        ]);
    }
}
