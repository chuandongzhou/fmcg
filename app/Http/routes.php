<?php


/**
 * 后台登录
 */
$router->group(['prefix' => 'admin/auth', 'namespace' => 'Admin'], function ($router) {
    $router->controller('/', 'AuthController');
});

/**
 * 子帐号登录
 */
$router->group(['prefix' => 'child-user/auth', 'namespace' => 'ChildUser'], function ($router) {
    $router->controller('/', 'AuthController');
});


/**
 * 移动端登录注册
 */
$router->group(['domain' => 'm.fmcg.com', 'namespace' => 'Mobile'], function ($router) {
    $router->group(['prefix' => 'auth'], function ($router) {
        $router->get('login', 'AuthController@login');
        $router->get('register-account', 'AuthController@registerAccount');
        $router->get('register-password', 'AuthController@registerPassword');
        $router->get('register-shop', 'AuthController@registerShop');
        $router->get('register-success', 'AuthController@registerSuccess');
        $router->get('logout', 'AuthController@logout');
        $router->get('forget-password', 'AuthController@forgetPassword');
    });
    $router->get('weixin-article/articles', 'WeixinArticleController@articles');
    $router->get('weixin-article', 'WeixinArticleController@index');
});

/**
 * 移动端
 */
$router->group(['domain' => 'm.fmcg.com', 'namespace' => 'Mobile', 'middleware' => 'auth'], function ($router) {
    $router->get('/', 'HomeController@index');
    $router->get('category', 'CategoryController@index');
    $router->get('search', 'SearchController@index');
    $router->get('search/{shop}/shop-goods', 'SearchController@shopGoods')->where('shop', '[0-9]+');
    $router->get('goods/{goods}', 'GoodsController@detail');
    $router->get('goods', 'GoodsController@index');
    //店铺
    $router->group(['prefix' => 'shop'], function ($router) {
        $router->get('/', 'ShopController@index');
        $router->get('/{shop}', 'ShopController@detail')->where('shop', '[0-9]+');
        $router->get('/{shop}/coupons', 'ShopController@coupons')->where('shop', '[0-9]+');
        $router->get('/{shop}/delivery-area', 'ShopController@deliveryArea')->where('shop', '[0-9]+');
        $router->get('/{shop}/qr-code', 'ShopController@qrCode')->where('shop', '[0-9]+');
        $router->get('/{shop}/goods', 'ShopController@goods')->where('shop', '[0-9]+');
    });
    $router->get('cart', 'CartController@index');
    $router->get('mine', 'MineController@index');

    //订单
    $router->group(['prefix' => 'order'], function ($router) {
        $router->get('/', 'OrderController@index');
        $router->get('un-sent', 'OrderController@unSent');
        $router->get('non-payment', 'OrderController@nonPayment');
        $router->get('wait-confirm', 'OrderController@waitConfirm');
        $router->get('non-arrived', 'OrderController@nonArrived');
        $router->get('{order}', 'OrderController@detail')->where('order', '[0-9]+');
        $router->get('confirm-order', 'OrderController@confirmOrder');
        $router->get('success-order', 'OrderController@successOrder');
    });
    //支付
    $router->group(['prefix' => 'pay'], function ($router) {
        $router->get('{orderId}', 'PayController@index')->where('orderId', '[0-9]+');
        $router->get('yeepay/{orderId}', 'PayController@yeepay')->where('orderId', '[0-9]+');
        $router->get('alipay/{orderId}', 'PayController@alipay')->where('orderId', '[0-9]+');
        $router->get('balancepay/{orderId}', 'PayController@balancepay')->where('orderId', '[0-9]+');
    });

    $router->get('coupon', 'CouponController@index');
    $router->resource('shipping-address', 'ShippingAddressController');
    $router->get('like/goods', 'LikeController@goods');
    $router->get('like/shops', 'LikeController@shops');

});

/**
 * 前台登录注册
 */
$router->group(['namespace' => 'Auth'], function ($router) {
    $router->group(['prefix' => 'auth'], function ($router) {
        $router->get('login', 'AuthController@login');
        $router->get('register', 'AuthController@register');
        $router->get('register-set-password', 'AuthController@setPassword');
        $router->get('register-add-shop', 'AuthController@addShop');
        $router->get('reg-success', 'AuthController@regSuccess');
        $router->get('logout', 'AuthController@logout');
        $router->get('geetest', 'AuthController@getGeetest');
    });

    /**
     * 微信 web登录
     */
    $router->group(['prefix' => 'weixinweb-auth'], function ($router) {
        $router->get('login', 'WeixinWebAuthController@login');
        $router->any('callback', 'WeixinWebAuthController@callback');
        $router->get('bind-socialite', 'WeixinWebAuthController@bindSocialite');
    });

    /**
     * 微信
     */
    $router->group(['prefix' => 'weixin-auth'], function ($router) {
        $router->get('login', 'WeixinAuthController@login');
        $router->any('callback', 'WeixinAuthController@callback');
        $router->get('bind-socialite', 'WeixinAuthController@bindSocialite');
    });

});

/**
 * 前台无需登录模块
 */
$router->group(['namespace' => 'Index'], function ($router) {
    /**
     * 处理支付回调
     */
    $router->group(['prefix' => 'webhooks', 'namespace' => 'Webhook'], function ($router) {
        $router->controller('pingxx', 'PingxxController');
        $router->controller('yeepay', 'YeepayController');
        $router->controller('alipay', 'AlipayController');
        $router->controller('wechat', 'WechatController');
        $router->controller('union-pay', 'UnionPayController');
    });


    $router->get('test', 'HomeController@test');
    $router->get('about', 'HomeController@about');         //关于我们
    $router->get('download', 'HomeController@download');         //下载
});


