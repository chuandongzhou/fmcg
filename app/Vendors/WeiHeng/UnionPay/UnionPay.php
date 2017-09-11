<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2017/7/31
 * Time: 14:52
 */

namespace WeiHeng\UnionPay;

use App\Models\Order;
use Carbon\Carbon;
use GuzzleHttp\Client;

class UnionPay
{

    protected $config;
    protected $timeStamp;
    protected $authConfig;

    public function __construct($config)
    {
        $this->config = $config;
        $this->timeStamp = (string)Carbon::now();
        $this->authConfig = $this->_getAuthConfig();
    }

    /**
     * 支付
     *
     * @param \App\Models\Order $order
     * @param $payTypeName
     * @param null $subPayTypeName
     * @return array|bool
     */
    public function pay(Order $order, $payTypeName, $subPayTypeName = null)
    {
        if (!($payType = array_get($this->config['pay_type'], $payTypeName))) {
            return false;
        }
        $subPayType = null;
        if ($subPayTypes = array_get($this->config['sub_pay_type'], $payTypeName)) {
            $subPayTypeName = $subPayTypeName ? $subPayTypeName : key($subPayTypes);
            $subPayType = array_get($subPayTypes, $subPayTypeName);
        }

        $payData = $this->_getPayData($order, $payType, $subPayType);

        $data = $this->_getData($payData);

        $method = $this->config['method']['back_pay'];

        $this->authConfig = $this->_getAuthConfig($method);

        $secretKey = $this->authConfig['secret_key'];

        $aesEncryptString = $this->_base64Encode($this->_aesEncrypt($secretKey, $secretKey, $data));

        $options = $this->_getOptions($method, $aesEncryptString);

        $client = new Client();

        $res = $client->post($this->config['open_api_url'], ['form_params' => $options]);

        $content = $res->getBody()->getContents();

        return $this->_formatResponse($content);
    }

    /**
     * 快速进件
     *
     * @return array
     */
    public function subCompanyAdd()
    {

        $bankData = $this->_getCompanyData();

        $data = $this->_getData($bankData);

        $method = $this->config['method']['quick_in'];

        $this->authConfig = $this->_getAuthConfig($method);

        $secretKey = $this->authConfig['secret_key'];

        $aesEncryptString = $this->_base64Encode($this->_aesEncrypt($secretKey, $secretKey, $data));

        $options = $this->_getOptions($method, $aesEncryptString);

        $client = new Client();

        $res = $client->post($this->config['open_api_url'], ['form_params' => $options]);

        $content = $res->getBody()->getContents();

        return $this->_formatResponse($content);

    }

    /**
     * 获取余额
     *
     * @return array
     */
    public function balanceGet()
    {

        $data = json_encode([], JSON_FORCE_OBJECT);

        $method = $this->config['method']['balance_get'];

        $this->authConfig = $this->_getAuthConfig($method);

        $secretKey = $this->authConfig['secret_key'];

        $aesEncryptString = $this->_base64Encode($this->_aesEncrypt($secretKey, $secretKey, $data));

        $options = $this->_getOptions($method, $aesEncryptString);

        $client = new Client();

        $res = $client->post($this->config['open_api_url'], ['form_params' => $options]);

        $content = $res->getBody()->getContents();

        return $this->_formatResponse($content);
    }

    /**
     * 单笔代付
     *
     * @return array
     */
    public function agentPay()
    {
        $agentData = $this->_getAgentData();

        $data = $this->_getData($agentData);

        $method = $this->config['method']['settlement_transfer'];

        $this->authConfig = $this->_getAuthConfig($method);

        $secretKey = $this->authConfig['secret_key'];

        $aesEncryptString = $this->_base64Encode($this->_aesEncrypt($secretKey, $secretKey, $data));

        $options = $this->_getOptions($method, $aesEncryptString);

        $client = new Client();

        $res = $client->post($this->config['open_api_url'], ['form_params' => $options]);

        $content = $res->getBody()->getContents();

        return $this->_formatResponse($content);

    }

    /**
     * 批量代付
     *
     * @return array
     */
    public function batchTransfer()
    {
        $agentData = $this->_getBatchTransferData();

        $data = $this->_getData($agentData);

        $method = $this->config['method']['batch_transfer'];

        $this->authConfig = $this->_getAuthConfig($method);

        $secretKey = $this->authConfig['secret_key'];

        $aesEncryptString = $this->_base64Encode($this->_aesEncrypt($secretKey, $secretKey, $data));

        $options = $this->_getOptions($method, $aesEncryptString);

        $client = new Client();

        $res = $client->post($this->config['open_api_url'], ['form_params' => $options]);

        $content = $res->getBody()->getContents();

        return $this->_formatResponse($content);
    }

    /**
     * 判断sign
     *
     * @param $sign
     * @param $data
     * @return bool
     *
     */
    public function validateSign($sign, $data)
    {
        return $sign == md5($data . $this->authConfig['secret_key']);
    }

    /**
     * 解析data
     *
     * @param $data
     * @return string
     */
    public function decodeData($data)
    {
        $base64Decode = $this->_base64Decode($data);
        $secretKey = $this->authConfig['secret_key'];
        return $this->_aesDecrypt($secretKey, $secretKey, $base64Decode);
    }

    /**
     * 获取auth
     *
     * @return mixed
     */
    public function getAuthConfig()
    {
        return $this->authConfig;
    }

