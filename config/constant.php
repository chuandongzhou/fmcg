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
        'pay_status' => [//支付状态
            'non_payment' => 0,
            'payment_success' => 1,
            'payment_failed' => 2,
            'refund' => 3,
            'refund_success' => 4,
            'refund_failed' => 5,
        ],
        'status' => [ //订单状态
            'non_sure' => 0, //未确认
            'non_send' => 1, //未发货
            'send' => 2, //已发货
            'finished' => 3, //完成
        ],
        'is_cancel' => [ //订单是否被取消，默认是未取消
            'off' => 0,
            'on' => 1,
        ]
    ],
    //用户类别
    'user' => [
        'type' => [
            'retailer' => 1,       //零售商
            'wholesaler' => 2,       //批发商
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
        ],
        'address_type' => [
            'shop_address' => 1,        //店铺地址
            'delivery_address' => 2,    //配送地址
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
            'no' => 0    //否
        ],
        'status' => [
            'on' => 1,   //上架
            'off' => 0   //下架
        ]
    ] ,
    // 银行列表
    'bank' => [
        'type' => [
            'BOC' => 1 ,   // '中国银行'
            'ICBC' => 2 ,   // '中国工商银行'
            'ABOC' => 3 ,   // '中国农业银行'
            'CCB' => 4 ,   // '中国建设银行'
            'CMB' => 5 ,   // '中国招商银行'
            'CB' => 6 ,   // '商业银行'
            //预留
            'HB' => 7 ,   // '华夏银行'
            'CEB' => 8 ,   // '中国光大银行'
            'CMBC' => 9 ,   // '中国民生银行'
            'CITIC' => 10 ,   // '中信实业银行'
            'CIB' => 11 ,   // '福建兴业银行'
        ]
    ],
    //收藏
    'like' => [
        'type' => [
            'goods' => 1,
            'shop' => 2
        ]
    ],
    //Model
    'model' => [
        'goods' => App\Models\Goods::class,
        'shop' => App\Models\Shop::class,
    ]
];
