<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/28
 * Time: 10:05
 */
namespace App\Http\Controllers\Api\V1;

use App\Models\DeliveryMan;
use App\Models\Order;
use App\Services\PayService;
use Carbon\Carbon;

class PosController extends Controller
{

    public function anyIndex()
    {
        $xmlData = file_get_contents("php://input");
        $hmac = $this->_hmac($xmlData);
        $ms = xml_to_array($xmlData);
        $posConf = cons('pos.service_code');
        switch ($ms['SessionHead']['ServiceCode']) {
            case $posConf['login'] :
                //登录
                return $this->login($ms, $hmac);
                break;
            case $posConf['retrieve'] :
                //查询
                return $this->retrieve($ms, $hmac);
                break;
            case $posConf['pay'] :
                //付款
                return $this->pay($ms, $hmac);
                break;
            case $posConf['receive'] :
                //签收
                return $this->receive($ms, $hmac);
                break;
            case $posConf['cancel'] :

                break;
            case $posConf['refund'] :

                break;
        }

    }

    /**
     * pos机登录
     *
     * @param $data
     * @param $hmac
     * @return mixed
     */
    protected function login($data, $hmac)
    {
        $head = $data['SessionHead'];
        $body = $data['SessionBody'];
        $resultCodeArr = cons('pos.result_code');

        $array = [
            'SessionHead' => array_except($head, ['ReqTime', 'HMAC'])
        ];
        if ($hmac != $head['HMAC']) {
            //报文验证
            $array['SessionHead']['ResultCode'] = $resultCodeArr['hmac_error'];
            $array['SessionHead']['ResultMsg'] = cons()->valueLang('pos.result_code',
                $resultCodeArr['hmac_error']);

        } else {
            //验证账户名和密码
            $user = DeliveryMan::where(['pos_sign' => $body['PosSn'], 'user_name' => $body['Employee_ID']])->first();
            if (!$user || $user->password !== $body['Password']) {
                $array['SessionHead']['ResultCode'] = $resultCodeArr['password_error'];
                $array['SessionHead']['ResultMsg'] = cons()->valueLang('pos.result_code',
                    $resultCodeArr['password_error']);
            } else {
                //验证成功
                $user->fill(['last_login_at' => Carbon::now()])->save();

                $systemConf = cons('system');
                $array['SessionHead']['ResultCode'] = $resultCodeArr['success'];
                $array['SessionHead']['ResultMsg'] = cons()->valueLang('pos.result_code',
                    $resultCodeArr['success']);
                $array['SessionHead']['ExtendAtt']['Employee_ID'] = $user->id;

                $array['SessionBody']['ExtendAtt'] = [
                    'Employee_Name' => $systemConf['employee_name'],
                    'Company_Code' => $systemConf['company_code'],
                    'Company_Name' => $systemConf['company_name'],
                    'Company_Addr' => $systemConf['company_addr'],
                    'Company_Tel' => $systemConf['company_mobile'],
                ];
            }
        }
        return $this->_posReturn($array);
    }


    /**
     * 订单查询
     *
     * @param $data
     * @param $hmac
     * @return mixed
     */
    protected function retrieve($data, $hmac)
    {
        $head = $data['SessionHead'];
        $body = $data['SessionBody'];
        $resultCodeArr = cons('pos.result_code');
        $orderStatusArr = cons('pos.order_status');

        $array = [
            'SessionHead' => array_except($head, ['ReqTime', 'HMAC'])
        ];
        if ($hmac != $head['HMAC']) {
            //报文验证
            $array['SessionHead']['ResultCode'] = $resultCodeArr['hmac_error'];
            $array['SessionHead']['ResultMsg'] = cons()->valueLang('pos.result_code',
                $resultCodeArr['hmac_error']);

        } else {
            $orderId = $body['OrderNo'];
            $order = Order::where('delivery_man_id', $body['EmployeeID'])->where('pay_type',
                cons('pay_type.cod'))->with('deliveryMan', 'shippingAddress.address',
                'systemTradeInfo')->NonCancel()->find($orderId);
            if (!$order) {
                $array['SessionBody'] = [
                    'OrderStatus' => $orderStatusArr['no_order'],
                    'OrderStatusMsg' => cons()->valueLang('pos.order_status',
                        $orderStatusArr['no_order'])
                ];
            } else {
                $array['SessionHead']['ResultCode'] = $resultCodeArr['success'];
                $array['SessionHead']['ResultMsg'] = cons()->valueLang('pos.result_code',
                    $resultCodeArr['success']);

                $orderStatus = $order->systemTradeInfo ? $order->systemTradeInfo->pay_status : $orderStatusArr['no_pay_no_sign'];

                $array['SessionBody']['Item'] = [
                    'EmployeeID' => $body['EmployeeID'],
                    'OrderNo' => $orderId,
                    'ReceiverOrderNo' => $orderId,
                    'ReceiverName' => $order->shippingAddress->consigner,
                    'RceiverAddr' => $order->shippingAddress->address->address,
                    'RceiverTel' => $order->shippingAddress->phone,
                    'Amount' => $order->price,
                    'OrderStatus' => $orderStatus,
                    'OrderStatusMsg' => cons()->valueLang('pos.order_status', $orderStatus)
                ];
            }

        }
        return $this->_posReturn($array);
    }

