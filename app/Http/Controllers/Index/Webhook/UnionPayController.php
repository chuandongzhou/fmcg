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
use App\Services\OrderService;
use App\Services\PayService;
use Illuminate\Http\Request;

class UnionPayController extends Controller
{

    public function anySuccess(Request $request)
    {

        $datas = array(
            'Data' => 'K40RplFF04dhrWNpK6haeBRNLZsjSVHMuebL5QwLSB_qJj-ubOZUkiD4YC-fP54VkigAbYOVUF14n_8vdoHJJx5BtEJwM1kcb8T0xTXxcAD4YlNsdHCVvA3s706JQpqQqXE-Uz8TvEO_J1d4CKkJrhu6jtbexwq-KB-jGmrsklYcxSSPk7edB2vYL8FiXg7fnOd9HjJdjIyhUVMefWrykg5VyNS5nqjmdF1-5SJI3_EqxSDiIsQPvsfhXCv1gFsyVZVg8pttLT_l21kMfhpJSEPPaftpFpWqGZ3-LSdO_UC2mSYKlwb1ltD2LJGluTXIGJWDyr94x5q4UyhsC4Pyn0qCTeFtrqW0iUVDfBt9Rjbpy7J7CArcxuFcBkuS6hXEBz1fYymQneczVg-sgT-Nzzwf8jLmRk9dQNBaq22ePAeCfauJj7ElS8LW9Ftr-S37A1qN3Wqlg_bET3MvMHq_sTICPGOrJqebJ-Bf8uqUKS9w4uxLgUmLE_d8jtvkpSY-QxfOv0r6nr3CSGkCQfuPxZa6-gsAskjrL1bYCi1yv1FYLceA-tLXtwmvIcgb0NumQfTo_dYzRvI4o33fDgfOeuSgzl7PIiDiUkR3e53OCZutj7T95W_YWzKTLL_AnlfqlQKMC3v-USMtJM6GL90W2nG0XtkJhnIiJNc9bnZlFI-_B9pnHGGxTkq_dhSBZJ8m8-_kPH0ma6bGhZ0CEvXhT3K53jFUXhb445tCnozBE6gfZFnFenTb12gMCXdzVuadCHkJ2jit2_5UL2WhM3Wu6gERVrioZTeugJ7sHtxWROE7X0U2FgUkvLIBago8xFFycn2QcxXXKVtkKW7aM6k4QSvtZqigM_NcyEQnvWNW52GE_GIriusX8XgNI2TU7t3XHDZQb4fzGCnfpP8uXKqfs8Qq85S7UgoO2SApW9FFR_PkA_hRu0UQGQ7GQkYcKvG1L2Ff7X1Y25K_mqUjeQH8aU3RPcxBnF7rMr4wiI8fIROlN_TOoIKF_1CvDNigGGkYAW-1QZglY54O0jekIIyNAc8vgLG5eHSJWXvpCbzQoAA=',
            'Sign' => '42f472ed94909c27babe0b12196a2590',
            'Method' => 'paymentreport',
            'Appid' => '400445348',
        );
        $unionPay = app('union.pay');

        //验证appid
        if (array_get($datas, 'Appid') !== config('union-pay')['app_id']) {
            return $this->_buildResponse('公司id错误', '01');
        }


        //验证sign
        if (!$unionPay->validateSign($sign = array_get($datas, 'Sign'), $data = array_get($datas, 'Data'))) {
            return $this->_buildResponse('Sign验证失败', '02');
        }

        $method = array_get($datas, 'method');

        $reportData = $unionPay->decodeData($data);

        dd($reportData);

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

        $amount = $reportData['amount'];

        $orderFee = sprintf("%.2f", $amount * 6 / 1000);

        $tradeNo = $reportData['channelordernumber'];


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