<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\OrderGoods;
use App\Services\OrderService;
use Illuminate\Http\Request;
use App\Models\DeliveryMan;
use Hash;
use App\Models\Order;
use Carbon\Carbon;
use App\Http\Requests\Api\v1\DeliveryRequest;
use App\Http\Requests\Api\v1\DeliveryLoginRequest;
use DB;
use App\Http\Requests\Api\v1\UpdateOrderRequest;
use App\Services\DeliveryService;

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
            'shippingAddress.address', 'coupon')->get();

        $orders->each(function ($order) {

            $order->is_pay = $order->pay_status == 1 ? 1 : 0;
            $order->pieces = cons()->valueLang('goods.pieces');
            $order->setAppends([
                'status_name',
                'payment_type',
                'step_num',
                'can_cancel',
                'can_confirm',
                'can_send',
                'can_confirm_collections',
                'can_export',
                'can_payment',
                'can_confirm_arrived',
                'can_change_price',
                'user_shop_name',
                'after_rebates_price'
            ]);
            // $order->user&&$order->user->setHidden([]);
        });

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
            ->with('user.shop', 'shippingAddress.address')->orderBy('delivery_finished_at', 'DESC')->get();

        $historyOrder = array();
        $j = 0;
        foreach ($orders as $order) {
            $date = (new Carbon($order['delivery_finished_at']))->toDateString();
            $key = array_search($date, array_column($historyOrder, 'date'));
            $order->delivery_finished_at = (new Carbon($order->delivery_finished_at))->getTimestamp();
            $order->user && $order->user->setVisible(['shop']);
            $order->setAppends([
                'status_name',
                'payment_type',
                'step_num',
                'can_cancel',
                'can_confirm',
                'can_send',
                'can_confirm_collections',
                'can_export',
                'can_payment',
                'can_confirm_arrived',
                'can_change_price',
                'user_shop_name',
                'after_rebates_price'
            ]);
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
        $order->setAppends([
            'status_name',
            'payment_type',
            'step_num',
            'can_cancel',
            'can_confirm',
            'can_send',
            'can_confirm_collections',
            'can_export',
            'can_payment',
            'can_confirm_arrived',
            'can_change_price',
            'user_shop_name',
            'after_rebates_price'
        ]);
        $order->goods->each(function ($goods) {
            $goods->addHidden(['introduce', 'images_url', 'pieces']);

        });
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

    /**
     * 修改订单
     *
     * @param \App\Http\Requests\Api\v1\UpdateOrderRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function updateOrder(UpdateOrderRequest $request)
    {
        $deliveryId = delivery_auth()->id();
        //判断该订单是否存在
        $order = Order::where('delivery_man_id', $deliveryId)->find(intval($request->input('order_id')));
        if (!$order || !$order->can_change_price) {
            return $this->error('订单不存在或不能修改');
        }

        $attributes = $request->all();

        $flag = (new OrderService)->changeOrder($order, $attributes, $deliveryId);
        return $flag ? $this->success('修改成功') : $this->error('修改失败,稍后再试!');
    }

    /**
     * 订单统计
     *
     * @param \App\Http\Requests $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function statisticalDelivery(Request $request)
    {
        $search = $request->all();
        $search['start_at'] = isset($search['start_at']) ? (new Carbon($request->input('start_at')))->startOfDay() : '';
        $search['end_at'] = isset($search['end_at']) ? (new Carbon($request->input('end_at')))->endOfDay() : '';
        $search['delivery_man_id'] = delivery_auth()->id();
        $delivery = Order::whereNotNull('delivery_finished_at')->ofDeliverySearch($search)->with([
            'orderGoods.goods' => function ($query) {
                return $query->select(['id', 'name']);
            },
            'systemTradeInfo',
            'deliveryMan'
        ])->get();
        $data = DeliveryService::format($delivery);
        return $this->success($data);
    }

    /**
     * 删除订单商品
     *
     * @param $goodsId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function orderGoodsDelete($goodsId)
    {
        $orderGoods = OrderGoods::with('order')->find($goodsId);

        if (is_null($orderGoods) || $orderGoods->order->shop_id != delivery_auth()->user()->shop_id) {
            return $this->error('订单商品不存在');
        }
        $order = $orderGoods->order;
        $result = DB::transaction(function () use ($orderGoods, $order) {
            $orderGoodsPrice = $orderGoods->total_price;
            $orderGoods->delete();
            if ($order->orderGoods->count() == 0) {
                //订单商品删完了标记为作废
                $order->fill(['status' => cons('order.status.invalid'), 'price' => 0])->save();
            } elseif ($orderGoodsPrice > 0) {
                $order->decrement('price', $orderGoodsPrice);
            }

            $content = '订单商品id:' . $orderGoods->goods_id . '，已被删除';
            (new OrderService())->addOrderChangeRecord($order, $content, delivery_auth()->id());
            //如果有业务订单修改业务订单
            if (!is_null($salesmanVisitOrder = $order->salesmanVisitOrder)) {
                $salesmanVisitOrder->fill(['amount' => $order->price])->save();

                if ($orderGoods->price == 0) {
                    //商品原价为0时更新抵费商品
                    $salesmanVisitOrderGoods = $salesmanVisitOrder->mortgageGoods()->where('goods_id',
                        $orderGoods->goods_id)->first();

                    $salesmanVisitOrderGoods && $salesmanVisitOrder->mortgageGoods()->detach($salesmanVisitOrderGoods->id);
                } else {
                    $salesmanVisitOrder->orderGoods()->where('goods_id', $orderGoods->goods_id)->delete();
                }
            }

            return 'success';
        });


        return $result == 'success' ? $this->success('删除订单商品成功') : $this->error('删除订单商品时出现问题');
    }
}