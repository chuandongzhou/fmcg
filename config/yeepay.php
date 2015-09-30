<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/28
 * Time: 10:09
 */
return [
    //	商户编号p1_MerId,以及密钥merchantKey 需要从易宝支付平台获得
    'p1_mer_id' => "10001126856",
    //测试使用
    'merchant_key' => "69cl522AV6q613Ii4W6u8K6XuW8vM1N6bFgyv769220IuYe9u37N4y7rI4Pl",
    //测试使用

    'log_name' => storage_path() . "/logs/YeePay_HTML.log",
    'req_url_onLine' => "https://www.yeepay.com/app-merchant-proxy/node",
    // 业务类型
    // 支付请求，固定值"Buy" .
    'p0_cmd' => "Buy",
    //	送货地址
    // 为"1": 需要用户将送货地址留在易宝支付系统;为"0": 不需要，默认为 "0"
    'p9_saf' => "0",
    #	交易币种,固定值"CNY".
    'p4_cur' => "CNY",
    #	商户接收支付成功数据的地址,支付成功后易宝支付会向该地址发送两次成功通知.
     'p8_url' => url('yeepay/callback'),

    'pr_need_response' => "1"

];