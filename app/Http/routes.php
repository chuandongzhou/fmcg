<?php

/**
 * 登录注册
 */
$router->group(['prefix' => 'auth', 'namespace' => 'Auth'], function ($router) {
    $router->get('login', 'AuthController@login');
    $router->get('register', 'AuthController@register');
    $router->get('register-set-password', 'AuthController@setPassword');
    $router->get('register-add-shop', 'AuthController@addShop');
    $router->get('reg-success', 'AuthController@regSuccess');
    $router->get('logout', 'AuthController@logout');
    $router->get('geetest', 'AuthController@getGeetest');
});

/**
 * 后台登录
 */
$router->group(['prefix' => 'admin/auth', 'namespace' => 'Admin'], function ($router) {
    $router->controller('/', 'AuthController');
});


/**
 * 处理支付回调
 */
$router->controller('webhooks/pingxx', 'Index\Webhook\PingxxController');
$router->controller('webhooks/yeepay', 'Index\Webhook\YeepayController');
$router->controller('webhooks/alipay', 'Index\Webhook\AlipayController');

/**
 * 前台
 *
 */
$router->group(['namespace' => 'Index', 'middleware' => 'auth'], function ($router) {
    $router->get('/test', 'HomeController@test');              //商家管理首页
    $router->get('/', 'HomeController@index');              //商家管理首页
    $router->get('about', 'HomeController@about');         //关于我们

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
        $router->resource('delivery-man', 'DeliveryManController', ['only' => ['edit', 'index', 'create']]); //配送人员
        $router->controller('finance', 'FinanceController'); //账户余额
        $router->get('customer/{user_type}', 'CustomerController@index'); // 客户列表
        $router->controller('chat', 'ChatController'); // 消息列表
        $router->resource('shipping-address', 'ShippingAddressController',
            ['only' => ['edit', 'index', 'create']]);          //提现账号
        $router->get('delivery', 'DeliveryController@historyDelivery');
        $router->get('delivery-statistical', 'DeliveryController@statisticalDelivery');//配送统计
        $router->get('delivery-report', 'DeliveryController@report');
        $router->controller('model', 'ModelController');  //模版管理
        $router->resource('coupon', 'CouponController'); // 优惠券
    });

    //业务管理
    $router->group(['prefix' => 'business', 'namespace' => 'Business'], function ($router) {
        $router->get('salesman/target', 'SalesmanController@target');
        $router->resource('salesman', 'SalesmanController');
        $router->resource('salesman-customer/{salesman_customer}/export', 'SalesmanCustomerController@export');
        $router->resource('salesman-customer', 'SalesmanCustomerController');
        $router->get('report/{salesman_id}/export', 'ReportController@export');
        $router->get('report/export', 'ReportController@exportIndex');
        $router->resource('report', 'ReportController');
        $router->resource('mortgage-goods', 'MortgageGoodsController');
        $router->group(['prefix' => 'order'], function ($router) {
            $router->get('export', 'SalesmanVisitOrderController@export');
            $router->get('order-forms', 'SalesmanVisitOrderController@orderForms');
            $router->get('return-orders', 'SalesmanVisitOrderController@returnOrders');
            $router->get('browser-export/{salesman_visit_order}',
                'SalesmanVisitOrderController@browserExport')->where('salesman_visit_order', '[0-9]+');
            $router->get('{salesman_visit_order}', 'SalesmanVisitOrderController@detail');
        });
    });

    $router->get('help', 'HelpController@index'); // 帮助中心
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
    $router->put('admin/switch', 'AdminController@putSwitch');//管理员状态切换
    $router->resource('admin', 'AdminController');          //管理员管理
    $router->resource('advert-index', 'AdvertIndexController'); // 首页广告
    $router->resource('advert-user', 'AdvertUserController'); // 用户端广告
    $router->resource('advert-app', 'AdvertAppController'); // APP广告
    $router->resource('advert-category', 'AdvertCategoryController'); // 商品分类广告
    $router->resource('advert-left-category', 'AdvertLeftCategoryController');//商品分类左侧广告
    $router->resource('role', 'RoleController');
    $router->get('user/audit', 'UserController@audit');    //未审核账号列表
    $router->put('user/audit/{user}', 'UserController@auditUpdate');    //审核账号
    $router->put('user/multi_audit', 'UserController@multiAudit');    //批量审核
    $router->delete('user/batch', 'UserController@deleteBatch');//批量删除用户
    $router->put('user/switch', 'UserController@putSwitch');//批量修改用户
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
        $router->get('goods-sales-map/{goods_id}', 'OperationDataController@goodsSalesMap')->where('goods_id', '[0-9]+');;
    });
    $router->get('operation/notification', 'OperationController@notification');
    $router->get('operation/export', 'OperationController@export');
    $router->get('operation/notification-export', 'OperationController@notificationExport');
    $router->resource('operation', 'OperationController');

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

        $router->controller('goods', 'GoodsController');                           //商品
        $router->group(['prefix' => 'shop'], function ($router) {
            $router->get('shops', 'ShopController@shops');                        //热门店铺
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
            $router->post('{my_goods}/mortgage', 'MyGoodsController@mortgage');                //商品上下架
            $router->put('batch-shelve', 'MyGoodsController@batchShelve');     //商品批量上下架
            $router->get('images', 'MyGoodsController@getImages');
            $router->post('import', 'MyGoodsController@import');
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
            //  $router->put('password', 'SecurityController@password');          //修改密码
            $router->post('edit-password', 'SecurityController@editPassword');//修改
            $router->put('bank-default/{bank}', 'UserBankController@bankDefault');//设置默认提现账号
            $router->get('bank-info', 'UserBankController@banks');  //所有银行信息
            $router->resource('bank', 'UserBankController',
                ['only' => ['index', 'store', 'update', 'destroy']]);          //提现账号
            $router->resource('delivery-man', 'DeliveryManController',
                ['only' => ['index', 'store', 'update', 'destroy']]);          //配送人员
            $router->put('shipping-address/default/{address}', 'ShippingAddressController@addressDefault');
            $router->resource('shipping-address', 'ShippingAddressController');          //收货地址
            $router->controller('finance', 'FinanceController');    //提现相关操作
            $router->controller('model', 'ModelController');  //模版管理
            $router->resource('coupon', 'CouponController'); // 优惠券

        });
        $router->controller('cart', 'CartController');
        $router->controller('order', 'OrderController');
        $router->controller('like', 'LikeController');
        $router->post('address/street', 'AddressController@street');
        $router->post('address/province-id', 'AddressController@getProvinceIdByName');
        $router->get('address/city-detail', 'AddressController@getCityDetail');
        $router->controller('auth', 'AuthController');
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
        //司机版APP接口
        $router->group(['prefix' => 'delivery'], function ($router) {
            $router->get('index', 'DeliveryController@index');//配送人员登陆页面 测试用
            $router->post('login', 'DeliveryController@login');//处理配送人员登陆
            $router->get('orders', 'DeliveryController@orders');//已分配的订单信息
            $router->get('history-orders', 'DeliveryController@historyOrders');//已配送历史订单信息
            $router->get('detail', 'DeliveryController@detail');//配送订单详情
            $router->get('deal-delivery', 'DeliveryController@dealDelivery');//处理完成配送
            $router->get('logout', 'DeliveryController@logout');//退出登陆
            $router->post('update-order', 'DeliveryController@updateOrder');//修改订单商品数量
            $router->get('delivery-statistical', 'DeliveryController@statisticalDelivery');//配送统计
            $router->delete('order-goods-delete/{order_goods_id}',
                'DeliveryController@orderGoodsDelete')->where('order_goods_id', '[0-9]+'); //订单商品删除
            $router->post('modify-password', 'DeliveryController@modifyPassword');//修改密码
            $router->get('latest-version', 'DeliveryController@latestVersion');//检查最新版本
        });
        //业务管理
        $router->group(['prefix' => 'business', 'namespace' => 'Business'], function ($router) {
            $router->post('auth/login', 'AuthController@login');
            $router->get('auth/logout', 'AuthController@logout');
            $router->group(['prefix' => 'salesman'], function ($router) {
                $router->get('home-data', 'SalesmanController@homeData');
                $router->get('export-target', 'SalesmanController@exportTarget');
                $router->delete('batch-delete', 'SalesmanController@batchDelete');
                $router->put('target-set', 'SalesmanController@targetSet');
                $router->put('update-by-app', 'SalesmanController@updateByApp');
            });

            $router->post('salesman/lock','SalesmanController@postLock');
            $router->put('salesman/password', 'SalesmanController@password');  //修改密码
            $router->resource('salesman', 'SalesmanController');

            $router->group(['prefix' => 'salesman-customer'], function ($router) {
                $router->put('update-by-app/{salesman_customer}',
                    'SalesmanCustomerController@updateByApp');
                $router->post('add-sale-goods', 'SalesmanCustomerController@addSaleGoods');
                $router->get('sale-goods', 'SalesmanCustomerController@saleGoods');
                $router->delete('delete-sale-goods', 'SalesmanCustomerController@deleteSaleGoods');
                $router->get('customer-display-fee', 'SalesmanCustomerController@customerDisplayFee');//客户陈列费发放情况
                $router->post('display-fee', 'SalesmanCustomerController@displayFee');//陈列费发放情况
                $router->get('purchased-goods','SalesmanCustomerController@purchasedGoods');//客户曾购买商品
            });

            $router->resource('salesman-customer', 'SalesmanCustomerController');
            $router->get('visit/can-add/{customer_id}', 'SalesmanVisitController@canAdd')
                ->where('customer_id', '[0-9]+');
            $router->get('visit/surplus-display-fee', 'SalesmanVisitController@surplusDisplayFee');//获取月份陈列费剩余情况
            $router->get('visit/surplus-mortgage-goods', 'SalesmanVisitController@surplusMortgageGoods');//获取月份陈列商品剩余情况
            $router->resource('visit', 'SalesmanVisitController');

            $router->group(['prefix' => 'order'], function ($router) {
                $router->delete('{order_id}', 'SalesmanVisitOrderController@destroy')->where('order_id', '[0-9]+');
                $router->delete('goods-delete/{goods_id}',
                    'SalesmanVisitOrderController@goodsDelete')->where('goods_id', '[0-9]+');
                $router->delete('mortgage-goods-delete', 'SalesmanVisitOrderController@mortgageGoodsDelete');
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
                $router->get('mortgage-goods-surplus', 'SalesmanVisitOrderController@mortgageGoodsSurplus');//查询陈列商品剩余

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

            $router->group(['prefix' => 'goods'], function ($router) {
                $router->get('categories', 'GoodsController@category'); //启/禁用
                $router->get('/', 'GoodsController@goods'); //启/禁用
            });

        });
        $router->group(['prefix' => 'wechat-pay'], function ($router) {
            $router->get('qrcode/{order_id}', 'WechatPayController@getQrCode')->where('order_id', '[0-9]+');
            $router->get('order-pay-status/{order_id}', 'WechatPayController@orderPayStatus')->where('order_id',
                '[0-9]+');
            $router->any('pay-result', 'WechatPayController@payResult');
        });

    });
});