    /**
     * 支付
     *
     * @param $data
     * @param $hmac
     * @return mixed
     */
    protected function pay($data, $hmac)
    {
        $head = $data['SessionHead'];
        $body = $data['SessionBody'];
        $resultCodeArr = cons('pos.result_code');
        //$orderStatusArr = cons('pos.order_status');

        $array = [
            'SessionHead' => array_except($head, ['ReqTime', 'HMAC'])
        ];
        if ($hmac != $head['HMAC']) {
            //报文验证
            $array['SessionHead']['ResultCode'] = $resultCodeArr['receive_error'];
            $array['SessionHead']['ResultMsg'] = cons()->valueLang('pos.result_code',
                $resultCodeArr['receive_error']);

        } else {
            $order = Order::where('id', $body['OrderNo'])->get();

            //增加平台交易记录
            $result = (new PayService)->addTradeInfo($order, $body['Amount'], 0, $body['YeepayOrderNo'], 'yeepay',
                $head['HMAC'], $body['ReferNo'], cons('pos.order_status.received_no_sign'), $body['BankCardNo']);

            // TODO: 增加卖家平台余额

            $array['SessionHead']['ResultCode'] = $resultCodeArr['success'];
            $array['SessionHead']['ResultMsg'] = cons()->valueLang('pos.result_code',
                $resultCodeArr['success']);
            $array['SessionBody'] = [
                'OrderNo' => $body['OrderNo'],
                'ReferNo' => $body['ReferNo']
            ];
        }
        return $this->_posReturn($array);
    }

    /**
     * pos机订单签收
     *
     * @param $data
     * @param $hmac
     * @return mixed
     */
    public function receive($data, $hmac)
    {
        $head = $data['SessionHead'];
        $body = $data['SessionBody'];
        $resultCodeArr = cons('pos.result_code');
        //$orderStatusArr = cons('pos.order_status');

        $array = [
            'SessionHead' => array_except($head, ['ReqTime', 'HMAC'])
        ];
        if ($hmac != $head['HMAC']) {
            //报文验证
            $array['SessionHead']['ResultCode'] = $resultCodeArr['receive_error'];
            $array['SessionHead']['ResultMsg'] = cons()->valueLang('pos.result_code',
                $resultCodeArr['receive_error']);

        } else {
            $order = Order::find($body['OrderNo']);
            $trade = $order->systemTradeInfo;
            if (!$order || !$trade || $trade->pay_status != cons('pos.order_status.received_no_sign')) {
                //报文验证
                $array['SessionHead']['ResultCode'] = $resultCodeArr['receive_error'];
                $array['SessionHead']['ResultMsg'] = cons()->valueLang('pos.result_code',
                    $resultCodeArr['receive_error']);
            } else {
                $array['SessionHead']['ResultCode'] = $resultCodeArr['success'];
                $array['SessionHead']['ResultMsg'] = cons()->valueLang('pos.result_code',
                    $resultCodeArr['success']);
                $array['SessionBody'] = [
                    'OrderNo' => $body['OrderNo']
                ];
                $trade->fill(['pay_status' => cons('pos.order_status.signed')])->save();
            }
        }
        return $this->_posReturn($array);
    }


    /**
     * 生成hmac
     *
     * @param string $xml
     * @return string
     */
    private function _hmac($xml = '')
    {
        //去除xml里面的头部以及<COD-MS>
        $pattern = '/<COD\-MS>(.+)<\/COD\-MS>/';
        $key = cons('pos.key');
        preg_match_all($pattern, $xml, $matchs, PREG_PATTERN_ORDER);
        if (empty($matchs[1])) {
            return '';
        }

        $xml = $matchs[1][0];
        //去除xml里面的hmac标签
        $removeHmacPattern = '/<HMAC>.+<\/HMAC>/';
        $xml = preg_replace($removeHmacPattern, '', $xml);
        $hmac = md5($xml . $key);
        return $hmac;
    }


    /**
     * pos机返回数据
     *
     * @param $array
     * @return mixed
     */
    private function _posReturn($array)
    {
        $xml = array_to_xml($array,
            new \SimpleXMLElement('<?xml version=\'1.0\' encoding=\'utf-8\'?><COD-MS />'))->asXML();
        $hmac = $this->_hmac($xml);

        //添加hmac至SessionHead
        $array['SessionHead']['HMAC'] = $hmac;

        return array_to_xml($array,
            new \SimpleXMLElement('<?xml version=\'1.0\' encoding=\'utf-8\'?><COD-MS />'))->asXML();
    }

}