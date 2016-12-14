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
        $order = Order::with('wechatPayCode')->find($orderId);
        if (Gate::denies('validate-payment-orders', $order)) {
            return $this->error('订单不存在或已支付');
        }

        if (!is_null($wechatPayCode = $order->wechatPayCode)) {
            //有二维码时直接返回

            $nowTime = Carbon::now();
            if ($nowTime->diffInHours($wechatPayCode->created_at) >= 2) {
                return $this->error('二维码已过期,请选择其它渠道');
            } else {
                return $this->success([
                    'deal_code' => $wechatPayCode->deal_code,
                    'created_at' => $wechatPayCode->created_at
                ]);
            }
        }

        $wechatPay = app('wechat.pay');

        $result = $wechatPay->getQrCode($order);

        if (!$wechatPay->verifySign($result)) {
            return $this->error('请求出错，请重试');
        }
        if ($result['dealCode'] != 10000) {
            return $this->error($result['dealMsg']);
        }

        return $wechatPay->created($result, $orderId) ? $this->success([
            'deal_code' => $result['deal_code'],
            'created_at' => Carbon::now()
        ]) : $this->error('创建二维码时出现问题');
    }

    /**
     * 支付成功回调
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function payResult(Request $request)
    {
        $data = $request->all();
        info($data);

        $wechatPay = app('wechat.pay');
        if (!$wechatPay->verifySign($data)) {
            info('微信支付回调错误：');
            info($data);
            return $this->success($wechatPay->buildResponse(false));
        }

        $orders = Order::whereId($data['orderNo'])->get()->each(function ($order) {
            $order->setAppends([]);
        });

        $result = (new PayService())->addTradeInfo($orders, bcdiv($data['orderAmount'], 100, 2),
            bcdiv($data['fee'], 100, 2), $data['cxOrderNo'], 'wechat_pay', $data['sign']);

        return $this->success($wechatPay->buildResponse($result === true));

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