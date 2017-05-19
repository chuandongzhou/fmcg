<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2016/12/12
 * Time: 17:10
 */
return [
    'merchantNo' => 'CX0001089',                //二维码编号
    'key' => 'c9efbc0d16f4480197e47d1171f4b703',//二维码以及代付key
    'url' => 'http://api.shijihuitong.com/cxpayApi/offLine',    //二维码地址
    'agentPayUrl' => 'http://api.shijihuitong.com/cxpayApi/agentPay',   //代付请求地址
    'bankPayUrl' => 'http://api.shijihuitong.com/cxpayApi/bank',             //网关支付地址
    'bankPayMerchantNo' => 'CX0001133',     //网关支付编号
    'bankPayKey' => '7f5147b36b7740bfa53f860bc0227760',  //网关支付key
    'backPayType' => [    //网关续费类型
        'user' => 1,
        'delivery' => 2,
        'salesman' => 3
    ]
];