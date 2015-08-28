<?php

namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Order;

class OrderController extends Controller
{
    //TODO:获取当前用户ID号和类型
    public $userId;//用户ID
    public $userType;//用户类型


    public function __construct()
    {
        $this->userId = 2;
        $this->userType = 2;

    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        //支付方式
        $payType = cons()->valueLang('pay_type');

        //订单状态
        $orderStatus = cons()->lang('order.status');
        $payStatus = array_slice(cons()->lang('order.pay_status'), 0, 1, true);
        $orderStatus = array_merge($payStatus, $orderStatus);

        //$searchRole = $request->input['role'];//TODO:url上拼凑查询的角色类型；测试时暂时给一个假定值
        $searchRole = 1;
        if ($searchRole > $this->userType) {//显示买家待办事项及所有订单
            $data['nonSure'] = Order::OfBuy($this->userId)->NonSure()->count();//未确认
            $data['nonPayment'] = Order::OfBuy($this->userId)->NonPayment()->count();//待付款
            $data['nonArrived'] = Order::OfBuy($this->userId)->NonArrived()->count();//待收货
            //默认显示所有订单订单
            $orders = Order::OfBuy($this->userId)->orderBy('id', 'desc')->paginate()->toArray();
        } else {//显示卖家待办事项
            $data['nonSure'] = Order::OfSell($this->userId)->NonSure()->count();//未确认
            $data['nonSend'] = Order::OfSell($this->userId)->NonSend()->count();//待发货
            $data['pendingCollection'] = Order::OfSell($this->userId)->GetPayment()->count();//待收款（针对货到付款）
            $orders = Order::OfSell($this->userId)->orderBy('id', 'desc')->paginate()->toArray();
        }


        return view('index.wholesaler.admin-admin.index', [
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

        $search = $request->all();
        $orderId = trim($search['search_content']);
        if ($search['search_role'] > $this->userType) {//作为买家查询订单,这里依赖于配置文件中的先后顺序

            if (is_numeric($orderId)) {
                $order = Order::OfBuy($this->userId)->find($orderId);
                $orders['data'][0] = $order;
            } else {
                $orders = Order::OfBuy($this->userId)->ofSellerType($search)->paginate()->toArray();
            }


        } else {//作为卖家查询订单

            if (is_numeric($orderId)) {
                $order = Order::OfSell($this->userId)->find($orderId);
                $orders['data'][0] = $order;
            } else {
                $orders = Order::OfSell($this->userId)->ofUserType($search)->paginate()->toArray();

            }
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
        if ($search['search_role'] > $this->userType) {//买家查询

            $orders = Order::OfBuy($this->userId)->OfSelectOptions($search)->paginate()->toArray();
        } else {//卖家查询

            $orders = Order::OfSell($this->userId)->OfSelectOptions($search)->paginate()->toArray();
        }


        return $orders;
    }

    /**
     * 修改订单确认状态
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putSure($id)
    {
        $order = Order::where('id', $id)->where('seller_id', $this->userId)->where('status',
            cons('order.status.non_sure'))->update(['status' => cons('order.status.non_send')]);
        if ($order) {
            return $this->success();
        }

        return $this->error('操作失败');
    }

    /**
     * 批量更新订单确认状态
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putBatchSure(Request $request)
    {
        $orderIds = $request->input('orderIds');
        $orders = Order::where('seller_id', $this->userId)->whereIn('id', $orderIds)->where('status',
            cons('order.status.non_sure'))->update(['status' => cons('order.status.non_send')]);
        if ($orders) {
            return $this->success();
        }

        return $this->error('操作失败');
    }

    /**
     * 获取待付款订单列表(买家)
     *
     * @return mixed
     */
    public function getNonPayment()
    {
        return Order::OfBuy($this->userId)->NonPayment()->paginate()->toArray();
    }

    /**
     * 获取待收货订单列表(买家)
     *
     * @return mixed
     */
    public function getNonArrived()
    {
        return Order::OfBuy($this->userId)->NonArrived()->paginate()->toArray();
    }

    /**
     * 获取待确认订单列表(卖家)
     *
     * @return mixed
     */
    public function getNonSure()
    {
        return Order::OfSell($this->userId)->NonSure()->paginate()->toArray();
    }

    /**
     * 获取待发货订单列表(卖家)
     *
     * @return mixed
     */
    public function getNonSend()
    {
        return Order::OfSell($this->userId)->NonSend()->paginate()->toArray();
    }

    /**
     * 获取待收款订单列表(卖家)
     *
     * @return mixed
     */
    public function getPendingCollection()
    {
        return Order::OfSell($this->userId)->GetPayment()->paginate()->toArray();
    }
}
