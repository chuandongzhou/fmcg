<?php

return [
    // 通用状态
    'status' => [
        'off' => 0,
        'on' => 1,
    ],
    //支付方式
    'pay_type' => [
        'online' => 1,
        'cod' => 2,
    ],
    //订单表
    'order' => [
        'order_status' => [ //订单状态
            'non-delivery' => 0,
            'delivered' => 1,
        ],
        'pay_status' => [ //支付状态
            'non-payment' => 0,
            'payment' => 1,
        ]
    ],
    //用户类别
    'user' => [
        'type' => [
            'wholesalers' => 1,       //批发商
            'retailer' => 2,       //零售商
            'supplier' => 3,       //供应商
        ],
    ],
    //广告表类型
    'advert' => [
        'type' => [
            'index' => 1, // 首页(外侧)广告
            'user' => 2, // 用户(内则)广告
            'app' => 3, // app广告(启动页)
        ],
    ],
    // 店铺图片分类
    'shop' => [
        'file_type' => [
            'logo' => 1,         //logo
            'license' => 2,         //营业执照
            'images' => 3          //店铺图片
        ]
    ],
    // 交易相关
    'trade' => [
        'type' => [
            'in' => 1,               //入帐
            'out' => 2,              //提现
        ],
        'pay_type' => [
            'yeepay' => 1,          //易宝
            'alipay' => 2           //支付宝
        ],
        'pay_info' => [
            'success' => 1,         //成功
            'failed' => 2           //失败
        ],
        'trade_currency' => [
            'rmb' => 1,             //人民币
            'foreign_currency' => 2 //外币
        ]
    ],
    'feedback' => [               //反馈
        'status' => [
            'untreaded' => 0,      // 未处理
            'treaded' => 1,        //已处理
        ]
    ],
    // 商品
    'goods' => [
        'type' => [
            'yes' => 1,   //是
            'no'  => 0    //否
        ],
        'status' => [
            'on' => 1,   //上架
            'off' => 0   //下架
        ]
    ]
];
