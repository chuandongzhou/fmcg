<?php

namespace App\Http\Controllers\Api\V1\ChildUser;

use App\Http\Controllers\Api\V1\Controller;
use App\Services\ChatService;
use Gate;
use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * 保存店铺
     *
     * @param \App\Http\Requests\Api\v1\UpdateShopRequest $request
     * @param $shop
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function shop(Requests\Api\v1\UpdateShopRequest $request, $shop)
    {
        $attributes = $request->all();;

        if (Gate::forUser(child_auth()->user())->denies('validate-shop', $shop)) {
            return $this->error('保存失败');
        }
        if ($shop->fill($attributes)->save()) {

            //更新聊天远程
            $shop->load('user');
            (new ChatService())->usersHandle($shop, true);
            return $this->success('保存店铺成功');
        }
        return $this->error('保存店铺时出现错误');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function orderData(Request $request)
    {
        $month_start = !empty($request->input('month')) ? (new Carbon($request->input('month')))->startOfMonth() : Carbon::now()->startOfMonth();
        $month_end = !empty($request->input('month')) ? (new Carbon($request->input('month')))->endOfMonth() : Carbon::now()->endOfMonth();
        //本月已完成订单
        $finishedOrders = Order::select(DB::raw("count(1) as count,DATE_FORMAT(finished_at,'%Y-%m-%d') as day,status,pay_status,pay_type,is_cancel"))
            ->whereBetween('finished_at', [$month_start, $month_end])
            ->where('status', cons('order.status.finished'))->useful()->groupBy('day');

        //本月付款订单
        $receivedOrders = Order::select(DB::raw("count(1) as count,DATE_FORMAT(paid_at,'%Y-%m-%d') as receivedday,status,pay_status,pay_type,is_cancel"))
            ->whereBetween('paid_at', [$month_start, $month_end])
            ->where('pay_status', cons('order.pay_status.payment_success'))->useful()->groupBy('receivedday');
        //判断是否是终端商

        $shopId = child_auth()->user()->shop_id;
        $finishedOrders = $finishedOrders->ofSell($shopId)->useful()->get()->each(function ($order) {
            $order->setAppends([]);
        });
        $receivedOrders = $receivedOrders->ofSell($shopId)->useful()->useful()->get();


        return $this->success([
            'finishedOrders' => $finishedOrders,
            'receivedOrders' => $receivedOrders,
            'selectMonth' => $request->input('month')
        ]);
    }

}
