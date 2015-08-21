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
        'type' => [
            'index' => '首页', // 首页(外侧)广告
            'user' => '用户', // 用户(内则)广告
            'app' => 'APP', // app广告(启动页)
        ],
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