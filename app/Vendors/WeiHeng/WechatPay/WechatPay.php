<?php

namespace WeiHeng\WechatPay;


use App\Models\Order;
use App\Models\SystemTradeInfo;
use App\Models\WechatPayUrl;
use App\Models\Withdraw;
use Carbon\Carbon;
use GuzzleHttp\Client;

class WechatPay
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 创建二维码
     *
     * @param $options
     * @param $orderId
     * @return bool
     */
    public function created($options, $orderId)
    {
        $wechatPayCode = WechatPayUrl::create([
            'order_id' => $orderId,
            'code_url' => $options['codeUrl'],
            'created_at' => Carbon::now()
        ]);
        return $wechatPayCode->exists;

    }

    /**
     * 获取支付二维码地址
     *
     * @param \App\Models\Order $order
     * @return string
     */
    public function getQrCode(Order $order)
    {
        $options = $this->getPayOptions($order);
        $url = $this->config['url'];
        $client = new Client();
        $res = $client->post($url, ['form_params' => $options]);
        $content = $res->getBody()->getContents();
        return $this->_formatResponse($content);
    }

    /**
     * 退款
     *
     * @param \App\Models\SystemTradeInfo $tradeInfo
     * @param string $reason
     * @return array
     */
    public function refund(SystemTradeInfo $tradeInfo, $reason = '')
    {
        $options = $this->getRefundOptions($tradeInfo, $reason);

        $url = $this->config['url'];
        $client = new Client();
        $res = $client->post($url, ['form_params' => $options]);
        $contents = $res->getBody()->getContents();
        return $this->_formatResponse($contents);

    }

    /**
     * 代付
     *
     * @param \App\Models\Withdraw $withdraw
     * @return array
     */
    public function agentPay(Withdraw $withdraw)
    {
        $options = $this->getAgentOptions($withdraw);
        $url = $this->config['agentPayUrl'];
        $client = new Client();
        $res = $client->post($url, ['form_params' => $options]);
        $content = $res->getBody()->getContents();
        return $this->_formatResponse($content);
    }


    /**
     * 帐户续费
     *
     * @param $id
     * @param $cost
     * @param $payChannelCode
     * @param string $backPayType
     * @return bool|string
     */
    public function userExpire($id, $cost, $payChannelCode, $backPayType = 'user')
    {
        $type = array_get($this->config['backPayType'], $backPayType);

        if (!$type) {
            return false;
        }

        $options = $this->getRenewOptions($id, $cost, $payChannelCode, $backPayType);

        return $this->buildRequestForm($options, 'post', $this->config['bankPayUrl']);
    }

    /**
     * 帐户续费微信二维码
     *
     * @param $id
     * @param $cost
     * @param string $backPayType
     * @return bool|string
     */
    public function userExpireQrCode($id, $cost, $backPayType = 'user')
    {
        $type = array_get($this->config['backPayType'], $backPayType);

        if (!$type) {
            return false;
        }

        $options = $this->getRenewQrCodeOptions($id, $cost, $backPayType);
        $url = $this->config['url'];
        $client = new Client();
        $res = $client->post($url, ['form_params' => $options]);
        $content = $res->getBody()->getContents();
        return $this->_formatResponse($content);
    }


    /**
     * 获取退款参数
     *
     * @param $tradeInfo
     * @param $reason
     * @return array
     */
    public function getRefundOptions($tradeInfo, $reason)
    {
        $options = [
            'service' => 'offRefundOrder',
            'merchantNo' => $this->config['merchantNo'],
            'refundNo' => $tradeInfo->id,
            'orderNo' => $tradeInfo->order_id,
            'version' => 'V1.0',
            'curCode' => 'CNY',
            'refundAmount' => bcmul($tradeInfo->amount, 100),
            'refundTime' => Carbon::now()->format('YmdHis'),
            'refundDesc' => $reason,

        ];
        $sign = $this->getSign($options);
        return array_add($options, 'sign', $sign);
    }

    /**
     * 获取支付参数
     *
     * @param $order
     * @return array
     */
    public function getPayOptions($order)
    {
        $options = [
            'service' => 'getCodeUrl',
            'merchantNo' => $this->config['merchantNo'],
            'bgUrl' => url('webhooks/wechat/pay-result'),
            'version' => 'V1.0',
            'payChannelCode' => 'CX_WX',
            'orderNo' => $order->id,
            'orderAmount' => (int)bcmul($order->price, 100),
            'curCode' => 'CNY',
            'orderTime' => str_replace(['-', ':', ' '], '', $order->created_at),
            'orderTimestamp' => Carbon::now()->format('YmdHis'),
            'productName' => $order->shop_name . ' - 订单号：' . $order->id,
            'productDesc' => ' 金额：' . $order->price
        ];
        $sign = $this->getSign($options);
        return array_add($options, 'sign', $sign);
    }

    /**
     * 获取代付参数
     *
     * @param \App\Models\Withdraw $withdraw
     * @return array
     */
    public function getAgentOptions(Withdraw $withdraw)
    {
        $options = [
            'service' => 'payForAnotherOne',
            'merchantNo' => $this->config['merchantNo'],
            'orderNo' => $withdraw->id,
            'version' => 'V1.0',
            'accountProp' => 1,
            'accountNo' => base64_encode($withdraw->card_number),
            'accountName' => base64_encode($withdraw->card_holder),
            'bankGenneralName' => cons()->valueLang('bank.type', $withdraw->card_type),
            'bankName' => $withdraw->bank_name,
            'bankCode' => cons()->key('bank.type', $withdraw->card_type),
            'currency' => 'CNY',
            'bankProvcince' => $withdraw->bank_province,
            'bankCity' => $withdraw->bank_city,
            'orderAmount' => bcmul($withdraw->amount, 100),
            'orderTime' => Carbon::now()->format('YmdHis'),
            'notifyUrl' => url('webhooks/wechat/agent-result'),
        ];
        $sign = $this->getSign($options);
        return array_add($options, 'sign', $sign);
    }

    /**
     * 续费参数
     *
     * @param $id
     * @param $cost
     * @param $payChannelCode
     * @param string $bankPayType
     * @return array
     */
    public function getRenewOptions($id, $cost, $payChannelCode, $bankPayType = 'user')
    {
        if ($bankPayType == 'user') {
            $pageUrl = url('personal/sign/renew');
        } elseif ($bankPayType == 'delivery') {
            $pageUrl = url('personal/delivery-man');
        } else {
            $pageUrl = url('business/salesman');
        }

        $options = [
            'service' => 'bankPay',
            'merchantNo' => $this->config['bankPayMerchantNo'],
            'pageUrl' => $pageUrl,
            'bgUrl' => 'http://dingbaida.com/webhooks/wechat/renew-result',//url('webhooks/wechat/renew-result'),
            'version' => 'V1.0',
            'payChannelCode' => $payChannelCode,
            'payChannelType' => 1,                  // 暂时只支持银行卡
            'orderNo' => $this->_getRenewOrderId($id),
            'orderAmount' => bcmul($cost, 100),
            'curCode' => 'CNY',
            'productName' => '成都订百达科技有限公司',
            'orderTime' => Carbon::now()->format('YmdHis'),
            'ext1' => $id,                  // 扩展字段1  用作用户id
            'ext2' => $bankPayType,         // 扩展字段2  用作用户类型  （user 为登录帐号， delivery  司机，  salesman  业务员）
        ];
        $sign = $this->getSign($options, $this->config['bankPayKey']);
        return array_add($options, 'sign', $sign);
    }

    /**
     * 续费二维码参数
     *
     * @param $id
     * @param $cost
     * @param string $bankPayType
     * @return array
     */
    public function getRenewQrCodeOptions($id, $cost, $bankPayType = 'user')
    {
        $options = [
            'service' => 'getCodeUrl',
            'merchantNo' => $this->config['bankPayMerchantNo'],
            'bgUrl' => 'http://dingbaida.com/webhooks/wechat/renew-result',//url('webhooks/wechat/renew-result'),
            'version' => 'V1.0',
            'payChannelCode' => 'CX_WX',
            'orderNo' => $this->_getRenewOrderId($id),
            'orderAmount' => bcmul($cost, 100),
            'curCode' => 'CNY',
            'productName' => '成都订百达科技有限公司',
            'orderTime' => Carbon::now()->format('YmdHis'),
            'ext1' => $id,                  // 扩展字段1  用作用户id
            'ext2' => $bankPayType,         // 扩展字段2  用作用户类型  （user 为登录帐号， delivery  司机，  salesman  业务员）
        ];
        info($options);
        $sign = $this->getSign($options, $this->config['bankPayKey']);
        return array_add($options, 'sign', $sign);
    }

    /**
     * 获取sign
     *
     * @param array $options
     * @param string $key
     * @return string
     */
    public function getSign($options = [], $key = null)
    {
        $key = $key ?: $this->config['key'];

        $options = $this->paraFilter($options);
        $options = $this->argSort($options);
        $queryString = $this->buildQueryString($options);
        return strtoupper(md5($queryString . $key));
    }

    /**
     * 验证sign
     *
     * @param array $options
     * @param string $key
     * @return bool
     */
    public function verifySign($options = [], $key = null)
    {
        $key = $key ?: $this->config['key'];
        return isset($options['sign']) && strtoupper($options['sign']) === $this->getSign($options, $key);
    }

    /**
     * 创建字符串
     *
     * @param $options
     * @return string
     */
    public function buildQueryString($options)
    {
        $arg = "";
        while (list ($key, $val) = each($options)) {
            $arg .= $key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    /**
     * 字符串过滤
     *
     * @param $para
     * @return array
     */
    public function paraFilter($para)
    {
        $para_filter = array();
        while (list ($key, $val) = each($para)) {
            if ($key == "sign" || $key == "sign_type" || $val == "") {
                continue;
            } else {
                $para_filter[$key] = $para[$key];
            }
        }
        return $para_filter;
    }

    /**
     * 创建支付成功返回数据
     *
     * @param bool $result
     * @return array
     */
    public function buildResponse($result = true)
    {
        $response = [
            'merchantNo' => $this->config['merchantNo'],
            'dealResult' => $result ? 'SUCCESS' : 'FAIL'
        ];

        return array_add($response, 'sign', $this->getSign($response));
    }

    /**
     * 排序
     *
     * @param $para
     * @return mixed
     */
    public function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * 建立请求
     *
     * @param $param
     * @param $method
     * @param $buttonName
     * @param $action
     * @return string
     */
    public function buildRequestForm($param, $method, $action, $buttonName = '确认')
    {

        $sHtml = "<form id='bankSubmit' name='bankSubmit' action='" . $action . "' method='" . $method . "'>";
        while (list ($key, $val) = each($param)) {
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }

        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml . "<input type='submit'  value='" . $buttonName . "' style='display:none;'></form>";

        $sHtml = $sHtml . "<script>document.forms['bankSubmit'].submit();</script>";

        return $sHtml;
    }

    /**
     * 格式化
     *
     * @param $result
     * @return array
     */
    private function _formatResponse($result)
    {
        info((array)json_decode($result));
        return (array)json_decode($result);
    }

    /**
     * 获取订单id
     *
     * @param $id
     * @return string
     */
    private function _getRenewOrderId($id)
    {
        return Carbon::now()->timestamp . random_int(0, 10000) . $id;
    }
}