$router->group(['namespace' => 'Index', 'middleware' => 'auth'], function ($router) {
    $router->get('/', 'HomeController@index');              //商家管理首页
    $router->get('shop/{shop}/search', 'ShopController@search')->where('shop', '[0-9]+');          //商家商店搜索
    $router->get('shop/{shop}', 'ShopController@detail')->where('shop', '[0-9]+');          //商家商店首页
    $router->get('shop/all-goods/{shop}/{sort?}', 'ShopController@shop')->where('shop', '[0-9]+');          //商家商店所有商品
    $router->get('shop/{sort?}', 'ShopController@index')->where('shop', '\d+');                   //商家
    $router->controller('order', 'OrderController');//订单统计
    $router->controller('order-buy', 'OrderBuyController');  //买家订单管理
    $router->controller('order-sell', 'OrderSellController');//卖家订单管理
    $router->get('my-goods/batch-create', 'MyGoodsController@batchCreate');  //批量增加商品
    $router->get('my-goods/download-template', 'MyGoodsController@downloadTemplate');  //批量增加商品
    $router->resource('my-goods', 'MyGoodsController');          //商品管理
    $router->get('goods/{goods}', 'GoodsController@detail')->where('goods', '[0-9]+');          //商品详情
    $router->get('cart', 'CartController@index');          // 购物车
    $router->controller('order', 'OrderController');       //订单
    $router->get('search', 'SearchController@index');      //商品搜索页
    $router->controller('like', 'LikeController');//收藏夹
    $router->get('yeepay/{order_id}', 'PayController@yeepay'); //易宝
    $router->get('alipay/{order_id}', 'PayController@alipay'); //支付宝
    $router->get('balancepay/{order_id}', 'PayController@balancepay'); //支付宝
    $router->group(['prefix' => 'personal', 'namespace' => 'Personal'], function ($router) {
        $router->get('shop', 'ShopController@index');          //商家信息
        $router->resource('delivery-area', 'DeliveryAreaController');          //商家信息
        $router->get('info', 'InfoController@index');          //商家信息
        $router->controller('security', 'SecurityController');          //安全设置
        $router->resource('bank', 'UserBankController', ['only' => ['edit', 'index', 'create']]);          //提现账号
        $router->resource('bill', 'BillController'/*, ['only' => ['edit', 'index', 'create']]*/);          //月对账单
        $router->resource('delivery-man', 'DeliveryManController', ['only' => ['edit', 'index', 'create']]); //配送人员
        $router->controller('finance', 'FinanceController'); //账户余额
        $router->get('customer/{user_type}', 'CustomerController@index'); // 客户列表
        $router->controller('chat', 'ChatController'); // 消息列表
        $router->resource('shipping-address', 'ShippingAddressController',
            ['only' => ['edit', 'index', 'create']]);          //提现账号
        $router->get('delivery/history', 'DeliveryController@history');
        $router->get('delivery/statistical', 'DeliveryController@statistical');//配送统计
        $router->get('delivery-report', 'DeliveryController@report');
        $router->controller('model', 'ModelController');  //模版管理
        $router->controller('dispatch-truck', 'DispatchTruckController');  //发车单
        $router->resource('coupon', 'CouponController'); // 优惠券
        $router->controller('sign', 'SignController'); //签约管理
        $router->resource('child-user', 'ChildUserController'); //子帐号
        $router->get('delivery-truck', 'DeliveryTruckController@index'); //配送车辆
    });
    //$router->get('business/union-pay/qrcode/{order_id}', 'UnionPayController@getQrCode')->where('order_id', '[0-9]+');
    //业务管理
    $router->group(['prefix' => 'business', 'namespace' => 'Business', 'middleware' => 'deposit'], function ($router) {
        $router->get('salesman/target', 'SalesmanController@target');
        $router->get('salesman/target-set', 'SalesmanController@targetSet');
        $router->resource('salesman', 'SalesmanController');
        $router->resource('salesman-customer/{salesman_customer}/export', 'SalesmanCustomerController@export');
        $router->get('salesman-customer/{salesman_customer}/stock', 'SalesmanCustomerController@getStockQuery');
        $router->get('salesman-customer/{salesman_customer}/bill', 'SalesmanCustomerController@bill');
        $router->resource('salesman-customer', 'SalesmanCustomerController');
        $router->get('report/{salesman_id}/export', 'ReportController@export');
        $router->get('report/{salesman_id}/customer-detail', 'ReportController@customerDetail');
        $router->get('report/{salesman_id}/customer-detail/export', 'ReportController@exportCustomerDetail');
        $router->get('report/export', 'ReportController@exportIndex');
        $router->resource('report', 'ReportController');
        $router->get('display-info', 'DisplayInfoController@index');
        $router->get('display-info/export', 'DisplayInfoController@export');

        $router->resource('mortgage-goods', 'MortgageGoodsController');
        $router->resource('area', 'AreaController');
        $router->group(['prefix' => 'order'], function ($router) {
            $router->get('export', 'SalesmanVisitOrderController@export');
            $router->get('order-forms', 'SalesmanVisitOrderController@orderForms');
            $router->get('return-orders', 'SalesmanVisitOrderController@returnOrders');
            $router->get('browser-export/{salesman_visit_order}',
                'SalesmanVisitOrderController@browserExport')->where('salesman_visit_order', '[0-9]+');
            $router->get('{salesman_visit_order}', 'SalesmanVisitOrderController@detail');
        });
        $router->resource('trade-request', 'TradeRequestController'); //交易请求
    });
    $router->resource('mortgage-goods', 'MortgageGoodsController');
    $router->group(['prefix' => 'order'], function ($router) {
        $router->get('export', 'SalesmanVisitOrderController@export');
        $router->get('order-forms', 'SalesmanVisitOrderController@orderForms');
        $router->get('return-orders', 'SalesmanVisitOrderController@returnOrders');
        $router->get('browser-export/{salesman_visit_order}',
            'SalesmanVisitOrderController@browserExport')->where('salesman_visit_order', '[0-9]+');
        $router->get('{salesman_visit_order}', 'SalesmanVisitOrderController@detail');
    });
    // 库存管理
    $router->group(['prefix' => 'inventory'], function ($router) {
        $router->get('in-export', 'InventoryController@inExport'); // 入库记录导出
        $router->get('out-export', 'InventoryController@outExport'); // 出库记录导出
        $router->get('detail-list-export', 'InventoryController@detailListExport'); // 出入库记录导出
        $router->controller('/', 'InventoryController');
    });

    //销售统计
    $router->get('sales-statistics', 'SalesStatisticsController@index');
    $router->get('sales-statistics/export', 'SalesStatisticsController@export');

//资产管理
    $router->group(['prefix' => 'asset'], function ($router) {
        $router->controller('/', 'AssetController');
    });

    //促销管理
    $router->group(['prefix' => 'promo'], function ($router) {
        $router->get('apply-log/{promo_apply}/detail', 'PromoController@applyLogDetail');
        $router->get('{promo}/edit', 'PromoController@edit');
        $router->get('{promo}/view', 'PromoController@view');
        $router->get('{promo}/partake', 'PromoController@partake');
        $router->controller('/', 'PromoController');
    });

    $router->get('warehouse-keeper', 'WarehouseKeeperController@index'); //仓库管理员

    $router->get('help', 'HelpController@index'); // 帮助中心
});

