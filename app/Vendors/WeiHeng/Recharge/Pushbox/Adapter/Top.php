<?php

namespace WeiHeng\Recharge\Pushbox\Adapter;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Tinpont\Pushbox\Adapter;
use Tinpont\Pushbox\Message;

/**
 * UMS adapter.
 *
 * @package Recharge\Pushbox\Adapter
 */
class Top extends Adapter
{
    /**
     * @var \GuzzleHttp\Client $client
     */
    protected $client;

    /**
     * @var string
     */
    protected $baseUri = 'http://gw.api.taobao.com/router/rest';


    /**
     * 默认参数
     *
     * @var array
     */
    protected $defaultParams = [
        'v' => '2.0',
        'format' => 'json',
        'sign_method' => 'md5',
        'method' => 'alibaba.aliqin.fc.sms.num.send',
        'sms_type' => 'normal',
        'sms_free_sign_name' => '订百达零售服务平台'
    ];

    /**
     * @var int
     */
    protected $signatureId;

    /**
     * 模板列表
     *
     * @var array
     */
    protected $templates;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->defaultParams['app_key'] = $this->getOption('app_key');
        $this->signatureId = $this->getOption('signature_id');
        $this->templates = $this->getOption('templates');
    }

    /**
     * 验证手机号码是否正确
     *
     * @param string $token
     * @return bool
     */
    protected function isValidToken($token)
    {
        return (bool)preg_match('/^1\d{10}$/i', $token);
    }

    /**
     * 短信消息发送
     *
     * @param string|\Tinpont\Pushbox\Message $message
     * @return Adapter
     */
    public function push($message)
    {
        $this->success = $this->fails = [];

        $message = $this->getMessage($message);
        $template = $message->getText();
        $messageOptions = $message->getOptions();

        foreach ($this->getDevices() as $device) {
            $context = array_merge($messageOptions, $device->getOptions());

            $token = $device->getToken();
            $result = $this->send($template, $token, $context);

            if ($result['err_code'] === '0') {
                $this->success[] = $token;
            } else {
                $this->fails[] = $token;
            }
        }

        return $this;
    }

    /**
     * 发送充值成功短信
     *
     * @param string|array $text
     * @return \Tinpont\Pushbox\Adapter
     */
    public function pushRecharged($text)
    {
        is_array($text) && $text = head($text);

        return $this->push(new Message('recharged', ['item' => $text]));
    }

    /**
     * 发送验证码
     *
     * @param $text
     * @return \Tinpont\Pushbox\Adapter
     */
    public function pushCode($text)
    {
        is_array($text) && $text = head($text);

        return $this->push(new Message('code', ['code' => $text]));
    }

    /**
     * 发送注册成功短信
     *
     * @param $text
     * @return \Tinpont\Pushbox\Adapter
     */
    public function pushRegister($text)
    {

        return $this->push(new Message('register', ['code' => $text]));
    }

    /**
     * 审核用户
     *
     * @param $text
     * @return \Tinpont\Pushbox\Adapter
     */
    public function pushAudit($text)
    {
        $template = 'audit_passed';

        $options = [
            'account' => $text['account'],
        ];
        if (!$text['result']) {
            $template = 'audit_not_passed';
            $options['error'] = isset($text['error']) ? $text['error'] : '资料不完整';
        }

        return $this->push(new Message($template, $options));
    }

    /**
     * 提现通知
     *
     * @param $text
     * @return \Tinpont\Pushbox\Adapter
     */
    public function pushWithdraw($text)
    {
        $options = [
            'number' => $text['withdraw_id'],
            'trading_number' => $text['trade_no']
        ];
        return $this->push(new Message('withdraw', $options));
    }

    /**
     * 订单处理
     *
     * @param $text
     * @return \Tinpont\Pushbox\Adapter
     */
    public function pushOrder($text)
    {
        return $this->push(new Message('order', $text));
    }


    /**
     * 发送短信
     *
     * @param string $template
     * @param string $mobile
     * @param array $context
     * @return array
     */
    protected function send($template, $mobile, array $context)
    {
        $templateId = array_get($this->templates, $template);
        if (empty($templateId)) {
            throw new \LogicException('Template id not exists for ' . $template);
        }

        $params = [
            //'signature_id' => $this->signatureId,
            'sms_template_code' => $templateId,
            'rec_num' => $mobile,
            'sms_param' => json_encode($context),
        ];

        return $this->handleResponse($this->post($params));
    }

    /**
     * post提交请求
     *
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function post(array $params = [])
    {
        $data = array_merge($params, $this->defaultParams);
        $data['timestamp'] = date('Y-m-d H:i:s');
        $data['sign'] = $this->getSign($data);

        return $this->getClient()->post(null, [
            'form_params' => $data,
        ]);
    }

    /**
     * 处理结果
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array
     */
    protected function handleResponse(ResponseInterface $response)
    {
        $json = (array)json_decode($response->getBody()->getContents(), true);

        $result = $json ? last($json) : [];

        $result && $result = $result['result'];

        if (!isset($result['err_code']) || $result['err_code'] != 0) {
            info('Top sms error', $json);
        }

        return $result;
    }

    /**
     * 淘宝签名计算
     *
     * @param array $params
     * @return string
     */
    protected function getSign(array $params)
    {
        $appSecret = $this->getOption('app_secret');
        $string = $appSecret;

        ksort($params);
        foreach ($params as $key => $value) {
            if ($key != 'sign') {
                $string .= ($key . $value);
            }
        }
        return strtoupper(md5($string . $appSecret));
    }

    /**
     * Get the current Client instance.
     *
     * @return \GuzzleHttp\Client $client
     */
    protected function getClient()
    {
        if ($this->client) {
            return $this->client;
        }

        return $this->client = new Client(['base_uri' => $this->baseUri]);
    }
}
