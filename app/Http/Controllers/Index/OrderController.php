<?php

namespace App\Http\Controllers\Index;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Order;

class OrderController extends Controller
{
    public $userId;//用户ID
    public $userType;//用户类型


    public function __construct()
    {
        //TODO:获取当前用户ID号和类型
        $this->userId = 1;
        $this->userType = 2;
        session(['id' => $this->userId]);
        session(['type' => $this->userType]);

    }

    /**
     * 批量取消订单确认状态，在线支付：确认但未付款，可取消；货到付款：确认但未发货，可取消
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putCancelSure(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $status = Order::where(function ($query) {
            $query->where('seller_id', $this->userId)->orWhere('user_id', $this->userId);
        })->whereIn('id', $orderIds)->where('pay_status', cons('order.pay_status.non_payment'))->NonSend()->update([
            'is_cancel' => cons('order.is_cancel.on'),
            'cancel_by' => $this->userId,
            'cancel_at' => Carbon::now()
        ]);
        if ($status) {
            return $this->success();
        }

        return $this->error('操作失败');
    }

    /**
     * 订单统计
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getStatistics(Request $request)
    {
        //订单对象类型
        $objType = cons()->valueLang('user.type');
        array_forget($objType, $this->userType);
        //支付类型
        $payType = cons()->valueLang('pay_type');
        $selectedObj = '';
        $selectedPay = '';
        //查询条件判断
        $search = $request->all();
        if (empty($search)) {
            //无查询条件时，查询该用户所有非取消订单。
            $stat = $this->_searchAllOrder();
        } else {
            $selectedObj = $search['obj_type'];
            $selectedPay = $search['pay_type'];
            $stat = $this->_searchAllOrderByOptions($search);
        }

        $otherStat = $this->_orderStatistics($stat['data']);

        return view('index.order.order-statistics', [
            'selectedPay' => $selectedPay,
            'selectedObj' => $selectedObj,
            'pay_type' => $payType,
            'obj_type' => $objType,
            'statistics' => $stat,
            'otherStat' => $otherStat
        ]);
    }

    /**
     * 搜索该用户参与的所有支付或发货的订单
     *
     * @return mixed
     */
    private function _searchAllOrder()
    {
        return Order::where(function ($query) {
            $query->where('user_id', $this->userId)->orWhere('seller_id', $this->userId);
        })->where(function ($query) {
            //在线支付情况，只查询付款完成以后的状态
            $query->where('pay_type', cons('pay_type.online'))->where('pay_status',
                cons('order.pay_status.payment_success'));
        })->orWhere(function ($query) {
            //货到付款情况，只查询发货以后的状态
            $query->where('pay_type', cons('pay_type.cod'))->whereIn('status',
                [cons('order.status.send'), cons('order.status.finished')]);
        })->NonCancel()->with('user', 'shippingAddress', 'goods')->orderBy('id', 'desc')->paginate()->toArray();
    }

    /**
     * 根据查询条件查询满足条件的订单
     *
     * @param $search
     * @return mixed
     */
    private function _searchAllOrderByOptions($search)
    {
        return Order::where(function ($query) use ($search) {
            //付款方式
            if ($search['pay_type'] == cons('pay_type.online')) {//在线支付
                $query->where('pay_type', cons('pay_type.online'))->where('pay_status',
                    cons('order.pay_status.payment_success'));
            } else { //货到付款
                $query->where('pay_type', cons('pay_type.cod'))->whereIn('status',
                    [cons('order.status.send'), cons('order.status.finished')]);
            }
            //查询对象
            if ($search['obj_type'] > $this->userId) {
                $query->where('seller_id', intval($search['obj_type']));
            } else {
                $query->where('user_id', intval($search['obj_type']));
            }

            if (!empty($search['start_at']) && !empty($search['end_at'])) {
                $query->whereBetween('created_at', [$search['start_at'], $search['end_at']]);
            }
        })->NonCancel()->with('user', 'shippingAddress', 'goods')->orderBy('id', 'desc')->paginate()->toArray();
    }

    /**
     * 订单和商品统计
     *
     * @param array $res
     * @return mixed
     */
    private function _orderStatistics(array $res)
    {
        $stat['totalNum'] = count($res);//订单总数
        $stat['totalAmount'] = 0;//订单总金额
        //在线支付订单
        $stat['onlineNum'] = 0;
        $stat['onlineAmount'] = 0;
        //货到付款订单
        $stat['codNum'] = 0;
        $stat['codAmount'] = 0;
        $stat['codReceiveAmount'] = 0;//实收金额
        $goodStat = [];
        foreach ($res as $value) {
            //订单相关统计
            $stat['totalAmount'] += $value['price'];
            if ($value['pay_type'] == cons('pay_type.online')) {
                ++ $stat['onlineNum'];
                $stat['onlineAmount'] += $value['price'];
            } else {
                ++ $stat['codNum'];
                $stat['codAmount'] += $value['price'];
                if ($value['pay_status'] == cons('order.pay_status.payment_success')) {
                    $stat['codReceiveAmount'] += $value['price'];
                }
            }
            //商品相关统计
            foreach ($value['goods'] as $good) {
                $num = $good['pivot']['num'];
                $price = $good['pivot']['price'] * $num;
                $name = $good['name'];

                if (isset($goodStat[$good['id']])) {
                    $goodStat[$good['id']]['num'] += $num;
                    $goodStat[$good['id']]['price'] += $price;
                } else {

                    $goodStat[$good['id']] = ['name' => $name, 'price' => $price, 'num' => $num];
                }
            }
        }
        $stat['goods'] = $goodStat;

        return $stat;
    }

}
