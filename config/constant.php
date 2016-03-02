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
    'cod_pay_type' => [
        'cash' => 1,  //现金
        'card' => 2   //刷卡
    ],
    //订单表
    'order' => [
        'pay_status' => [//支付状态
            'non_payment' => 0,
            'payment_success' => 1,
            'payment_failed' => 2,
            'refund' => 3,
            'refund_success' => 4,
        ],
        'status' => [ //订单状态
            'non_confirm' => 0, //未确认
            'non_send' => 1, //未发货
            'send' => 2, //已发货
            'finished' => 3, //完成
        ],
        'is_cancel' => [ //订单是否被取消，默认是未取消
            'off' => 0,
            'on' => 1,
        ],
        'auto_receive_time' => 72, //自动收货时间  （小时）
    ],
    //用户类别
    'user' => [
        'type' => [
            'retailer' => 1,       //零售商
            'wholesaler' => 2,       //批发商
            'supplier' => 3,       //供应商
        ],
        //审核状态
        'audit_status' => [
            'not_audit' => 0,       //未审核
            'pass' => 1,       //通过
            'not_pass' => 2,       //未通过
        ]
    ],
    //后台配置
    'admin' => [
        'super_admin_name' => 'admin'
    ],
    //首页栏目
    'home_column' => [
        'goods' => [
            'count' => 10, //显示商品条数
            'cache' => [
                'pre_name' => 'home_column:goods:',
                'expire' => 10
            ]
        ],
        'shop' => [
            'count' => 10,  //显示店铺条数
            'cache' => [
                'pre_name' => 'home_column:shops:',
                'expire' => 10
            ]
        ],
        'type' => [
            'goods' => 1,
            'shop' => 2
        ]
    ],
    //广告表类型
    'advert' => [
        'type' => [
            'index' => 1, // 首页(外侧)广告
            'user' => 2, // 用户(内则)广告
            'app' => 3, // app广告(启动页)
        ],
        'cache' => [
            'index' => [
                'name' => 'advert:index',
                'expire' => 10,
            ]
        ]
    ],
    // 店铺图片分类
    'shop' => [
        'file_type' => [
            'logo' => 1,         //logo
            'license' => 2,         //营业执照
            'images' => 3,          //店铺图片
            'business_license' => 4, //经营许可证
            'agency_contract' => 5, //代理合同
        ],
        'address_type' => [
            'shop_address' => 1,        //店铺地址
            'delivery_address' => 0,    //配送地址
        ],
        'sort' => [
            'hot',
            'new',
            'price',
        ],
        'page_per_num' => 10  //店铺每页显示数量
    ],
    // 交易相关
    'trade' => [
        'type' => [
            'in' => 1,               //入帐
            'out' => 2,              //提现
        ],
        'pay_type' => [
            'yeepay' => 1,          //易宝
            'pingxx' => 2           //pingxx
        ],
        'pay_status' => [
            'success' => 1,         //成功
            'failed' => 2           //失败
        ],
        'trade_currency' => [
            'rmb' => 1,             //人民币
            'foreign_currency' => 2 //外币
        ],
        'is_finished' => [          //用户是否确认收货(在线支付)
            'no' => 0,              //默认为未确认
            'yes' => 1              //确认
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
        ],
        'sort' => [
            'name',
            'price',
            'new',
            'not_on'
        ],
        'pieces' => [
            'box' => 0,
            'bottle' => 1,
            'boxes' => 2,
            'tin' => 3,
            'rope' => 4,
            'bag' => 5,
            'pot' => 6,
            'packet' => 7
        ],
        'import_allow_ext' => [
            'xls',
            'xlsx'
        ],
        'cache' => [
            'keywords_pre' => 'goods:keywords:',
            'num' => 8
        ]
    ],
    // 银行列表
    'bank' => [
        'type' => [
            'ICBC' => 1,        //'工商银行',
            'CMBCHINA' => 2,    // '招商银行',
            'CCB' => 3,         // '建设银行',
            'BOCO' => 4,        // '交通银行',
            'CIB' => 5,         // '兴业银行',
            'CMBC' => 6,        // '中国民生银行',
            'CEB' => 7,         //'光大银行',
            'BOC' => 8,         // '中国银行',
            'PINGANBANK' => 9,  // '平安银行',
            'ECITIC' => 10,     // '中信银行',
            'SDB' => 11,        // '深圳发展银行',
            'GDB' => 12,        // '广发银行',
            'SHB' => 13,        //'上海银行',
            'SPDB' => 14,       //'上海浦东发展银行',
            'HXB' => 15,        // '华夏银行「借」',
            'BCCB' => 16,       // '北京银行「借」',
            'ABC' => 17,        //'中国农业银行「借」',
            'POST' => 18,       //'中国邮政储蓄银行「借」'
        ]
    ],
    //收藏
    'like' => [
        'type' => [
            'goods' => 1,
            'shops' => 2
        ],
        'model' => [
            'goods' => App\Models\Goods::class,
            'shops' => App\Models\Shop::class
        ]
    ],
    //标签
    'attr' => [
        'default' => [
            'brand' => 1,                  // 品牌
            'import_and_export' => 20219,  // 进出口
            'packing' => 20235,            //包装
            'place' => 20465,              //产地
        ]
    ],
    //推送设备类型
    'push_device' => [
        'ios' => 1,
        'android' => 2,
    ],
    //android推送通知类型
    'push_type' => [
        'msg' => 0, //消息(透传)
        'notice' => 1 //通知
    ],
    //排序
    'sort' => [
        'goods' => [
            'hot' => 1,
            'price' => 2,
            'new' => 3,
        ],
        'shop' => [
            'hot' => 1,
            'new' => 2
        ]
    ],
    //统计页的两个分页单页显示条数
    'statistics_per' => 5,
    //推送信息生命时间
    'push_time' => [//单位都是秒(s)
        'when_push' => 300,//当存在时间小于该时间时送入到推送队列
        'msg_life' => 600//信息在redis中存在的生命周期
    ],
    //提现状态
    'withdraw' => [
        'failed' => 0,
        'review' => 1,
        'pass' => 2,
        'payment' => 3,
    ],
    // 手机验证码
    'validate_code' => [
        'length' => 4,
        'backup' => [
            'pre_name' => 'backup:code:',
            'expire' => 120,
        ]
    ],
    'location' => [
        'default_province' => 510000,
    ],
    //pos机
    'pos' => [
        'key' => 'DFE23HLAW198820SQWE1224SDAQQ3319203945',
        //业务编码
        'service_code' => [
            'login' => 'COD201',     //登录
            'retrieve' => 'COD402',    //查询
            'pay' => 'COD403',       //支付
            'receive' => 'COD404',   //签收
            'cancel' => 'COD406',   //撤销
            'refund' => 'COD407'    //退款
        ],
        //返回码
        'result_code' => [
            'password_error' => 10,
            'no_user' => 11,
            'receive_error' => 3,
            'hmac_error' => 4,
            'success' => 2
        ],
        'order_status' => [
            'no_order' => 20,
            'signed' => 21,
            'received_no_sign' => 22,
            'no_pay_no_sign' => 23
        ]
    ],

    //系统信息
    'system' => [
        'employee_name' => '订百达',
        'company_code' => 'dingbaida',
        'company_name' => '成都订百达科技有限公司',
        'company_addr' => '成都市高新区天华路299号英郡三期6栋1单元905',
        'company_tel' => '028-83233316',
        'company_mobile' => '13980537732',
        'company_ceo' => '马先生',
        'company_record' => '蜀ICP备15031748号-1'
    ]

];
