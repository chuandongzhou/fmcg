<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/28
 * Time: 10:05
 */
namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use DB;
use Pingpp\Charge;
use Pingpp\Pingpp;

class PayController extends Controller
{
    /**
     * @param $orderId
     * @return \Illuminate\View\View
     */
    public function pay($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return $this->error('订单不存在');
        }
        if ($order->pay_type != cons('pay_type.online')) {
            return $this->error('此订单不是在线支付订单');
        }
        $orderConfig = cons('order');

        if ($order->pay_status != $orderConfig['pay_status']['non_payment']) {
            return $this->error('此订单已付款');
        }

        //配置extra

        Pingpp::setApiKey('sk_live_8izjnHmf9mPG4aTOWL0yvbv9');

        $extra = array(
            'product_category' => '1',
            'identity_id' => '' . auth()->id() . '',
            'identity_type' => 2,
            'terminal_type' => 3,
            'terminal_id' => auth()->id(),
            'user_ua' => 'Mozilla/5.0 (iPod touch; CPU iPhone OS 7_1_2 like Mac OS X) AppleWebKit/537.51.2 (KHTML, like Gecko) Mobile/11D257',
            'result_url' => 'http://fmcg.com/order-buy'
        );

        $charge = Charge::create(
            array(
                'subject' => 'Your Subject',
                'body' => 'Your Body',
                'amount' => 1,
                'order_no' => $orderId,
                'currency' => 'cny',
                'extra' => $extra,
                'channel' => 'yeepay_wap',
               // 'client_ip' => $_SERVER['REMOTE_ADDR'],
                'client_ip' => '125.71.162.123',
                'app' => array('id' => 'app_1mH8m59WrrDCHSqb')
            )
        )->__toArray(true);
        return $this->success($charge);
    }

}