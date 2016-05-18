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
use App\Services\PayService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PingxxController extends Controller
{

    /**
     * pingxx支付成功回调
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function postSuccess(Request $request)
    {
        $raw_data = file_get_contents('php://input');
        $signature = $request->server('HTTP_X_PINGPLUSPLUS_SIGNATURE');
        $pub_key_path = storage_path('pingxx_pem/rsa_public_key.pem');
        // 验证 webhooks 签名
        $result = verify_signature($raw_data, $signature, $pub_key_path);

        if ($result !== 1) {
            return $this->error('verification failed');
        }

        $event = json_decode($raw_data);

// 对异步通知做处理
        if (!isset($event->type)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            exit("fail");
        }
        switch ($event->type) {
            case "charge.succeeded":
                // 开发者在此处加入对支付异步通知的处理代码
                $orderInfo = $event->data->object;
                $type = $orderInfo->description;
                $field = $type == 'all' ? 'pid' : 'id';

                $chargeId = $orderInfo->id;

                $channel = $orderInfo->channel;

                $orders = Order::where($field, $orderInfo->order_no)->get()->each(function ($order) {
                    $order->setAppends([]);
                });
                $amount = $orderInfo->amount / 100; //单位为分
                //TODO: 订单手续费暂定千分之六
                $orderFee = sprintf("%.2f", $amount * 6 / 1000);
                $tradeNo = $orderInfo->transaction_no;

                //修改订单状态以及添加交易信息

                $result = (new PayService)->addTradeInfo($orders, $amount, $orderFee, $tradeNo, $channel, '',
                    $chargeId);
                if ($result) {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                } else {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                }
                break;
            case "refund.succeeded":
                // 开发者在此处加入对退款异步通知的处理代码
                $orderInfo = $event->data->object;
                $orderId = $orderInfo->metadata->order_no;
                $order = Order::find($orderId);
                $order->setAppends([]);
                if ($order->fill([
                    'pay_status' => cons('order.pay_status.refund_success'),
                    'refund_at' => Carbon::now()
                ])->save()
                ) {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                } else {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                }
                break;
            default:
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                break;
        }
    }
}