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
use Gate;

class UnionPayController extends Controller
{

    public function anySuccess(Request $request)
    {

        $datas = $request->all();
        /*$datas = array (
            'Data' => 'K40RplFF04dhrWNpK6haeBRNLZsjSVHMuebL5QwLSB-NsaYuvI6GuP9ykPblgLYJPUaxhAxzfkeFEeVtCrT4nPMEAy4lt1rkhZXgbJcqGODSuhqwPnNKPjSVmf4oNbYPUjyBXD4g5Bj_Cdr6vdu409zMbCUWZVwUuTiTIDJOQMS-Oq-W_i9Z7TFqSSwKwM8Lo3Mej4HULv2CC755QiW0OiGo84qaP8swp6zlnFF0lhn-n75khlcpIBBQUuZqU3OWQtKovHuQI0DcCEuZs76PeoOexeG6xPFwd3kxrixEvE_ugc-iudJx_Yhla9JqEEeZPNCWxFbw0SF60qGv8JY6zRl5p9TGQiLA3CLdlEHccRMdtdyu1g_t13o5ABkX6IY8KDlti-gZresNjSspiGKb3-7qTmcEiPZvhDIrv18jCEwUV0e5W--Gbm1uvSuULXZw6OPvRgcbFXD5i-0hcAkBxo59E37Tj_yr0-NnDfIlq2yGwWMBO4Bl0_qLI9tAN7p57WuSHBkvVAF7mgs3DDZXhfoTq3c9pmTcJWvWZmYOCs1Cg-fKXQs1W1gBdK689yFlPTbMOo1b0BxplIsCOGeOS-q9a5FrksZIY2WB44EbC_5xxpyCPHZ2uO866EjeItHXQLW5lY1oNdYhg0L4yydi1gYorMvfK6wXA0SubdO4XnHcDO4vrvZfegwfXi3lpp3Uh0XxEHwwpVagvdhReVR6-4EF2yvRqEvN4C5x_t32RkIGHGE6etC5UmCbYSdpV0VQgYhLT8lk81u4rkurioE6JPeBsSkMGaNJgc4cNdsdL4juOeKL1EikcyyIc3UZDs16GG8dn1UmxAWoS9RLJ4nwcwyH9oHcTXpFIPj1uqnCv9CqnGNjJ4avJoDbOkl9tWeebNmZCmRBtegFQft9mI39PcSp2JmSpwk63vNxH7hR-HQg2aGjeCFVQhKDiezcA--1QdxOK70qNO4lY5bcVZtEgwB_mSkJxoiVePTMeYJ2BHcdxnvvJknX0QQXrIFErMKx-t4UhqozpoNLlPXFeH74oZ0_JNy-DNJOvaHvzFmCwu6MEYXuaWm3gJOZnxWh1F5S',
            'Sign' => 'd2ad5bd58d205a8c812f79b59f1e1dc7',
            'Method' => 'paymentreport',
            'Appid' => '400445348',
        );*/

        $unionPay = app('union.pay');

        //验证appid
        if (array_get($datas, 'Appid') !== $unionPay->getAuthConfig()['app_id']) {
            return $this->_buildResponse('公司id错误', '01');
        }

        //验证sign
        if (!$unionPay->validateSign($sign = array_get($datas, 'Sign'), $data = array_get($datas, 'Data'))) {
            return $this->_buildResponse('Sign验证失败', '02');
        }

        $method = array_get($datas, 'Method');

        $reportData = $unionPay->decodeData($data);

        if ($method == 'paymentreport') {
            $result = $this->_payReport($reportData, $sign);
        } else {
            $result = false;
        }

        return $result ? $this->_buildResponse('成功', '00') : $this->_buildResponse('失败', '03');

    }

    /**
     * 支付回调数据处理
     *
     * @param $reportData
     * @param $sign
     * @return mixed
     */
    private function _payReport($reportData, $sign)
    {

        $orders = Order::where('id', $reportData['ordernumber'])->get();

        //验证是否可支付
        if (!$orders->first()->can_payment) {
            return false;
        }

        $amount = bcdiv($reportData['amount'], 100, 4);

        $orderFee = sprintf("%.2f", $amount * 3 / 1000);

        $tradeNo = array_get($reportData, 'payorderid');


        return (new PayService())->addTradeInfo($orders, $amount, $orderFee, $tradeNo, 'union-pay', $sign);
    }


    /**
     * 退款处理
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function anyRefund(Request $request)
    {

    }

    /**
     * 返回
     *
     * @param $message
     * @param $response
     * @return string
     */
    private function _buildResponse($message, $response)
    {
        return json_encode([
            'massage' => $message,
            'response' => $response
        ]);
    }
}