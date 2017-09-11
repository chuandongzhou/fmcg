<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/10
 * Time: 16:46
 */

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use Gate;
use Illuminate\Http\Request;


class UnionPayController extends Controller
{
    //支付方式
    protected $payTypes = ['alipay', 'wechat'];

    /**
     * 获取二维码订单
     *
     * @param \Illuminate\Http\Request $request
     * @param $orderId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getQrCode(Request $request, $orderId)
    {
        $order = Order::with('shop')->find($orderId);
        if (is_null($order) || !$order->can_payment) {
            return $this->error('订单不存在或已支付');
        }

        $payType = $request->input('pay_type', head($this->payTypes));

        if(!in_array($payType, $this->payTypes)) {
            return $this->error('支付方式暂未开通');
        }

        $unionPay = app('union.pay');

        $result = $unionPay->pay($order, $payType);

        if ($result['ret'] != 0) {
            return $this->error($result['message']);
        }

        return $this->success(['code_url' => $result['data']['qrcodeurl']]);
    }


    /**
     * 获取订单状态
     *
     * @param $orderId
     * @return mixed
     */
    public function orderPayStatus($orderId)
    {
        $order = Order::find($orderId);

        if (is_null($order)) {
            return $this->error('订单不存在');
        }

        return $this->success(['pay_status' => $order->pay_status]);
    }


}