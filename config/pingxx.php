<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/28
 * Time: 10:09
 */
return [
    'app_id' => 'app_1mH8m59WrrDCHSqb',                 //应用id
    'api_key' => 'sk_live_8izjnHmf9mPG4aTOWL0yvbv9',    //应用key

    //  支付渠道
    'channels' => [
        'yeepay_wap',               //易宝
        'alipay',                   //支付宝app
        'alipay_wap'          //支付宝app(网页）
    ],
    //支付成功同步回调地址
    'success_url' => [
        'yeepay_wap' => 'api/v1/pay/success-url',
        'alipay' => 'api/v1/pay/success-url',
        'alipay_wap' => 'api/v1/pay/success-url'
    ],
    'cancel_url' => [
        'yeepay_wap' => 'api/v1/pay/cancel-url',
        'alipay' => 'api/v1/pay/cancel-url',
        'alipay_wap' => 'api/v1/pay/cancel-url'
    ]
];