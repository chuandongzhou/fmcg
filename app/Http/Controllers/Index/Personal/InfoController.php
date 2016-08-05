<?php

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;
use App\Http\Requests;
use App\Models\Order;

class InfoController extends Controller
{

    public function index()
    {
        $shop = auth()->user()->shop->load('shopAddress', 'deliveryArea', 'user')->setAppends(['logo_url']);

        $type = auth()->user()->type;
        //判断是否是终端商
        if ($type == cons('user.type.retailer')) {
            //待付款
            $waitReceive = Order::ofBuy(auth()->id())->nonPayment()->count();
            //待发货
            $waitSend = Order::ofBuy(auth()->id())->nonSend()->count();
            //待收货
            $refund = Order::ofBuy(auth()->id())->nonArrived()->count();
            //待确认（待审核）
            $waitConfirm = Order::ofBuy(auth()->id())->waitConfirm()->count();

        } else {
            //待付款
            $waitReceive = Order::OfSell(auth()->id())->where(['pay_status'=>
                cons('order.pay_status.non_payment'),'status'=>cons('order.status.non_send')])->where('pay_type', cons('pay_type.online'))->nonCancel()->count();
            //待发货
            $waitSend = Order::OfSell(auth()->id())->nonSend()->count();
            //代收款
            $refund = Order::OfSell(auth()->id())->getPayment()->nonCancel()->count();
            //待确认（待审核）
            $waitConfirm = Order::OfSell(auth()->id())->waitConfirm()->count();
        }
        return view('index.personal.info', [
            'shop' => $shop,
            'waitReceive' => $waitReceive,
            'waitSend' => $waitSend,
            'refund' => $refund,
            'waitConfirm' => $waitConfirm,
            'type' => $type,
        ]);
    }


}