    /**
     * 转换json
     *
     * @param $options
     * @return string
     */
    private function _getData($options)
    {
        return json_encode($options, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 获取银行数据
     *
     * @return array
     */
    private function _getCompanyData()
    {

        $options = [
            'companyname' => '东仔科技2',
            'companycode' => '6000003',
            'accountname' => '林晓东',
            'bankaccount' => '6215584402010812337',
            'bank' => '中国工商银行四川成都世纪城支行',
            'bankcode' => '308584001303',
            'accounttype' => '1',
            'bankcardtype' => '1',
            'mobilephone' => '13536574250',
            'idcardno' => '440583199008071657',
            'address' => '深圳市张三街道23号'
        ];

        return $options;
    }

    /**
     * 代付
     *
     * @return array
     */
    private function _getAgentData()
    {
        return [
            'settlementid' => 400457877,
            'amount' => (int)bcmul(10, 100),
            'batchnumber' => (string)Carbon::now()->timestamp,
            'remark' => '结算',
        ];
    }

    /**
     * 获取代付数据
     *
     * @return array
     */
    private function _getBatchTransferData()
    {
        return [
            'total' => 1,
            'totalamount' => (int)bcmul(10, 100),
            'batchnumber' => (string)Carbon::now()->timestamp,
            'rows' => $this->_getBatchTransferRows()
        ];
    }

    /**
     * 获取各商家代付数据
     *
     * @return array
     */
    private function _getBatchTransferRows()
    {
        return [
            [
                'ordernumber' => '20170809154201',
                'accountname' => '林晓东',
                'bankaccount' => '6215584402010812337',
                'accounttype' => 1,
                'bank' => '中国工商银行',
                'mobilephone' => '13536574250',
                'certificateno' => '440583199008071657',
                'amount' => 1000
            ]
        ];
    }

    /**
     * 获取支付参数
     *
     * @param $order
     * @param $payType
     * @param $subPayType
     * @return array
     */
    private function _getPayData($order, $payType, $subPayType)
    {
        $options = [
            'ordernumber' => (string)$order->id,
            'body' => '订单收款-' . $order->id,
            'amount' => bcmul($order->after_rebates_price, 100),
            'businesstype' => '1001',
            'paymenttypeid' => $payType,
            'subpaymenttypeid' => $subPayType,
            'fronturl' => url($this->config['return_url']),
            'backurl' => 'http://dingbaida.com/' . $this->config['notify_url'],
            'payextraparams' => $this->_getPayExtraparams($payType, $subPayType),
        ];

        return $options;
    }


    /**
     * 获取请求数据
     *
     * @param $method
     * @param $data
     * @return array
     */
    private function _getOptions($method, $data)
    {
        $sign = $this->_getSign($method, $data);
        return [
            'appid' => $this->authConfig['app_id'],
            'method' => $method,
            'format' => $this->config['format'],
            'data' => $data,
            'v' => $this->config['v'],
            'timestamp' => $this->timeStamp,
            'session' => $this->authConfig['session'],
            'sign' => $sign
        ];
    }


    private function _getPayExtraparams($payType = '', $subPayType = '')
    {
        return json_encode(new \stdClass());
    }


    /**
     * 获取sign
     *
     * @param $encryptData
     * @param $method
     * @return string
     */
    private function _getSign($method, $encryptData)
    {
        return md5($this->authConfig['secret_key'] . $this->authConfig['app_id'] . $encryptData . $this->config['format'] . $method . $this->authConfig['session'] . $this->timeStamp . $this->config['v'] . $this->authConfig['secret_key']);
    }

    private function _getAuthConfig($method = null)
    {
        if (is_null($method) || $method == $this->config['method']['back_pay']) {
            return $this->config['back_pay'];
        }
        return $this->config['sub_company_add'];
    }

    /**
     * aes加密
     *
     * @param $privateKey
     * @param $iv
     * @param $data
     * @return string
     */
    private function _aesEncrypt($privateKey, $iv, $data)
    {
        //return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
        return openssl_encrypt($data, 'AES-128-CBC', $privateKey, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * aes解密
     *
     * @param $privateKey
     * @param $iv
     * @param $data
     * @return string
     */
    private function _aesDecrypt($privateKey, $iv, $data)
    {
        $str = openssl_decrypt($data,'AES-128-CBC',$privateKey, 3, $iv);
        return json_decode($this->_stripSpecialChat($str), true);
    }

    /**
     * 去掉末尾特殊字符  \x00
     *
     * @param $str
     * @return string
     */
    private function _stripSpecialChat($str)
    {
        return stripslashes(rtrim(addslashes($str), '\0'));
    }

    /**
     * base64加密
     *
     * @param $str
     * @return mixed|string
     */
    private function _base64Encode($str)
    {
        $base64str = base64_encode($str);
        $base64str = str_replace("+", "-", $base64str);
        $base64str = str_replace("/", "_", $base64str);
        return $base64str;
    }

    /**
     * base64解密
     *
     * @param $str
     * @return mixed|string
     */
    private function _base64Decode($str)
    {
        $str = str_replace("_", "/", $str);
        $str = str_replace("-", "+", $str);
        $unbase64str = base64_decode($str);
        return $unbase64str;
    }

    /**
     * 格式化
     *
     * @param $result
     * @return array
     */
    private function _formatResponse($result)
    {
        return (array)json_decode($result, true);
    }
}