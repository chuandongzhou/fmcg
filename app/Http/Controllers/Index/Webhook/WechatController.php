<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/11/6
 * Time: 14:56
 */

namespace App\Http\Controllers\Index\Webhook;

use App\Http\Controllers\Index\Controller;
use App\Models\Order;
use App\Models\Withdraw;
use App\Services\PayService;
use App\Services\RedisService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class WechatController extends Controller
{


    /**
     * 代付成功回调
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function anyAgentResult(Request $request)
    {
        $data = $request->all();


        if (!app('wechat.pay')->verifySign($data)) {
            return 'FAIL';
        }
        $orderNo = array_get($data, 'orderNo');

        $withdrawConf = cons('withdraw');

        $withdraw = Withdraw::where('status', $withdrawConf['pass'])->find($orderNo);

        if (is_null($withdraw)) {
            return 'SUCCESS';
        }

        if (array_get($data, 'dealCode') == '10000') {
            $withdraw->fill([
                'trade_no' => $data['cxOrderNo'],
                'status' => cons('withdraw.payment'),
                'payment_at' => Carbon::now()
            ])->save();

            $this->_pushSuccessMsg($withdraw, $data);
            return 'SUCCESS';
        }
        return 'FAIL';
    }

    /**
     * 支付成功回调
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|string|\Symfony\Component\HttpFoundation\Response
     */
    public function anyPayResult(Request $request)
    {
        $data = $request->all();

        $wechatPay = app('wechat.pay');
        if (!$wechatPay->verifySign($data)) {
            //info('微信支付回调错误：' . $request->server('ip'));
            return 'FAIL';
        }

        $orders = Order::whereId($data['orderNo'])->get();

        $fee = isset($data['fee']) ? $data['fee'] : $data['orderAmount'] * 7 / 1000;
        $result = (new PayService())->addTradeInfo($orders, bcdiv($data['orderAmount'], 100, 2),
            bcdiv($fee, 100, 2), $data['cxOrderNo'], 'wechat_pay', $data['sign']);

        return $result === true ? 'SUCCESS' : 'FAIL';

    }

    /**
     * 发送提现成功短信
     *
     * @param $withdraw
     * @param $data
     */
    private function _pushSuccessMsg($withdraw, $data)
    {
        //启动通知
        $redisKey = 'push:withdraw:' . $withdraw->user_id;
        $redisVal = '您的提现订单:' . $withdraw->id . ',' . cons()->lang('push_msg.review_payment');

        (new RedisService)->setRedis($redisKey, $redisVal, cons('push_time.msg_life'));

        app('pushbox.sms')->send('withdraw', $withdraw->user->backup_mobile,
            [
                'withdraw_id' => $data['orderNo'],
                'trade_no' => $data['cxOrderNo'],
            ]);
    }
}