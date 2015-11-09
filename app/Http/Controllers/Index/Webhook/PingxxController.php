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
        $pub_key_path = __DIR__ . "/rsa_public_key.pem";
        // 验证 webhooks 签名
        $result = verify_signature($raw_data, $signature, $pub_key_path);

        info($result);


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

                $field = $orderInfo->description; //

                $orders = Order::where($field, $orderInfo->order_no)->get();
                $amount = $orderInfo->amount;
                //TODO: 订单手续费
                $orderFee = $amount - $orderInfo->amount_settle;
                $tradeNo = $orderInfo->transaction_no;

                //修改订单状态以及添加交易信息
                (new PayService)->addTradeInfo($orders, $amount, $orderFee,'pingxx', $tradeNo);


                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                break;
            case "refund.succeeded":
                // 开发者在此处加入对退款异步通知的处理代码
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                break;
            default:
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                break;
        }
    }
}