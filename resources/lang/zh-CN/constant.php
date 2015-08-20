<?php

return [
    // 通用状态
    'status' => [
        'off' => '禁用',
        'on' => '正常',
    ],
    //用户类别
    'user' => [
        'type' => [
            'wholesalers' => '批发商',
            'retailer' => '零售商',
            'supplier' => '供应商',
        ],
    ],
    //广告表类型
    'advert' => [
        //广告分类设置
        'ad_type' => [
            1 => '商品',
            2 => '批发商',
        ],
        //广告时长设置
        'time_type' => [
            1 => '永久',
            2 => '时间段',
        ],
        //app广告归属分类
        'app_type' => [
            1 => '批发商',
            2 => '零售商',
        ]

    ],
    // 交易相关
    'trade' => [
        'type' => [
            'in' => '入帐',
            'out' => '提现'
        ],
        'pay_type' => [
            'yeepay' => '易宝',
            'alipay' => '支付宝',
        ],
        'pay_info' => [
            'success' => '成功',
            'failed' => '失败'
        ],
        'trade_currency' => [
            'rmb' => '人民币',
            'foreign_currency' => '外币'
        ]
    ]
];