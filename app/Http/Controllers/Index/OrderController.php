<?php

namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Order;

class OrderController extends Controller
{
    public $userId;//用户ID


    public function __construct()
    {
        //TODO:获取当前用户ID号
        $this->userId = 1;

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
        $status = Order::where('seller_id', $this->userId)->orWhere('user_id', $this->userId)->whereIn('id',
            $orderIds)->where('pay_status',
            cons('order.status.non_payment'))->NonSend()->update(['status' => cons('order.status.non_sure')]);
        if ($status) {
            return $this->success();
        }

        return $this->error('操作失败');
    }


}
