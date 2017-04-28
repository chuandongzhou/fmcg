<?php

return [
    // 通用状态
    'status' => [
        'off' => 0,
        'on' => 1,
    ],
    //支付类型
    'pay_type' => [
        'online' => 1,
        'cod' => 2,
        'pick_up' => 3
    ],
    //支付方式
    'pay_way' => [
        'online' => [
            'yeepay' => 1,
            'alipay' => 2
        ],
        'cod' => [
            'cash' => 1,  //现金
            'card' => 2   //刷卡
        ]
    ],

    //支付渠道
    'payment_channel' => [
        'icon' => [
            'width' => 100,
            'height' => 35
        ],
        'type' => [
            'pc' => 1,
            'app' => 2
        ]
    ],

    //订单表
    'order' => [
        'goods' => [
            'type' => [
                'order_goods' => 0,
                'mortgage_goods' => 1
            ]
        ],
        'pay_status' => [//支付状态
            'non_payment' => 0,        //未支付
            'payment_success' => 1,     //已支付
            'payment_failed' => 2,
            'refund' => 3,              //退款中
            'refund_success' => 4,      //已退款
        ],
        'status' => [ //订单状态
            'non_confirm' => 0,         //未确认
            'non_send' => 1,            //未发货
            'send' => 2,                //已发货
            'finished' => 3,            //完成
            'invalid' => 4              //作废
        ],
        //类型
        'type' => [
            'platform' => 0,            //平台
            'business' => 1,            //业务
        ],
        'is_cancel' => [ //订单是否被取消，默认是未取消
            'off' => 0,
            'on' => 1,
        ],
        'delivery_mode' => [
            'delivery' => 1,
            'pick_up' => 2
        ],
        //订单打印模块
        'templete' => [
            'first' => 1,
            'second' => 2,
            'third' => 3,
            'fourth' => 4
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
        ],
    ],
    //首页栏目
    'home_column' => [
        'goods' => [
            'count' => 8, //显示商品条数
            'cache' => [
                'name_cate' => 'home_column:cate:',
                'name_admin' => 'home_column:admin:',
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
            'category' => 4, //商品分类广告
            'shop' => 5, //店铺首页广告
            'promote' => 6, //店铺促销信息
            'signature' => 7, //店招
            'left-category' => 8 //商品分类左侧广告
        ],
        'cache' => [
            'index' => [
                'name' => 'advert:index',
                'expire' => 10,
            ],
            'app' => [
                'name' => 'advert:app',
                'expire' => 10
            ]
        ]
    ],
    //公告
    'notice' => [
        'index' => [
            'cache' => [
                'name' => 'notice:index',
                'expire' => 10,
            ],
            'count' => 3
        ],
    ],
    // 店铺
    'shop' => [
        'file_type' => [
            'logo' => 1,         //logo
            'license' => 2,         //营业执照
            'images' => 3,          //店铺图片
            'business_license' => 4, //经营许可证
            'agency_contract' => 5, //代理合同
            'signature' => 6, //店招
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
        'home_page_per_num' => 15,
        'page_per_num' => 10,  //店铺每页显示数量
        'qrcode_size' => 60   //二维码尺寸
    ],
    // 交易相关
    'trade' => [
        'type' => [
            'in' => 1,               //入帐
            'out' => 2,              //提现
        ],

        'pay_type' => [
            'yeepay' => 1,              //易宝
            'yeepay_wap' => 2,          //pingxx_易宝
            'pos' => 3,                 //pos机
            'alipay_pc' => 4,           //支付宝
            'alipay' => 5,              //支付宝app
            //'alipay_wap' => 6,           //支付宝（app网页）
            'balancepay' => 7,               //余额支付,
            'wechat_pay' => 8           //微信支付
        ],
        'pay_status' => [
            'success' => 1,         //成功
            'failed' => 2,           //失败
            'no_order' => 20,
            'signed' => 21,
            'received_no_sign' => 22,
            'no_pay_no_sign' => 23
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
    //反馈
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
            'packet' => 7,
            'bucket' => 8,
            'cup' => 9,
            'branch' => 10,
            'individual' => 11,
            'canister' => 12,
            'row' => 13,
            'piece' => 14,
            'pair' => 15,
            'bowl' => 16,
            'pot2' => 17,
            'hang' => 18,
            'carry' => 19
        ],
        'import_allow_ext' => [
            'xls',
            'xlsx'
        ],
        'cache' => [
            'keywords_pre' => 'goods:keywords:',
            'num' => 8,
            'cate_name' => 'goods:cate_name:',
            'cate_name_expire' => 10
        ]
    ],
    // 银行列表
    'bank' => [
        'type' => [
            'ICBC' => 1,//工商银行
            'CMB' => 2,//招商银行
            'CCB' => 3,//中国建设银行
            'BCOM' => 4,//中国交通银行
            'CIB' => 5,//兴业银行
            'CMBC' => 6,//中国民生银行
            'CEB' => 7,//中国光大银行
            'BOC' => 8,//中国银行
            'PAB' => 9,//平安银行
            'CITIC' => 10,//中信银行
            'SDB' => 11,//深圳发展银行
            'GDB' => 12,//广发银行
            'SHB' => 13,//上海银行
            'SPDB' => 14,//浦发银行
            'HXB' => 15,//华夏银行
            'BOB' => 16,//北京银行
            'ABC' => 17,//中国农业银行
            'PSBC' => 18,//中国邮政储蓄银行
            'GZCB' => 19,//广州银行
            'JSB' => 20,//江苏银行
            'SRCB' => 21,//上海农村商业银行
            'CBHB' => 22,//渤海银行
            'BJRCB' => 23,//北京农商银行
            'NJCB' => 24,//南京银行
            'BEA' => 25,//东亚银行
            'NBCB' => 26,//宁波银行
            'HZB' => 27,//杭州银行
            'HSB' => 28,//徽商银行
            'CZB' => 29,//浙商银行
            'DLB' => 30,//大连银行
            'UPOP' => 31,//银联在线支付
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
    //分类
    'category' => [
        'cache' => [
            'pre_name' => 'categories:',
            'expire' => 1440
        ]
    ],
    //标签
    'attr' => [
        'default' => [
            'brand' => 1,                  // 品牌
            'import_and_export' => 20219,  // 进出口
            'packing' => 20235,            //包装
            'place' => 20465,              //产地
        ],
        'cache' => [
            'pre_name' => 'attrs:',
            'expire' => 1440
        ]
    ],
    //推送设备类型
    'push_device' => [
        'ios' => 1,
        'android' => 2,
        'delivery' => 3,
        'business' => 4
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
        ],
        'update' => [
            'pre_name' => 'update-auth:code:',
            'expire' => 120,
        ],

        'register' => [
            'pre_name' => 'register:code',
            'expire' => 120
        ]
    ],
    //地址
    'address' => [
        'default_province' => 510000,
        'provinces' => [
            'cache' => [
                'name' => 'address:province',
                'expire' => -1
            ]
        ],
        'districts' => [
            'cache' => [
                'pre_name' => 'address:districts:',
                'expire' => -1
            ]
        ]
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
        'max_fee' => 26,   //pos机支付最高手续费
    ],
    //系统信息
    'system' => [
        'employee_name' => '订百达',
        'company_code' => 'dingbaida',
        'company_name' => '成都订百达科技有限公司',
        'company_addr' => '成都市高新区天华路9号6栋1单元905',
        'company_tel' => '028-83233316',
        'company_mobile' => '17723323231',
        'company_ceo' => '马先生',
        'company_record' => '蜀ICP备15031748号-1'
    ],
    //签约管理
    'sign' => [
        //工人超过数
        'max_worker' => 4,
        //超过后每个工人缴费金额
        'worker_excess_amount' => 10,
        //保证金
        'deposit' => 1000,
        //缴纳保证金后免费使用月份
        'free_month' => 3,
        //账号续期金额
        'expire_amount' => [
            'a_year' => 1000,
            'two_years' => 2000,
            'three_years' => 3000,
            /* 'four_months' => 400,
             'five_months' => 500,
             'six_months' => 600,
             'seven_months' => 700,
             'eight_months' => 800,
             'nine_months' => 900,
             'a_year' => 1000,*/
        ],
        //业务员司机续期金额
        'worker_expire_amount' => [
            'a_month' => 10,
            'two_months' => 20,
            'three_months' => 30,
            'four_months' => 40,
            'five_months' => 50,
            'six_months' => 60,
            'seven_months' => 70,
            'eight_months' => 80,
            'nine_months' => 90,
            'a_year' => 100,
        ]

    ],

    //后台配置
    'admin' => [
        'super_admin_name' => 'admin',
        'phone' => '18780521651',  //管理员手机
        'notification' => [
            'role-create' => 1,
            'admin-create' => 2,
            'user-create' => 3,
            'category-create' => 4,
            'attr-create' => 5,
            'images-create' => 6,
            'advert-create' => 7,
            'promoter-create' => 8,
            'version-recode-create' => 9,
            'shop-column-create' => 10,
            'notice-create' => 11,
            'role-update' => 12,
            'admin-update' => 13,
            'category-update' => 14,
            'attr-update' => 15,
            'advert-update' => 16,
            'promoter-update' => 17,
            'shop-column-update' => 18,
            'notice-update' => 19,
        ],
    ],
    //业务员
    'salesman' => [
        //头像
        'avatar' => [
            64 => 'default_64.jpg',
            128 => 'default_128.jpg',
        ],
        //客户
        'customer' => [
            'address_type' => [
                'business' => 1,
                'shipping' => 2
            ],
            //陈列费
            'display_type' => [
                'no' => 0,
                'cash' => 1,
                'mortgage' => 2
            ]
        ],
        //订单
        'order' => [
            'sync' => [
                'pay_type' => 2,
                'pay_way' => 1,
            ],
            'status' => [
                'not_pass' => 0,
                'passed' => 1
            ],
            'type' => [
                'order' => 0,
                'return_order' => 1
            ],
            'goods' => [
                'type' => [
                    'order' => 0,
                    'return' => 1
                ]
            ]
        ]
    ],
];
