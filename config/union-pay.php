<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2017/7/31
 * Time: 14:56
 */
return [
    'open_api_url' => 'https://gw.masget.com:17373/openapi/rest',
    'notify_url' => 'webhooks/union-pay/success',
    'return_url' => 'order-buy',
    'format' => 'json',
    'v' => '2.0',


    'back_pay' => [
        'app_id' => '400445348',
        'secret_key' => 'jw3sfa6eyo9kju97',
        'session' => 'jw3sfa6eyo9kju97umyuzu5v7qlfd1n8',
    ],

    'sub_company_add' => [
        'app_id' => '400445338',
        'secret_key' => 'QyD7BTUkwdTZ2CXM',
        'session' => 'm8v7dihom7qam1euj24yug2pqm9tt3rn',
    ],


    //方法
    'method' => [
        'quick_in' => 'masget.webapi.com.subcompany.add',
        'sub_company_key' => 'masget.webapi.com.subcompany.get',
        'front_pay' => 'masget.pay.compay.router.front.pay',
        'back_pay' => 'masget.pay.compay.router.back.pay',
        'query_trade_order' => 'masget.pay.compay.router.paymentjournal.get',
        'cancel_trade' => 'masget.pay.compay.router.undo',
        'refund' => 'masget.pay.compay.router.refund',
        'query_refund' => 'masget.pay.compay.router.refund.get',
        'settlement_transfer' => 'masget.webapi.com.settlement.transfer',
        'balance_get' => 'masget.account.wallet.balance.get',
        'batch_transfer' => 'masget.webapi.com.settlement.batch.transfer',
    ],

    //支付类型
    'pay_type' => [
        'pos' => 2,
        'union_b2c' => 4,
        'agent' => 7,
        'alipay' => 12,
        'wechat' => 13,
        'union_b2b' => 15,
        'quick_pay' => 25,
        'alipay_wechat' => 26,
        'gateway_pay' => 29,
        'union_qrcode' => 30,
        'qq_wallet' => 31,
        'union_qrcode_barcode' => 34,
    ],

    // 子支付类型
    'sub_pay_type' => [
        'alipay' => [
            'qrcode' => 12,
            'barcode' => 16,
            'service_window' => 20
        ],
        'wechat' => [
            'qrcode' => 13,
            'barcode' => 17,
            'public' => 21,
            'app' => 27
        ],
        'qq_wallet' => [
            'qrcode' => 31,
            'barcode' => 32,
            'public' => 33
        ]
    ],

];