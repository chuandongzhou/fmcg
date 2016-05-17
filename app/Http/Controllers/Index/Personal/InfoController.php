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

        //待付款
        $waitReceive = Order::bySellerId(auth()->id())->where('pay_status',
            cons('order.pay_status.non_payment'))->where('pay_type', cons('pay_type.online'))->nonCancel()->count();
        //待发货
        $waitSend = Order::bySellerId(auth()->id())->nonSend()->count();
        //代收款
        $refund = Order::bySellerId(auth()->id())->getPayment()->nonCancel()->count();
        //待确认（待审核）
        $waitConfirm = Order::bySellerId(auth()->id())->waitConfirm()->count();
        return view('index.personal.info', [
            'shop' => $shop,
            'waitReceive' => $waitReceive,
            'waitSend' => $waitSend,
            'refund' => $refund,
            'waitConfirm' => $waitConfirm,
        ]);
    }


}
