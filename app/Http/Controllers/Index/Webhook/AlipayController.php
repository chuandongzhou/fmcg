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
use App\Models\SystemTradeInfo;
use App\Services\PayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use WeiHeng\Alipay\AlipayNotify;
use DB;

class AlipayController extends Controller
{

    public function anySuccess(Request $request)
    {
        info($request->all());
        //计算得出通知验证结果
        $alipayConf = getAlipayConfig();

        $alipayNotify = new AlipayNotify($alipayConf);
        $verify_result = $alipayNotify->verifyNotify();

        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            $orderId = $request->input('out_trade_no');     //商户订单号
            $tradeNo = $request->input('trade_no');         //支付宝交易号
            $amount = $request->input('total_fee');         //交易金额
            //TODO: 订单手续费暂定千分之六
            $orderFee = sprintf("%.2f", $amount * 6 / 1000);
            $sign = $request->input('sign');                //签名
            $field = $request->input('extra_common_param');

            $orders = Order::where($field, $orderId)->get()->each(function ($order) {
                $order->setAppends([]);
            });

            if ($request->input('trade_status') == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($request->input('trade_status') == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //付款完成后，支付宝系统发送该交易状态通知

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            $result = (new PayService)->addTradeInfo($orders, $amount, $orderFee, $tradeNo, 'alipay_pc', $sign);

            return $result ? "success" : '';


            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        } else {
            //验证失败
            return "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

    public function anyRefund(Request $request)
    {
        info($request->all());
        $alipayConf = getAlipayConfig('refund');
        $alipayNotify = new AlipayNotify($alipayConf);
        $verify_result = $alipayNotify->verifyNotify();

        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //批次号
            $batchNo = $this->input('batch_no');

            //批量退款数据中转账成功的笔数
            $successNum = $this->input('success_num');

            //批量退款数据中的详细信息
            $resultDetails = $this->input('result_details');

            $result = DB::transaction(function () use ($resultDetails) {
                $refundDatas = (new PayService)->formatAlipayRefundData($resultDetails);
                if ($refundDatas && !empty($refundDatas)) {
                    $tradeNos = array_keys($refundDatas);
                    $orderIds = SystemTradeInfo::whereIn('trade_no', $tradeNos)->get()->pluck('order_id');
                    $orders = Order::whereIn('id', $orderIds)->get();
                    foreach ($orders as $order) {
                        if ($order->fill([
                            'pay_status' => cons('order.pay_status.refund_success'),
                            'refund_at' => Carbon::now()
                        ])->save()
                        ) {
                            $order->orderRefund()->increment('refunded_amount', $refundDatas[$order->id]);
                        }
                    }
                }
                return true;
            });


            //判断是否在商户网站中已经做过了这次通知返回的处理
            //如果没有做过处理，那么执行商户的业务程序
            //如果有做过处理，那么不执行商户的业务程序

            return $result ? "success" : 'fail';        //请不要修改或删除

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            return "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }
}