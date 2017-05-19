<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/10
 * Time: 16:46
 */

namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Services\PayService;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;


class WechatPayController extends Controller
{
    /**
     * 获取二维码订单
     *
     * @param $orderId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getQrCode($orderId)
    {
        $order = Order::with('wechatPayUrl', 'shop')->find($orderId);
        if (is_null($order) || !$order->can_payment) {
            return $this->error('订单不存在或已支付');
        }

        if (!is_null($wechatPayUrl = $order->wechatPayUrl)) {
            //有二维码时直接返回

            $nowTime = Carbon::now();
            if ($nowTime->diffInHours($wechatPayUrl->created_at) >= 2) {
                return $this->error('二维码已过期,请选择其它渠道');
            } else {
                return $this->success([
                    'code_url' => $wechatPayUrl->code_url,
                    'created_at' => (string)$wechatPayUrl->created_at
                ]);
            }
        }

        $wechatPay = app('wechat.pay');

        $result = $wechatPay->getQrCode($order);

        if ($result['dealCode'] != 10000) {
            return $this->error($result['dealMsg']);
        }

        if (!$wechatPay->verifySign($result)) {
            return $this->error('请求出错，请重试');
        }

        return $wechatPay->created($result, $orderId) ? $this->success([
            'code_url' => $result['codeUrl'],
            'created_at' => (string)Carbon::now()
        ]) : $this->error('创建二维码时出现问题');
    }


    /**
     * 获取续费二维码地址
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function renewQrCode(Request $request)
    {
        $type = $request->input('type', 'user');
        $month = $request->input('month');

        if ($type == 'user') {
            $id = auth()->id();
            $months = cons()->valueLang('sign.expire_amount');
        } else {
            $id = $request->input('id');
            $months = cons()->valueLang('sign.worker_expire_amount');
        }

        if (!($cost = array_search($month, $months))) {
            return $this->error('选择的时间不正确');
        }

        $wechatPay = app('wechat.pay');

        $result = $wechatPay->userExpireQrCode($id, $cost, $type);

        if ($result['dealCode'] != 10000) {
            return $this->error($result['dealMsg']);
        }

        return $this->success(['code_url' => $result['codeUrl'], 'created_at' => (string)Carbon::now()]);
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