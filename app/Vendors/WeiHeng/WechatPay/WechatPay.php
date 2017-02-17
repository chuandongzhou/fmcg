<?php

namespace WeiHeng\WechatPay;


use App\Models\Order;
use App\Models\SystemTradeInfo;
use App\Models\WechatPayUrl;
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
            'bgUrl' => url('api/v1/wechat-pay/pay-result'),
            'version' => 'V1.0',
            'payChannelCode' => 'CX_WX',
            'orderNo' => $order->id,
            'orderAmount' => (int)bcmul($order->price, 100),
            'curCode' => 'CNY',
            'orderTime' => str_replace(['-', ':', ' '], '', $order->created_at),
            'orderTimestamp' => Carbon::now()->format('YmdHis'),
            'productName' => $order->shop_name,
            'productDesc' => '订单号：' . $order->id . ' 金额：' . $order->price
        ];
        $sign = $this->getSign($options);
        return array_add($options, 'sign', $sign);
    }

    /**
     * 获取sign
     *
     * @param array $options
     * @return string
     */
    public function getSign($options = [])
    {
        $options = $this->paraFilter($options);
        $options = $this->argSort($options);
        $queryString = $this->buildQueryString($options);
        return strtoupper(md5($queryString . $this->config['key']));
    }

    /**
     * 验证sign
     *
     * @param array $options
     * @return bool
     */
    public function verifySign($options = [])
    {
        return isset($options['sign']) && strtoupper($options['sign']) === $this->getSign($options);
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
}