//子帐号
$router->group(['prefix' => 'child-user', 'namespace' => 'ChildUser', 'middleware' => 'child.auth'],
    function ($router) {
        $router->get('info', 'InfoController@index');          //商家信息
        $router->get('shop', 'ShopController@index');          //商家信息
        $router->resource('delivery-area', 'DeliveryAreaController');          //商家信息
        $router->get('security', 'SecurityController@index');          //安全设置
        $router->get('my-goods/batch-create', 'MyGoodsController@batchCreate');  //批量增加商品
        $router->get('my-goods/download-template', 'MyGoodsController@downloadTemplate');  //批量增加商品
        $router->resource('my-goods', 'MyGoodsController');          //商品管理

        $router->get('order/wait-confirm', 'OrderController@waitConfirm');
        $router->get('order/wait-send', 'OrderController@waitSend');
        $router->get('order/wait-receive', 'OrderController@waitReceive');
        $router->get('order/export', 'OrderController@export');
        $router->get('order/templete', 'OrderController@templete');
        $router->get('order/statistics', 'OrderController@statistics');
        $router->get('order/statistics-export', 'OrderController@statisticsExport');
        $router->resource('order', 'OrderController');//订单统计
        $router->resource('coupon', 'CouponController'); // 优惠券
        $router->controller('sign', 'SignController'); //签约管理
        $router->resource('delivery-man', 'DeliveryManController', ['only' => ['edit', 'index', 'create']]); //配送人员
        $router->get('delivery', 'DeliveryController@history');
        $router->get('delivery/statistical', 'DeliveryController@statistical');//配送统计
        $router->get('delivery/report', 'DeliveryController@report');
        $router->controller('chat', 'ChatController'); // 消息列表
        $router->controller('model', 'ModelController');  //模版管理
        $router->get('customer/{user_type}', 'CustomerController@index'); // 客户列表
        $router->get('salesman/target', 'SalesmanController@target');
        $router->get('salesman/target-set', 'SalesmanController@targetSet');
        $router->resource('salesman', 'SalesmanController');
        $router->get('report/{salesman_id}/export', 'ReportController@export');
        $router->get('report/{salesman_id}/customer-detail', 'ReportController@customerDetail');
        $router->get('report/{salesman_id}/customer-detail/export', 'ReportController@exportCustomerDetail');
        $router->get('report/export', 'ReportController@exportIndex');
        $router->resource('report', 'ReportController');
        $router->resource('salesman-customer/{salesman_customer}/export', 'SalesmanCustomerController@export');
        $router->resource('salesman-customer', 'SalesmanCustomerController');
        $router->group(['prefix' => 'business-order'], function ($router) {
            $router->get('export', 'SalesmanVisitOrderController@export');
            $router->get('order-forms', 'SalesmanVisitOrderController@orderForms');
            $router->get('return-orders', 'SalesmanVisitOrderController@returnOrders');
            $router->get('browser-export/{salesman_visit_order}',
                'SalesmanVisitOrderController@browserExport')->where('salesman_visit_order', '[0-9]+');
            $router->get('{salesman_visit_order}', 'SalesmanVisitOrderController@detail');
        });
        $router->resource('mortgage-goods', 'MortgageGoodsController');
        $router->get('display-info', 'DisplayInfoController@index');
        $router->get('display-info/export', 'DisplayInfoController@export');
    });

/**
 * 后台
 */
