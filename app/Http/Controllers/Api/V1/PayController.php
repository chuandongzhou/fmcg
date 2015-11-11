<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/28
 * Time: 10:05
 */
namespace App\Http\Controllers\Api\V1;

use App\Models\Order;
use App\Models\Shop;
use App\Models\SystemTradeInfo;
use DB;
use Gate;
use Illuminate\Http\Request;
use Pingpp\Charge;
use Pingpp\Pingpp;

class PayController extends Controller
{
    public function __construct()
    {
        Pingpp::setApiKey('sk_test_ynfvfL4084KSz9eHOSaXf9OC');
    }

    /**
     * 支付
     *
     * @param \Illuminate\Http\Request $request
     * @param $orderId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function charge(Request $request, $orderId)
    {
        $type = $request->input('type');
        $field = $type == 'all' ? 'pid' : 'id';
        $orders = Order::where($field, $orderId)->get();
        if ($orders->isEmpty()) {
            return $this->error('获取失败，请重试');
        }

        if (Gate::denies('validate-online-orders', $orders)) {
            return $this->error('获取失败，请重试');
        }
        $orderIds = $orders->pluck('shop_id')->all();

        $orderNames = Shop::whereIn('id', $orderIds)->get()->implode('name', ',');

        //配置extra

        //Pingpp::setApiKey('sk_live_8izjnHmf9mPG4aTOWL0yvbv9');

        $extra = array(
            'product_category' => '1',
            'identity_id' => auth()->id() . '',
            'identity_type' => 2,
            'terminal_type' => 3,
            'terminal_id' => auth()->id() . '',
            'user_ua' => $request->server('HTTP_USER_AGENT'),
            'result_url' => url('api/v1/pay/success-url')
        );
        $charge = Charge::create(
            array(
                'subject' => '成都订百达科技有限公司',
                'body' => $orderNames,
                'amount' => ($orders->pluck('price')->sum()) * 100,   //单位为分
                'order_no' => $orderId,
                'currency' => 'cny',
                'extra' => $extra,
                'channel' => 'yeepay_wap',
                'client_ip' => $request->server('REMOTE_ADDR'),
                'description' => $type,
                'app' => array('id' => 'app_1mH8m59WrrDCHSqb')
            )
        )->__toArray(true);

        return $this->success($charge);
    }

    public function refundCharge(Request $request, $orderId)
    {
        $amount = $request->input('amount');
        $order = Order::where('user_id', auth()->id())->find($orderId);

        $tradeConf = cons('trade');
        if (!$order || $order->pay_type != cons('pay_type.online') || $order->pay_status != $tradeConf['pay_status']['success']) {
            return $this->error('订单不存在');
        }
        $tradeId = SystemTradeInfo::where('order_id', $orderId)->select([
            'pay_type',
            'amount',
            'charge_id'
        ])->first();
        dd($tradeId);
    }

    /**
     * 移动端页面跳转
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function successUrl()
    {
        return redirect('dbdfmcg://pingppwappay?result=success');
    }

}