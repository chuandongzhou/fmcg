<?php

return [
    // 通用状态
    'status' => [
        'off' => 0,
        'on' => 1,
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
        'ad_type' => [              //广告列表
            'goods' => 1,       //商品
            'wholesalers' => 2,       //批发商
        ],
        'time_type' => [              //广告时长
            'forever' => 1,         //永久
            'time_slot' => 2,         //时间段
        ],
        'app_type' => [              //app广告归属分类
            'wholesalers' => 1,       //批发商
            'retailer' => 2,       //零售商
        ]
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
    ]
];
