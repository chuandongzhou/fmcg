<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2017/12/18
 * Time: 10:36
 */

namespace WeiHeng\ULinePay;

use App\Models\Order;
use GuzzleHttp\Client;

class ULinePay
{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }


    /**
     *  支付
     *
     * @param \App\Models\Order $order
     * @return mixed|\SimpleXMLElement
     */
    public function pay(Order $order)
    {
        $payData = $this->_getPayData($order);

        $payData['sign'] = $this->_getSign($payData);

        $openApiUrl = $this->_buildRequestUrl($this->config['open_api_url']);
        $client = new Client();
        $request = $client->post(
            $openApiUrl,
            [
                'headers' => ['Content-Type' => 'text/xml; charset=UTF8'],
                'body' =>$this->_arrayToXml($payData)
            ]
        );

        $content = $request->getBody()->getContents();

        return xml_to_array($content);
    }

    /**
     * 获取支付接口参数
     *
     * @param \App\Models\Order $order
     * @return array
     */
    public function _getPayData(Order $order)
    {
        $options = [
            'mch_id' => $this->config['mch_id'],
            'nonce_str' => $this->_getNonceStr(),
            'body' => $order->shop_name,
            'out_trade_no' => $order->id . '',
            'total_fee' => bcmul($order->after_rebates_price, 100),
            'spbill_create_ip' => $this->_getUserIp(),
            'notify_url' => $this->config['notify_url'],
            'trade_type' => $this->config['trade_type'],
            'product_id' => $this->_getProductId($order)
        ];

        return $options;
    }

    /**
     * 获取签名
     *
     * @param $options
     * @return string
     */
    public function _getSign($options)
    {
        //除去待签名参数数组中的空值和签名参数
        $paraFilter = paraFilter($options);

        //对待签名参数数组排序
        $paraSort = argSort($paraFilter);

        //转换为字符串
        $paraString = createLinkstring($paraSort);

        return $this->_md5Encrypt($paraString, $this->config['secret_key']);

    }


    /**
     * 获取32位随机字符串
     *
     * @return string
     */
    private function _getNonceStr()
    {
        return md5('dingbaida');
    }

    /**
     * 获取用户ip
     *
     * @return string
     */
    private function _getUserIp()
    {
        return request()->ip();
    }

    /**
     * 订单第一个商品id
     *
     * @param \App\Models\Order $order
     * @return string
     */
    private function _getProductId(Order $order)
    {
        return (string)$order->orderGoods()->pluck('id');
    }

    /**
     * 加密
     *
     * @param $paraString
     * @param $secretKey
     * @return string
     */
    private function _md5Encrypt($paraString, $secretKey)
    {
        $paraString = $paraString . '&key=' . $secretKey;

        return strtoupper(md5($paraString));
    }

    /**
     * 获取请求地址
     *
     * @param $openApiUrl
     * @param string $type
     * @return string
     */
    private function _buildRequestUrl($openApiUrl, $type = 'wechat')
    {
        $subUrl = '';
        if ($type == 'wechat') {
            $subUrl = '/wechat/orders';
        }

        return $openApiUrl . $subUrl;
    }

    /**
     * 转换为xml
     *
     * @param $array
     * @return mixed
     */
    private function _arrayToXml($array)
    {
        return array_to_xml($array,
            new \SimpleXMLElement('<?xml version=\'1.0\' encoding=\'utf-8\'?><xml />'))->asXML();
    }
}