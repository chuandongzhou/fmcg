<?php

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;
use App\Http\Requests;
use App\Models\Order;
use DB;
use Carbon\carbon;

class InfoController extends Controller
{

    public function index()
    {
        $shop = auth()->user()->shop->load('shopAddress', 'deliveryArea', 'user')->setAppends(['logo_url']);
        $type = auth()->user()->type;
        $month = Carbon::now()->formatLocalized('%Y-%m');
        $status = cons('order.status');
        $payStatus = cons('order.pay_status');
        $payType = cons('pay_type');
        //判断是否是终端商
        if ($type == cons('user.type.retailer')) {
            //订单统计
            $countData = Order::select(DB::raw('count(if(pay_status=' . $payStatus['non_payment'] . ' and status < ' . $status['finished'] . ' and ((status > ' . $status['non_send'] . '  and pay_type =' . $payType['cod'] . ') or (status >= ' . $status['non_send'] . '  and pay_type =' . $payType['online'] . ')),true,null)) AS waitReceive,count(if(((pay_type=' . $payType['online'] . ' and pay_status=' . $payStatus['payment_success'] . ') or (pay_type=' . $payType['cod'] . ' and pay_status<'.$payStatus['refund'].')) and status=' . $status['non_send'] . ',true,null)) as waitSend,count(if(status=' . $status['send'] . ',true,null)) as refund,count(if(status=' . $status['non_confirm'] . ',true,null)) as waitConfirm'))->ofBuy(auth()->id())->nonCancel()->first();
            //本月销售图表信息
            $orderGoodsInfo = DB::select('SELECT category.name, goods.cate_level_2, sum(order_goods.num) AS sum FROM fmcg_order orders JOIN fmcg_order_goods order_goods ON orders.id = order_goods.order_id JOIN fmcg_goods goods ON goods.id = order_goods.goods_id JOIN fmcg_category category ON category.id = goods.cate_level_2 WHERE orders.user_id = :id AND orders.`status`= :status AND is_cancel = :is_cancel AND orders.finished_at LIKE :finish_at GROUP BY goods.cate_level_2',
                [
                    'id' => auth()->id(),
                    'status' => cons('order.status.finished'),
                    'is_cancel' => cons('order.is_cancel.off'),
                    'finish_at' => '%' . $month . '%'
                ]);
        }
        else {
            //订单统计
            $countData = Order::select(DB::raw('count(if(pay_status=' . $payStatus['non_payment'] . ',true,null)) AS waitReceive,count(if(((pay_type=' . $payType['online'] . ' and pay_status=' . $payStatus['payment_success'] . ') or (pay_type=' . $payType['cod'] . ' and pay_status<'.$payStatus['payment_success'].')) and status=' . $status['non_send'] . ',true,null)) as waitSend,count(if((pay_type=' . $payType['cod'] . ' and status=' . $status['send'] . ') or (pay_type=' . $payType['pick_up'] . ' and status=' . $status['non_send'] . '),true,null)) as refund,count(if(status=' . $status['non_confirm'] . ',true,null)) as waitConfirm'))->OfSell(auth()->id())->nonCancel()->first();
            //本月销售图表信息
            $orderGoodsInfo = DB::select('SELECT category.name, goods.cate_level_2, sum(order_goods.num) AS sum FROM fmcg_order orders JOIN fmcg_order_goods order_goods ON orders.id = order_goods.order_id JOIN fmcg_goods goods ON goods.id = order_goods.goods_id JOIN fmcg_category category ON category.id = goods.cate_level_2 JOIN fmcg_shop AS shop ON shop.id = orders.shop_id JOIN fmcg_user user ON user.id = shop.user_id WHERE user.id = :id AND orders.`status` = :status AND is_cancel = :is_cancel AND orders.finished_at LIKE :finish_at GROUP BY goods.cate_level_2',
                [
                    'id' => auth()->id(),
                    'status' => cons('order.status.finished'),
                    'is_cancel' => cons('order.is_cancel.off'),
                    'finish_at' => '%' . $month . '%'
                ]);
        }
        return view('index.personal.info', [
            'shop' => $shop,
            'waitReceive' => $countData->waitReceive,
            'waitSend' => $countData->waitSend,
            'refund' => $countData->refund,
            'waitConfirm' => $countData->waitConfirm,
            'type' => $type,
            'orderGoodsInfo' => json_encode($orderGoodsInfo),
        ]);
    }


}
