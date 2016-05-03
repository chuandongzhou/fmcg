<?php
namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\DeliveryMan;
use Hash;
use App\Models\Order;
use Carbon\Carbon;
use App\Http\Requests\Api\v1\DeliveryRequest;
use App\Http\Requests\Api\v1\DeliveryLoginRequest;
use DB;

class DeliveryController extends Controller
{

    /**
     *
     *返回配送人员登陆页面 测试用
     */
    public function index()
    {
        return view('api.login');
    }

    /**
     *
     * 配送人员登录
     *
     * @param \App\Http\Requests\Api\v1\DeliveryLoginRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     *
     */
    public function login(DeliveryLoginRequest $request)
    {

        $userName = $request->input('user_name');
        $password = $request->input('password');
        if (empty($userName) || empty($password)) {
            return $this->invalidParam('password', '账号或密码不能为空');
        }

        $deliveryMan = DeliveryMan::where('user_name', $userName)->first();
        if (!$deliveryMan) {
            return $this->invalidParam('password', '账号不存在');
        }
        $password = md5($password);

        if ($password != $deliveryMan->password) {
            return $this->invalidParam('password', '密码错误');
        }
        $nowTime = Carbon::now();

        $deliveryMan->last_login_at = $nowTime;
        if ($deliveryMan->save()) {
            delivery_auth()->login($deliveryMan, true);
            return $this->success($deliveryMan);

        }


    }

    /**
     * 所有已分配订单信息
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function orders()
    {
        $orders = Order::where(['delivery_man_id' => delivery_auth()->id()])->whereNull('delivery_finished_at')->with('user.shop',
            'shippingAddress.address')->paginate();

        $orders->each(function ($order) {

            $order->is_pay = $order->pay_status == 1 ? 1 : 0;
            $order->pieces = cons('pieces');
            $order->user->setHidden([]);
        });

        //  dd($orders);
        return $this->success($orders);


    }

    /**
     *配送历史
     *
     * @param \App\Http\Requests\Api\v1\DeliveryRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function historyOrders(DeliveryRequest $request)
    {
        $start_at = (new Carbon($request->input('start_at')))->startOfDay();
        $end_at = (new Carbon($request->input('end_at')))->endOfDay();

        $orders = Order::where(['delivery_man_id' => delivery_auth()->id()])
            ->whereNotNull('delivery_finished_at')
            ->whereBetween('delivery_finished_at', [$start_at, $end_at])
            ->with('user.shop')->orderBy('delivery_finished_at', 'DESC')->get();

        $historyOrder = array();
        $j = 0;
        foreach ($orders as $order) {
            $date = (new Carbon($order['delivery_finished_at']))->toDateString();
            $key = array_search($date, array_column($historyOrder, 'date'));
            $order->delivery_finished_at = (new Carbon($order->delivery_finished_at))->getTimestamp();
            if ($key === false) {
                $historyOrder[$j]['date'] = $date;
                $historyOrder[$j]['data'][] = $order;
                $j++;
            } else {
                $historyOrder[$key]['data'][] = $order;
            }
        }

        return $this->success(['historyOrder' => $historyOrder]);

    }

    /**
     * 配送订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function detail(Request $request)
    {


        $order = Order::where(['delivery_man_id' => delivery_auth()->id()])->with('user.shop',
            'shippingAddress.address',
            'goods.images.image')->find($request->input('order_id'));

        $order->is_pay = $order->pay_status == 1 ? 1 : 0;
        $order->user->setHidden([]);
        $order->goods->each(function ($goods) {
            $goods->addHidden(['introduce', 'images_url', 'pieces']);

        });

        //dd($order);
        return $this->success($order);

    }

    /**
     * 处理完成配送
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function dealDelivery(Request $request)
    {

        $orderId = $request->input('order_id');
        if (empty($orderId)) {
            return $this->error('订单号不能为空');
        }

        $order = Order::where(['delivery_man_id' => delivery_auth()->id()])->find($orderId);

        if (empty($order)) {
            return $this->error('该订单不存在');
        }

        if (!empty($order['delivery_finished_at'])) {
            return $this->error('配送已完成');
        }

        $nowTime = Carbon::now();
        $order->delivery_finished_at = $nowTime;
        if ($order->save()) {

            return $this->success('完成配送');
        }
        return $this->error('操作失败');
    }

    /**
     * 退出登陆
     *
     */
    public function logout()
    {
        delivery_auth()->logout();
        return $this->success();
    }

}