$router->group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'admin.auth'], function ($router) {
    // 首页
    $router->get('/', ['uses' => 'HomeController@getIndex']);  // 后台首页
    $router->delete('admin/batch', 'AdminController@deleteBatch');//批量删除
    $router->get('admin/password', 'AdminController@getPassword');//获取修改密码表单
    $router->put('admin/password', 'AdminController@putPassword');//修改当前管理员密码
    $router->get('admin/pay-password', 'AdminController@payPassword');//获取修改支付密码表单
    $router->put('admin/pay-password', 'AdminController@putPayPassword');//修改支付密码
    $router->put('admin/switch', 'AdminController@putSwitch');//管理员状态切换
    $router->resource('admin', 'AdminController');          //管理员管理
    $router->resource('advert-index', 'AdvertIndexController'); // 首页广告
    $router->resource('advert-user', 'AdvertUserController'); // 用户端广告
    $router->resource('advert-app', 'AdvertAppController'); // APP广告
    $router->resource('advert-category', 'AdvertCategoryController'); // 商品分类广告
    $router->resource('advert-left-category', 'AdvertLeftCategoryController');//商品分类左侧广告
    $router->resource('role', 'RoleController');
    $router->group(['prefix' => 'user'], function ($router) {
        $router->get('audit', 'UserController@audit');    //未审核账号列表
        $router->put('audit/{user}', 'UserController@auditUpdate');     //审核账号
        $router->put('multi_audit', 'UserController@multiAudit');       //批量审核
        $router->delete('batch', 'UserController@deleteBatch');         //批量删除用户
        $router->put('switch', 'UserController@putSwitch');             //批量修改用户
        $router->post('{user}/deposit', 'UserController@deposit')->where('user', '[0-9]+');             //缴纳保证金
        $router->post('expire', 'UserController@expire')->where('user', '[0-9]+');             //缴纳保证金
    });


    $router->resource('user', 'UserController');            //用户管理
    $router->post('getCategory', 'CategoryController@getCategory');  //获取属性
    $router->resource('category', 'CategoryController');            //属性管理
    $router->post('getAttr', 'AttrController@getAttr');             //获取标签
    $router->post('attr/save', 'AttrController@save');             //绑定标签到分类
    $router->resource('attr', 'AttrController');                    //标签管理
    $router->get('attr/create/{id}', 'AttrController@create')->where('id', '[0-9]+'); //添加子标签
    $router->get('images/check', 'ImagesController@check');          //商品图片审核
    $router->put('images/check-handle', 'ImagesController@checkHandle');          //商品图片审核
    $router->delete('images/batch-delete', 'ImagesController@batchDelete');          //商品图片审核
    $router->resource('images', 'ImagesController');                    //商品图片管理
    $router->resource('shop', 'ShopController', ['only' => ['edit', 'update']]); //店铺管理
    $router->controller('system-trade', 'SystemTradeInfoController');        //系统交易信息
    $router->controller('system-withdraw', 'SystemWithdrawInfoController');        //系统提现信息
    $router->controller('feedback', 'FeedbackController');             //反馈管理
    $router->controller('trade', 'TradeController');        //交易信息
    $router->controller('refund', 'RefundApplyController');        //退款申请信息
    $router->delete('promoter/batch', 'PromoterController@deleteBatch');    //批量删除推广人员
    $router->get('promoter/statistics', 'PromoterController@statistics');    //批量删除推广人员
    $router->get('promoter/export', 'PromoterController@export');    //导出
    $router->resource('promoter', 'PromoterController');             //推广人员管理
    $router->resource('operation-record', 'OperationRecordController');    //运维操作记录
    $router->controller('data-statistics', 'DataStatisticsController');    //运营数据统计
    $router->controller('statistics', 'StatisticsController');    //运营数据统计(时间段)
    $router->resource('shop-column', 'ShopColumnController');    //店铺栏目
    $router->resource('version-record', 'VersionRecordController');    //店铺栏目
    $router->controller('goods-column', 'GoodsColumnController');    //商品栏目
    $router->delete('barcode-without-images/batch', 'BarcodeWithoutImagesController@batch'); //批量删除前台用户添加商品时没有图片的条形码
    $router->get('barcode-without-images/export', 'BarcodeWithoutImagesController@export'); //导出没有图片的条形码
    $router->resource('barcode-without-images', 'BarcodeWithoutImagesController'); //前台用户添加商品时没有图片的条形码
    $router->resource('notice', 'NoticeController'); //前台用户添加商品时没有图片的条形码
    $router->get('chat', 'ChatController@index'); // 消息
    $router->controller('cache', 'CacheController'); // 消息
    $router->get('goods/import', 'GoodsController@import');//批量导入商品
    $router->group(['prefix' => 'operation-data'], function ($router) {
        $router->get('user', 'OperationDataController@user');
        $router->get('user-register', 'OperationDataController@userRegister');
        $router->get('user-export', 'OperationDataController@userExport');
        $router->get('financial', 'OperationDataController@financial');
        $router->get('financial-export', 'OperationDataController@financialExport');
        $router->get('order-create-map', 'OperationDataController@orderCreateMap');
        $router->get('order-amount', 'OperationDataController@orderAmount');
        $router->get('order-amount-export', 'OperationDataController@orderAmountExport');
        $router->get('complete-amount', 'OperationDataController@completeAmount');
        $router->get('complete-amount-export', 'OperationDataController@completeAmountExport');
        $router->get('sales-rank', 'OperationDataController@salesRank');
        $router->get('sales-rank-export', 'OperationDataController@salesRankExport');
        $router->get('goods-sales', 'OperationDataController@goodsSales');
        $router->get('goods-sales-export', 'OperationDataController@goodsSalesExport');
        $router->get('goods-sales-map/{goods_id}', 'OperationDataController@goodsSalesMap')->where('goods_id',
            '[0-9]+');;
    });
    $router->get('operation/notification', 'OperationController@notification');
    $router->get('operation/export', 'OperationController@export');
    $router->get('operation/notification-export', 'OperationController@notificationExport');
    $router->resource('operation', 'OperationController');
    $router->resource('payment-channel', 'PaymentChannelController');
    $router->group(['prefix' => 'weixin'], function ($router) {
        $router->resource('article', 'WeixinArticleController');
        $router->resource('article-banner', 'WeixinArticleBannerController');
    });
});

/**
 * 接口
 */
