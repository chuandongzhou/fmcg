<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/28
 * Time: 10:09
 */
return [
    //	商户编号p1_MerId,以及密钥merchantKey 需要从易宝支付平台获得
    'p1_mer_id' => "10012687856",
    //测试使用
    'merchant_key' => "rb6pb8W6DR6c04dE1cP9F96597AcQdw5A1L4XLwW7K64j254sXXU1B48V8Zz",
    //测试使用

    'log_name' => storage_path() . "/logs/YeePay_HTML.log",
    'req_url_online' => "https://www.yeepay.com/app-merchant-proxy/node",
    'ref_url_online' => "https://www.yeepay.com/app-merchant-proxy/command",//退款正式地址
    'ref_url_test' => "http://tech.yeepay.com:8080/robot/debug.action",     //退款测试地址
    // 业务类型
    // 支付请求，固定值"Buy" .
    'p0_cmd' => "Buy",
    //	送货地址
    // 为"1": 需要用户将送货地址留在易宝支付系统;为"0": 不需要，默认为 "0"
    'p9_saf' => "0",
    #	交易币种,固定值"CNY".
    'p4_cur' => "CNY",
    #	商户接收支付成功数据的地址,支付成功后易宝支付会向该地址发送两次成功通知.
     'p8_url' => 'webhook/yeepay/success',

    'pr_need_response' => "1"

];