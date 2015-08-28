<?php

return [
    // 通用状态
    'status' => [
        'off' => '禁用',
        'on' => '正常',
    ],
    //支付方式
    'pay_type' => [
        'online' => '在线支付',
        'cod' => '货到付款',
    ],
    //订单表
    'order' => [
        'pay_status' => [//支付状态
            'non_payment' => '未付款',
            'payment_success' => '已付款',
            'payment_failed' => '付款失败',
            'refund' => '退款中',
            'refund_success' => '退款成功',
            'refund_failed' => '退款失败',
        ],
        'status' => [ //订单状态
            'non_sure' => '未确认',
            'non_send' => '未发货',
            'send' => '已发货',
            'finished' => '已完成',
        ],
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
    ],
    'feedback' => [               //反馈
        'status' => [
            'untreaded' => '未处理',
            'treaded' => '已处理'
        ]
    ]
];