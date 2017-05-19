<?php

namespace App\Http\Controllers\ChildUser;

use App\Models\Order;
use DB;
use Carbon\carbon;

class InfoController extends Controller
{

    public function index()
    {
        $shop = child_auth()->user()->shop->load('shopAddress', 'deliveryArea', 'user')->setAppends(['logo_url']);
        $month = Carbon::now()->formatLocalized('%Y-%m');
        $status = cons('order.status');
        $payStatus = cons('order.pay_status');
        $payType = cons('pay_type');
        //判断是否是终端商

        //订单统计
        $countData = Order::select(DB::raw('count(if(pay_status=' . $payStatus['non_payment'] . ',true,null)) AS waitReceive,count(if(((pay_type=' . $payType['online'] . ' and pay_status=' . $payStatus['payment_success'] . ') or (pay_type=' . $payType['cod'] . ' and pay_status<' . $payStatus['payment_success'] . ')) and status=' . $status['non_send'] . ',true,null)) as waitSend,count(if((pay_type=' . $payType['cod'] . ' and status=' . $status['send'] . ') or (pay_type=' . $payType['pick_up'] . ' and status=' . $status['non_send'] . '),true,null)) as refund,count(if(status=' . $status['non_confirm'] . ',true,null)) as waitConfirm'))->ofSell(child_auth()->user()->shop_id)->useful()->first();
        //本月销售图表信息
        $orderGoodsInfo = DB::select('SELECT category.name, goods.cate_level_2, sum(order_goods.num) AS sum FROM fmcg_order orders JOIN fmcg_order_goods order_goods ON orders.id = order_goods.order_id JOIN fmcg_goods goods ON goods.id = order_goods.goods_id JOIN fmcg_category category ON category.id = goods.cate_level_2 JOIN fmcg_shop AS shop ON shop.id = orders.shop_id  WHERE shop.id = :id AND orders.`status` = :status AND is_cancel = :is_cancel AND orders.finished_at LIKE :finish_at GROUP BY goods.cate_level_2',
            [
                'id' => child_auth()->user()->shop_id,
                'status' => cons('order.status.finished'),
                'is_cancel' => cons('order.is_cancel.off'),
                'finish_at' => '%' . $month . '%'
            ]);

        return view('child-user.info.index', [
            'shop' => $shop,
            'waitReceive' => $countData->waitReceive,
            'waitSend' => $countData->waitSend,
            'refund' => $countData->refund,
            'waitConfirm' => $countData->waitConfirm,
            'orderGoodsInfo' => json_encode($orderGoodsInfo),
        ]);
    }


}
