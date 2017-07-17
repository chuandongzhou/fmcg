<?php

return [
    // 通用状态
    'status' => [
        'off' => '禁用',
        'on' => '启用',
    ],
    //支付类型
    'pay_type' => [
        'online' => '在线支付',
        'cod' => '货到付款',
        'pick_up' => '自提',
    ],
    //支付方式
    'pay_way' => [
        'online' => [
            'yeepay' => '易宝',
            'alipay' => '支付宝',
        ],
        'cod' => [
            'cash' => '现金',
            'card' => '刷卡'
        ]

    ],
    //支付渠道
    'payment_channel' => [
        'icon' => [
            'width' => 100,
            'height' => 35
        ],
        'type' => [
            'pc' => 'PC',
            'app' => 'APP'
        ]
    ],
    //订单表
    'order' => [
        'pay_status' => [//支付状态
            'non_payment' => '未付款',
            'payment_success' => '已付款',
            'payment_failed' => '付款失败',
            'refund' => '退款中',
            'refund_success' => '退款成功',
        ],
        'status' => [ //订单状态
            'non_confirm' => '未确认',
            'non_send' => '未发货',
            'send' => '已发货',
            'finished' => '已完成',
            'invalid' => '已作废',
        ],
        //类型
        'type' => [
            'platform' => '自主订单',
            'business' => '业务订单'
        ],
        'is_cancel' => [//订单是否被取消
            'off' => '未取消',
            'on' => '已取消',
        ],
        //订单打印模块
        'templete' => [
            'first' => '219mm*140mm模板一',
            'second' => '219mm*140mm模板二',
            'third' => '209mm*296mm模板一',
            'fourth' => '209mm*296mm模板二'
        ],
    ],
    //用户类别
    'user' => [
        'type' => [
            'wholesaler' => '批发商',
            'retailer' => '终端商',
            'supplier' => '供应商',
            'maker' => '厂家',
        ],
        // 审核状态
        'audit_status' => [
            'not_audit' => '未审核',
            'pass' => '通过',
            'not_pass' => '未通过'
        ]
    ],
    //广告表类型
    'advert' => [
        'type' => [
            'index' => '首页', // 首页(外侧)广告
            'user' => '用户', // 用户(内则)广告
            'app' => 'APP', // app广告(启动页)
            'category' => '商品分类',
            'left-category' => '商品分类左侧栏'
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
            'yeepay_wap' => '易宝(app)',
            'pos' => 'pos机',
            'alipay_pc' => '支付宝',
            'alipay' => '支付宝(app)',
            //'alipay_wap' => '支付宝(app网页)',
            'balancepay' => '余额支付',
            'wechat_pay' => '微信支付'
        ],
        'pay_status' => [
            'success' => '成功',
            'failed' => '失败',
            'no_order' => '查无此单',
            'signed' => '订单已签收',
            'received_no_sign' => '已收款，未签收',
            'no_pay_no_sign' => '未支付，未签收'
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
    ],
    // 商品
    'goods' => [
        'type' => [
            'yes' => '是',
            'no' => '否',
        ],
        'status' => [
            'on' => '上架',
            'off' => '下架'
        ],
        // 单位
        'pieces' => [
            'box' => '盒',
            'bottle' => '瓶',
            'boxes' => '箱',
            'tin' => '听',
            'rope' => '条',
            'bag' => '袋',
            'pot' => '罐',
            'packet' => '包',
            'bucket' => '桶',
            'cup' => '杯',
            'branch' => '支',
            'individual' => '个',
            'canister' => '筒',
            'row' => '排',
            'piece' => '件',
            'pair' => '对',
            'bowl' => '碗',
            'pot2' => '壶',
            'hang' => '挂',
            'carry' => '提'
        ]
    ],
    // 银行列表
    'bank' => [
        'type' => [
            'ICBC' => '工商银行',
            'CMB' => '招商银行',
            'CCB' => '中国建设银行',
            'BCOM' => '中国交通银行',
            'CMBC' => '中国民生银行',
            'CEB' => '中国光大银行',
            'BOC' => '中国银行',
            'PAB' => '平安银行',
            'CITIC' => '中信银行',
            'GDB' => '广发银行',
            'BOB' => '北京银行',
            'ABC' => '中国农业银行',
            'PSBC' => '中国邮政储蓄银行',
            /*'NJCB' => '南京银行',
            'BEA' => '东亚银行',
            'SPDB' => '浦发银行',
            'GZCB' => '广州银行',
            'SHB' => '上海银行',
            'CIB' => '兴业银行',
            'SDB' => '深圳发展银行',
            'HXB' => '华夏银行',
            'JSB' => '江苏银行',
            'SRCB' => '上海农村商业银行',
            'CBHB' => '渤海银行',
            'BJRCB' => '北京农商银行',
            'NBCB' => '宁波银行',
            'HZB' => '杭州银行',
            'HSB' => '徽商银行',
            'CZB' => '浙商银行',
            'DLB' => '大连银行',
            'UPOP' => '银联在线支付'*/
        ]
    ],
    // 收藏
    'like' => [
        'type' => [
            'goods' => '商品',
            'shop' => '店铺'
        ]
    ],
    //标签
    'attr' => [
        'default' => [
            'brand' => '品牌',
            'import_and_export' => '进口/国产',
            'packing' => '包装',
            'place' => '产地'
        ]
    ],
    // 前台栏目
    'home_column' => [
        'type' => [
            'goods' => '商品',
            'shop' => '店铺'
        ]
    ],
    //排序
    'sort' => [
        'goods' => [
            'hot' => '热门',
            'price' => '价格',
            'new' => '最新'
        ],
        'shop' => [
            'hot' => '热门',
            'new' => '最新'
        ]
    ],
    //推送信息
    'push_msg' => [
        'non_send' => [
            'online' => '买家已完成在线支付,请发货',
            'cod' => '买家已提交货到付款订单,请发货',
            'pick_up' => '买家已提交到店面自提订单'
        ],
        'finished' => '买家已确认收货',
        'send' => '卖家已发货,请注意查收',
        'cancel_by_buyer' => '已经被买家取消~~',
        'cancel_by_seller' => '已经被卖家取消~~',
        'price_changed' => '价格发生了变化',
        'review_failed' => '审核未通过',
        'review_payment' => '已打款,请查收',
        'refund' => '已取消并已申请退款',
    ],
    //推送设备类型
    'push_device' => [
        'ios' => 'IOS',
        'android' => '安卓',
        'delivery' => '司机',
        'business' => '外勤'
    ],

    //提现状态
    'withdraw' => [
        'failed' => '审核未通过',
        'review' => '审核中',
        'pass' => '审核通过',
        'payment' => '已打款',
    ],
    //pos机
    'pos' => [
        //返回码
        'result_code' => [
            'password_error' => '账户名或密码错误',
            'no_user' => '没有该用户',
            'receive_error' => '接收失败',
            'hmac_error' => '报文检验失败',
            'success' => '成功'
        ],
    ],
    //签约管理
    'sign' => [
        //账户续期金额
        'expire_amount' => [
            'a_year' => '1年',
            'two_years' => '2年',
            'three_years' => '3年',
            /* 'four_months' => '4',
             'five_months' => '5',
             'six_months' => '6',
             'seven_months' => '7',
             'eight_months' => '8',
             'nine_months' => '9',
             'a_year' => '1年',*/
        ],
        //续期金额
        'worker_expire_amount' => [
            'a_month' => '1',
            'two_months' => '2',
            'three_months' => '3',
            'four_months' => '4',
            'five_months' => '5',
            'six_months' => '6',
            'seven_months' => '7',
            'eight_months' => '8',
            'nine_months' => '9',
            'a_year' => '1年',
        ]
    ],
    'admin' => [
        'notification' => [
            'role-create' => '创建角色',
            'admin-create' => '创建管理员',
            'user-create' => '创建用户',
            'category-create' => '创建商品分类',
            'attr-create' => '创建商品标签',
            'images-create' => '添加商品图片',
            'advert-create' => '添加广告',
            'promoter-create' => '添加推广',
            'version-recode-create' => '版本更新',
            'shop-column-create' => '店铺栏目添加',
            'notice-create' => '添加公告',
            'role-update' => '角色更新',
            'admin-update' => '管理员更新',
            'category-update' => '商品分类更新',
            'attr-update' => '商品标签更新',
            'advert-update' => '广告更新',
            'promoter-update' => '推广人员更新',
            'shop-column-update' => '店铺栏目更新',
            'notice-update' => '公告更新',
        ],
    ],
    // 业务员
    'salesman' => [
        'order' => [
            'status' => [
                'not_pass' => '未审核',
                'passed' => '已通过'
            ],
        ],
        'customer' => [
            //陈列费
            'display_type' => [
                'no' => '暂无',
                'cash' => '现金',
                'mortgage' => '陈列商品'
            ]
        ]
    ],

    //出入库
    'inventory' => [
        'inventory_type' => [
            'system' => '系统',
            'manual' => '手动'
        ],
        'action_type' => [
            'in' => '入库',
            'out' => '出库',
        ]
    ],
    //资产表
    'asset' => [
        'status' => [
            'on' =>  '已启用',
            'off' => '已禁用'
        ],
    ],
    //资产申请使用表
    'asset_apply' => [
        'status' => [
            'not_audit' => '未审核',
            'approved' => '通过',
            'delete' => '删除'
        ],
    ],
    //资产申请日志动作区分
    'asset_apply_log' => [
        'action' => [
            'apply' => '提交申请',
            'review' =>'审核处理',
            'use' => '登记开始使用时间'
        ]
    ],
    'promo' => [
        'type' => [
            'custom' => '自定义',
            'money-money' => '下单总金额达到返利',
            'money-goods' => '下单总金额达到返商品',
            'goods-money' => '下单商品总量达到返利',
            'goods-goods' => '下单商品总量达到返商品',
        ],
        'review_status' => [
            'pass' => '通过',
            'non-review' => '未审核'
        ]
    ]
];