$router->group(['prefix' => 'api', 'namespace' => 'Api'], function ($router) {
    $router->get('/', function () {
        return view('api.index');
    });
    $router->get('/pingxx', function () {
        return view('api.pingxx');
    });

    /**
     * v1 版本
     */
    $router->group(['prefix' => 'v1', 'namespace' => 'V1'], function ($router) {
        // 接口地址
        $router->get('/', [
            'as' => 'api.v1.root',
            function () {
                return redirect('/');
            }
        ]);

        $router->controller('auth', 'AuthController');
        $router->controller('goods', 'GoodsController');                           //商品
        $router->group(['prefix' => 'shop'], function ($router) {
            $router->get('shops', 'ShopController@shops');                        //热门店铺
            $router->get('salesman', 'ShopController@salesman');                        //热门店铺
            $router->get('{shop}', 'ShopController@detail')->where('shop', '[0-9]+');                        //店铺详细
            $router->get('{shop}/goods', 'ShopController@goods')->where('shop', '[0-9]+');                  //店铺商品
            $router->get('{shop}/adverts', 'ShopController@adverts')->where('shop', '[0-9]+');                  //店铺商品
            $router->get('{shop}/extend', 'ShopController@extend')->where('shop',
                '[0-9]+');                   //店铺商品
            $router->get('get-shops-by-ids', 'ShopController@getShopsByids');                   //店铺列表
            $router->get('all', 'ShopController@allShops');
            $router->get('{shop}/category', 'ShopController@category');         //获取店铺分类
        });
        $router->controller('version', 'VersionInfoController');
        $router->controller('file', 'FileController');                              // 文件上传
        $router->get('categories/{id}/attrs', 'CategoryController@getAttr');         //获取标签
        $router->get('attr/{id}/second', 'AttrController@secondAttr');         //获取二级分类
        $router->get('categories', 'CategoryController@getCategory');         //获取标签
        $router->post('categories/all', 'CategoryController@getAllCategory');         //获取所有标签
        $router->group(['prefix' => 'my-goods'], function ($router) {
            $router->put('shelve', 'MyGoodsController@shelve');                //商品上下架
            $router->put('gift', 'MyGoodsController@gift');                //商品上下架
            $router->post('{my_goods}/mortgage', 'MyGoodsController@mortgage');                //商品上下架
            $router->post('{my_goods}/promo', 'MyGoodsController@promo');                //设置促销商品
            $router->put('{my_goods}/warning', 'MyGoodsController@setWarning');                //设置促销商品
            $router->put('batch-shelve', 'MyGoodsController@batchShelve');     //商品批量上下架
            $router->get('images', 'MyGoodsController@getImages');
            $router->post('import', 'MyGoodsController@import');
            $router->get('goods', 'MyGoodsController@goods');
        });
        $router->resource('my-goods', 'MyGoodsController');
        $router->group(['prefix' => 'personal', 'namespace' => 'Personal'], function ($router) {
            $router->group(['prefix' => 'security'], function ($router) {
                $router->get('send-sms', 'SecurityController@sendSms');//安全设置发送原密保手机验证码
                $router->post('validate-backup-sms', 'SecurityController@validateBackupSms');//密保手机验证码验证
                $router->get('new-backup-sms', 'SecurityController@sendNewBackupSms');//获取新密保手机验证码
                $router->post('edit-backup-phone', 'SecurityController@editBackupPhone');//设置新密保手机
                $router->post('validate-old-password', 'SecurityController@validateOldPassword');//验证原密码
            });
            $router->put('shop/{shop}', 'ShopController@shop');          //商家信息
            $router->resource('delivery-area', 'DeliveryAreaController',
                ['only' => ['index', 'store', 'update', 'destroy']]);          //商家配送区域
            $router->get('order-data', 'ShopController@orderData');//商家首页订单统计信息
            $router->put('password', 'SecurityController@password');          //修改密码
            $router->post('edit-password', 'SecurityController@editPassword');//修改
            $router->put('bank-default/{bank}', 'UserBankController@bankDefault');//设置默认提现账号
            $router->get('bank-info', 'UserBankController@banks');  //所有银行信息
            $router->resource('bank', 'UserBankController',
                ['only' => ['index', 'store', 'show', 'update', 'destroy']]);          //提现账号
            $router->resource('delivery-man', 'DeliveryManController',
                ['only' => ['index', 'store', 'update', 'destroy']]);          //配送人员
            $router->put('shipping-address/default/{address}', 'ShippingAddressController@addressDefault');
            $router->resource('shipping-address', 'ShippingAddressController');          //收货地址
            $router->controller('finance', 'FinanceController');    //提现相关操作
            $router->controller('model', 'ModelController');  //模版管理
            $router->resource('coupon', 'CouponController'); // 优惠券
            $router->controller('sign', 'SignController'); //签约管理
            $router->put('child-user/status/{child_user}', 'ChildUserController@status'); //子帐号禁/启用
            $router->post('child-user/bind-node/{child_user}', 'ChildUserController@bindNode'); //子帐号绑定节点权限
            $router->resource('child-user', 'ChildUserController'); //子帐号
            $router->get('index-node', 'IndexNodeController@index'); //获取所有节点
            $router->put('delivery-truck/status/{delivery_truck}',
                'DeliveryTruckController@status')->where('delivery_truck', '[0-9]+');
            $router->resource('delivery-truck', 'DeliveryTruckController', ['only' => ['store', 'update', 'destroy']]);

        });
        $router->controller('cart', 'CartController');
        $router->controller('order', 'OrderController');
        $router->controller('like', 'LikeController');
        $router->post('address/street', 'AddressController@street');
        $router->post('address/province-id', 'AddressController@getProvinceIdByName');
        $router->get('address/city-detail', 'AddressController@getCityDetail');
        $router->group(['prefix' => 'coupon'], function ($router) {
            $router->get('user-coupon/{expire?}', 'CouponController@userCoupon');
            $router->post('receive/{coupon}', 'CouponController@receive');
            $router->get('{shop}', 'CouponController@coupon');
            $router->get('coupon-num/{shop}', 'CouponController@couponNum');
        });
        $router->controller('push', 'PushController');//推送设备
        //获取支付charge
        $router->group(['prefix' => 'pay'], function ($router) {
            $router->get('charge/{order_id}', 'PayController@charge')->where('order_id', '[0-9]+');
            $router->post('balancepay/{order_id}', 'PayController@balancepay')->where('order_id', '[0-9]+');
            $router->any('refund/{order_id}', 'PayController@refund')->where('order_id', '[0-9]+');
            $router->get('success-url', 'PayController@successUrl');
            $router->get('cancel-url', 'PayController@cancelUrl');
        });
        $router->controller('pos', 'PosController');             //pos机付款
        $router->post('js-errors', ['uses' => 'PublicController@jsErrorStore']); // 前端JS错误记录
        //获取移动端广告
        $router->get('advert', 'AdvertController@index');
        $router->post('feedback', 'FeedbackController@index'); //意见反馈
        //仓管APP接口
        $router->group(['prefix' => 'wk', 'namespace' => 'WarehouseKeeper'], function ($router) {
            $router->post('login', 'AuthController@login');//处理仓管人员登陆
            $router->get('logout', 'AuthController@logout');//处理仓管人员登出

            $router->group(['middleware' => 'wk.auth'], function ($router) {
                //个人中心
                $router->group(['prefix' => 'person-center'], function ($router) {
                    $router->post('password', 'PersonCenterController@modifyPassword');//修改密码
                });
                //订单
                $router->group(['prefix' => 'order'], function ($router) {
                    $router->get('list', 'OrderController@nonSendOrder');//获取订单列表
                    $router->get('{order_id}/detail', 'OrderController@detail');//获取订单详情
                    $router->put('{order_id}/modify', 'OrderController@modifyOrder');//修改订单
                    $router->delete('order-goods-delete/{order_goods}', 'OrderController@orderGoodsDelete');//订单商品删除
                });
                //车辆
                $router->group(['prefix' => 'dispatch-truck'], function ($router) {
                    $router->get('trucks', 'DispatchTruckController@trucks');//获取车辆列表
                    $router->get('delivery-mans', 'DispatchTruckController@deliveryMans');//获取配送员列表
                    $router->post('order', 'DispatchTruckController@addOrder');//添加订单到发车单
                    $router->post('delivery-mans', 'DispatchTruckController@addDeliveryMans');//添加配送员到发车单
                    $router->post('voucher-create', 'DispatchTruckController@createDispatchTruckVoucher');//创建发车单
                    $router->get('voucher-detail', 'DispatchTruckController@getDispatchTruckVoucherDetail');//获取发车单详情
                    $router->get('history', 'DispatchTruckController@history');//获取发车历史
                    $router->get('{dispatch_truck}/goods-statistical',
                        'DispatchTruckController@dispatchGoodsStatistical');//获取发车单订单单个商品统计
                    $router->get('{dispatch_truck}/return-goods-statistical',
                        'DispatchTruckController@dispatchReturnGoodsStatistical');//获取发车单退货单个商品统计
                    $router->put('{dispatch_truck}/change-truck', 'DispatchTruckController@changeTruck');//换车
                    $router->put('{dispatch_truck}/change-sort', 'DispatchTruckController@dispatchOrderSort');//订单排序
                    $router->delete('delete-order/{order_id}',
                        'DispatchTruckController@deleteDispatchTruckOrder');//删除发车单内商品
                    $router->post('{dtv_id}/truck-back', 'DispatchTruckController@confirmTruckBack');//确认回车
                    //车销
                    $router->post('create-truck-sales', 'DispatchTruckController@createTruckSalesVoucher'); // 创建车销单
                    $router->get('goods', 'DispatchTruckController@goodsList'); // 获取商品列表
                    $router->post('{truck_sales}/goods', 'DispatchTruckController@addGoods'); // 添加商品到车销单
                    $router->get('salesman', 'DispatchTruckController@salesmanList'); // 获取业务员列表
                    $router->post('{truck_sales}/salesman', 'DispatchTruckController@addSalesman'); // 添加业务员到车销单


                    $router->delete('{dtv_id}/cancel', 'DispatchTruckController@cancelCreate'); // 取消创建
                    $router->delete('{dtv_id}/delete-sales-goods', 'DispatchTruckController@deleteGoods'); // 取消创建
                });
            });
        });
        //司机版APP接口
        $router->group(['prefix' => 'delivery'], function ($router) {
            $router->get('index', 'DeliveryController@index');//配送人员登陆页面 测试用
            $router->post('login', 'DeliveryController@login');//处理配送人员登陆
            $router->get('orders', 'DeliveryController@orders');//已分配的订单信息
            $router->get('history-orders', 'DeliveryController@historyOrders');//已配送历史订单信息
            $router->post('order/{order_id}/cancel', 'DeliveryController@cancelOrder')->where('order_id', '[0-9]+');
            $router->get('detail', 'DeliveryController@detail');//配送订单详情
            $router->get('deal-delivery', 'DeliveryController@dealDelivery');//处理完成配送
            $router->get('logout', 'DeliveryController@logout');//退出登陆
            $router->post('update-order', 'DeliveryController@updateOrder');//修改订单商品数量
            $router->get('delivery-statistical', 'DeliveryController@statisticalDelivery');//配送统计
            $router->delete('order-goods-delete/{order_goods_id}',
                'DeliveryController@orderGoodsDelete')->where('order_goods_id', '[0-9]+'); //订单商品删除
            $router->post('modify-password', 'DeliveryController@modifyPassword');//修改密码
            $router->get('latest-version', 'DeliveryController@latestVersion');//检查最新版本
            $router->get('now', 'DeliveryController@nowDispatchVoucherDetail');//当前配送单
            $router->get('order-complete/{order_id}', 'DeliveryController@orderComplete')->where('order_id', '[0-9]+');
        });
        //业务管理
        $router->post('business/auth/login', 'Business\AuthController@login');
        $router->get('business/auth/logout', 'Business\AuthController@logout');
        $router->group(['prefix' => 'business', 'namespace' => 'Business', 'middleware' => 'salesman.auth'],
            function ($router) {
                $router->group(['prefix' => 'salesman'], function ($router) {
                    $router->get('home-data', 'SalesmanController@homeData');
                    $router->get('export-target', 'SalesmanController@exportTarget');
                    $router->delete('batch-delete', 'SalesmanController@batchDelete');
                    $router->put('target-set', 'SalesmanController@targetSet');
                    $router->get('{salesman_id}/goods-target', 'SalesmanController@goodsTarget')->where('salesman_id',
                        '[0-9]+');
                    $router->put('update-by-app', 'SalesmanController@updateByApp');
                    $router->post('lock', 'SalesmanController@postLock');
                    $router->put('password', 'SalesmanController@password');  //修改密码
                });
                $router->resource('area', 'AreaController');
                $router->resource('salesman', 'SalesmanController');

                $router->group(['prefix' => 'salesman-customer'], function ($router) {
                    $router->put('update-store-type/{customer}', 'SalesmanCustomerController@updateStoreType');
                    $router->get('store-type', 'SalesmanCustomerController@getStoreType');
                    $router->put('update-by-app/{salesman_customer}',
                        'SalesmanCustomerController@updateByApp');
                    $router->post('add-sale-goods', 'SalesmanCustomerController@addSaleGoods');
                    $router->get('sale-goods', 'SalesmanCustomerController@saleGoods');
                    $router->delete('delete-sale-goods', 'SalesmanCustomerController@deleteSaleGoods');
                    $router->get('customer-display-fee', 'SalesmanCustomerController@customerDisplayFee');//客户陈列费发放情况
                    $router->post('display-fee', 'SalesmanCustomerController@displayFee');//陈列费发放情况
                    $router->get('purchased-goods', 'SalesmanCustomerController@purchasedGoods');//客户曾购买商品
                    $router->post('apply-bind-relation', 'SalesmanCustomerController@bindRelation');//客户申请绑定业务关系
                    $router->get('passed-promo', 'SalesmanCustomerController@getPassedPromos');//获取已通过活动
                });

                $router->resource('salesman-customer', 'SalesmanCustomerController');
                $router->get('visit/can-add/{customer_id}', 'SalesmanVisitController@canAdd')
                    ->where('customer_id', '[0-9]+');
                $router->get('visit/surplus-display-fee', 'SalesmanVisitController@surplusDisplayFee');//获取月份陈列费剩余情况
                $router->get('visit/surplus-mortgage-goods',
                    'SalesmanVisitController@surplusMortgageGoods');//获取月份陈列商品剩余情况
                $router->post('visit/{visit}/add-photos', 'SalesmanVisitController@addPhotos');
                $router->resource('visit', 'SalesmanVisitController');

                $router->group(['prefix' => 'order'], function ($router) {
                    $router->delete('{order_id}', 'SalesmanVisitOrderController@destroy')->where('order_id', '[0-9]+');
                    $router->delete('goods-delete/{goods_id}',
                        'SalesmanVisitOrderController@goodsDelete')->where('goods_id', '[0-9]+');
                    $router->delete('mortgage-goods-delete', 'SalesmanVisitOrderController@mortgageGoodsDelete');
                    $router->delete('gift/{id}', 'SalesmanVisitOrderController@gift')->where('id', '[0-9]+');
                    $router->put('gift/{id}', 'SalesmanVisitOrderController@upGift')->where('id', '[0-9]+');
                    $router->post('update-all/{salesman_order_id}', 'SalesmanVisitOrderController@updateAll');
                    $router->get('order-forms', 'SalesmanVisitOrderController@orderForms');
                    $router->get('return-orders', 'SalesmanVisitOrderController@returnOrders');
                    $router->post('{salesman_visit_order}/sync', 'SalesmanVisitOrderController@sync');
                    $router->post('batch-sync', 'SalesmanVisitOrderController@batchSync');
                    $router->put('batch-pass', 'SalesmanVisitOrderController@batchPass');
                    $router->put('change', 'SalesmanVisitOrderController@updateOrderGoods');
                    $router->put('update-order-display-fee', 'SalesmanVisitOrderController@updateOrderDisplayFee');
                    $router->put('{salesman_visit_order}', 'SalesmanVisitOrderController@update');
                    $router->get('order-detail/{order_id}', 'SalesmanVisitOrderController@orderDetail');
                    $router->get('return-order-detail/{order_id}', 'SalesmanVisitOrderController@returnOrderDetail');
                    $router->get('display-fee-surplus', 'SalesmanVisitOrderController@displayFeeSurplus');//查询陈列费剩余
                    $router->get('mortgage-goods-surplus',
                        'SalesmanVisitOrderController@mortgageGoodsSurplus');//查询陈列商品剩余
                    $router->get('order-complete/{order_id}',
                        'SalesmanVisitOrderController@orderComplete')->where('order_id', '[0-9]+');

                });
                //抵费商品
                $router->group(['prefix' => 'mortgage-goods'], function ($router) {
                    $router->get('/', 'MortgageGoodsController@index');
                    $router->put('{mortgage_goods}/status', 'MortgageGoodsController@status'); //启/禁用
                    $router->put('batch-status', 'MortgageGoodsController@batchStatus');//批量启/禁用
                    $router->put('{mortgage_goods}', 'MortgageGoodsController@update'); //修改
                    $router->delete('batch-delete', 'MortgageGoodsController@batchDestroy'); //移除
                    $router->delete('{mortgage_goods}', 'MortgageGoodsController@destroy'); //移除
                });

                $router->get('gift', 'GiftController@index');
                $router->group(['prefix' => 'goods'], function ($router) {
                    $router->get('categories', 'GoodsController@category'); //启/禁用
                    $router->get('/', 'GoodsController@goods'); //启/禁用
                });
                $router->group(['prefix' => 'asset'], function ($router) {
                    $router->get('/', 'AssetController@index'); // 获取资产列表
                    $router->get('/apply', 'AssetController@applyList'); // 资产申请列表
                    $router->post('/apply', 'AssetController@applyCreate'); // 资产申请
                    $router->put('/use-date/{asset_apply}', 'AssetController@addUseDate'); // 添加使用时间
                    $router->delete('/apply/{asset_apply}', 'AssetController@applyDelete'); // 删除申请
                });
                $router->group(['prefix' => 'promo'], function ($router) {
                    $router->get('/', 'PromoController@index'); // 获取活动列表
                    $router->get('/apply', 'PromoController@applyList'); // 业务员申请促销活动列表
                    $router->post('/apply', 'PromoController@applyCreate'); // 申请促销活动
                    $router->delete('/apply/{promo_apply}', 'PromoController@applyDelete'); // 删除申请
                    $router->get('/apply/pass', 'PromoController@applyPass'); // 通过申请列表
                });
                $router->group(['prefix' => 'trade-request'], function ($router) {
                    $router->post('pass', 'TradeRequestController@pass'); //通过
                });

            });
        //微信支付
        $router->group(['prefix' => 'wechat-pay'], function ($router) {
            $router->get('qrcode/{order_id}', 'WechatPayController@getQrCode')->where('order_id', '[0-9]+');
            $router->get('renew-qrcode', 'WechatPayController@renewQrCode');
            $router->get('order-pay-status/{order_id}', 'WechatPayController@orderPayStatus')->where('order_id',
                '[0-9]+');
        });
        //银联支付
        $router->group(['prefix' => 'union-pay'], function ($router) {
            $router->get('qrcode/{order_id}', 'UnionPayController@getQrCode')->where('order_id', '[0-9]+');
            $router->get('order-pay-status/{order_id}', 'UnionPayController@orderPayStatus')->where('order_id',
                '[0-9]+');
        });
        //支付渠道
        $router->get('payment-channel', 'PaymentChannelController@index');
        //库存
        $router->group(['prefix' => 'inventory'], function ($router) {
            $router->controller('/', 'InventoryController');
        });
        //资产管理
        $router->group(['prefix' => 'asset'], function ($router) {
            $router->group(['prefix' => 'apply'], function ($router) {
                $router->put('review/{asset_apply}', 'AssetController@review');
                $router->put('delete/{asset_apply}', 'AssetController@delete');
                $router->put('modify/{asset_apply}', 'AssetController@modify');
                $router->put('use-date/{asset_apply}', 'AssetController@useDate');
            });
            $router->controller('/', 'AssetController');
        });

        //促销管理
        $router->group(['prefix' => 'promo'], function ($router) {
            $router->put('apply/pass/{promo_apply}', 'PromoController@applyPass');
            $router->post('apply/{promo_apply}/partake-order', 'PromoController@partakeOrderDetail');//参与促销活动订单详情
            $router->put('apply/edit/{promo_apply}', 'PromoController@applyEdit');
            $router->put('apply/delete/{promo_apply}', 'PromoController@applyDelete');
            $router->put('status/{promo}', 'PromoController@status');
            $router->post('edit/{promo}', 'PromoController@edit');
            $router->get('goods', 'PromoController@getGoods');
            $router->post('add', 'PromoController@add');
            $router->put('goods/{promo_goods}/status', 'PromoController@goodsStatus');
            $router->post('goods/{promo_goods}/destroy', 'PromoController@goodsDestroy');
            $router->put('goods/batch-destroy', 'PromoController@goodsBatchDestroy');
            $router->put('goods/batch-status', 'PromoController@goodsBatchStatus');
        });
        //订单模板
        $router->put('templete/default/{templeteId}', 'TempleteController@default')->where('templeteId', '[0-9]+');;
        $router->resource('templete', 'TempleteController');

        $router->post('child-user/auth/login', 'ChildUser\AuthController@login');
        $router->group(['prefix' => 'child-user', 'namespace' => 'ChildUser', 'middleware' => 'child.auth'],
            function ($router) {
                $router->get('shop/order-data', 'ShopController@orderData');//商家首页订单统计信息
                $router->put('shop/{shop}', 'ShopController@shop');          //商家信息
                $router->resource('delivery-area', 'DeliveryAreaController',
                    ['only' => ['index', 'store', 'update', 'destroy']]);          //商家配送区域
                $router->resource('delivery-man', 'DeliveryManController',
                    ['only' => ['index', 'store', 'update', 'destroy']]);          //配送人员
                $router->put('security', 'SecurityController@update');          //修改密码
                $router->group(['prefix' => 'my-goods'], function ($router) {
                    $router->put('shelve', 'MyGoodsController@shelve');                //商品上下架
                    $router->put('gift', 'MyGoodsController@gift');                //商品上下架
                    $router->post('{my_goods}/mortgage', 'MyGoodsController@mortgage');                //商品上下架
                    $router->put('batch-shelve', 'MyGoodsController@batchShelve');     //商品批量上下架
                    $router->get('images', 'MyGoodsController@getImages');
                    $router->post('import', 'MyGoodsController@import');
                });
                $router->resource('my-goods', 'MyGoodsController');
                $router->controller('order', 'OrderController');
                //订单模板
                $router->put('templete/default/{templeteId}', 'TempleteController@default')->where('templeteId',
                    '[0-9]+');;
                $router->resource('templete', 'TempleteController');
                $router->resource('coupon', 'CouponController'); // 优惠券
                $router->controller('model', 'ModelController');  //模版管理
                $router->group(['prefix' => 'salesman'], function ($router) {
                    $router->get('home-data', 'SalesmanController@homeData');
                    $router->get('export-target', 'SalesmanController@exportTarget');
                    $router->delete('batch-delete', 'SalesmanController@batchDelete');
                    $router->put('target-set', 'SalesmanController@targetSet');
                    $router->put('update-by-app', 'SalesmanController@updateByApp');
                    $router->post('lock', 'SalesmanController@postLock');
                    $router->put('password', 'SalesmanController@password');  //修改密码
                });
                $router->resource('salesman', 'SalesmanController');
                //抵费商品
                $router->group(['prefix' => 'mortgage-goods'], function ($router) {
                    $router->get('/', 'MortgageGoodsController@index');
                    $router->put('{mortgage_goods}/status', 'MortgageGoodsController@status'); //启/禁用
                    $router->put('batch-status', 'MortgageGoodsController@batchStatus');//批量启/禁用
                    $router->put('{mortgage_goods}', 'MortgageGoodsController@update'); //修改
                    $router->delete('batch-delete', 'MortgageGoodsController@batchDestroy'); //移除
                    $router->delete('{mortgage_goods}', 'MortgageGoodsController@destroy'); //移除
                });
                $router->resource('salesman-customer', 'SalesmanCustomerController');
                $router->group(['prefix' => 'business-order'], function ($router) {
                    $router->delete('{order_id}', 'SalesmanVisitOrderController@destroy')->where('order_id', '[0-9]+');
                    $router->delete('goods-delete/{goods_id}',
                        'SalesmanVisitOrderController@goodsDelete')->where('goods_id', '[0-9]+');
                    $router->delete('mortgage-goods-delete', 'SalesmanVisitOrderController@mortgageGoodsDelete');
                    $router->delete('gift/{id}', 'SalesmanVisitOrderController@gift')->where('id', '[0-9]+');
                    $router->put('gift/{id}', 'SalesmanVisitOrderController@upGift')->where('id', '[0-9]+');
                    $router->post('update-all/{salesman_order_id}', 'SalesmanVisitOrderController@updateAll');
                    $router->get('order-forms', 'SalesmanVisitOrderController@orderForms');
                    $router->get('return-orders', 'SalesmanVisitOrderController@returnOrders');
                    $router->post('{salesman_visit_order}/sync', 'SalesmanVisitOrderController@sync');
                    $router->post('batch-sync', 'SalesmanVisitOrderController@batchSync');
                    $router->put('batch-pass', 'SalesmanVisitOrderController@batchPass');
                    $router->put('change', 'SalesmanVisitOrderController@updateOrderGoods');
                    $router->put('update-order-display-fee', 'SalesmanVisitOrderController@updateOrderDisplayFee');
                    $router->put('{salesman_visit_order}', 'SalesmanVisitOrderController@update');
                    $router->get('order-detail/{order_id}', 'SalesmanVisitOrderController@orderDetail');
                    $router->get('return-order-detail/{order_id}', 'SalesmanVisitOrderController@returnOrderDetail');
                    $router->get('display-fee-surplus', 'SalesmanVisitOrderController@displayFeeSurplus');//查询陈列费剩余
                    $router->get('mortgage-goods-surplus',
                        'SalesmanVisitOrderController@mortgageGoodsSurplus');//查询陈列商品剩余

                });
            }
        );
        $router->put('warehouse-keeper/status/{warehouse_keeper}',
            'warehouseKeeperController@status')->where('warehouse_keeper', '[0-9]+');
        $router->resource('warehouse-keeper', 'WarehouseKeeperController', ['only' => ['store', 'update', 'destroy']]);
    });
});