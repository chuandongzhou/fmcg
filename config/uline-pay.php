<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2017/7/31
 * Time: 14:56
 */
return [
    //api请求地址
    'open_api_url' => 'http://api.cmbxm.mbcloud.com',
    //接收微信支付异步通知回调地址
    'notify_url' => 'webhooks/uline-pay/success',
    //'return_url' => 'order-buy',
    //商户号
    'mch_id' => '100000002249',
    //交易类型
    'trade_type' => 'APP',
    //app密钥
    'secret_key' => '6965ae07be0a2a036a61a039461b88e6'
];