<?php

/**
 * 登录注册
 */
$router->group(['prefix' => 'auth', 'namespace' => 'Auth'], function ($router) {
    $router->get('login', 'AuthController@login');
    $router->get('register', 'AuthController@register');
    $router->get('reg-success', 'AuthController@regSuccess');
    $router->get('logout', 'AuthController@logout');
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

/**
 * 前台
 *
 */
$router->group(['namespace' => 'Index', 'middleware' => 'auth'], function ($router) {
    $router->get('/test', 'HomeController@test');              //商家管理首页
    $router->get('/', 'HomeController@index');              //商家管理首页
    $router->get('about' , 'HomeController@about');         //关于我们

    $router->get('shop/{shop}/search', 'ShopController@search')->where('shop', '[0-9]+');          //商家商店搜索
    $router->get('shop/{shop}/detail', 'ShopController@detail')->where('shop', '[0-9]+');          //商家商店详情
    $router->get('shop/{shop}/{sort?}', 'ShopController@shop')->where('shop', '[0-9]+');          //商家商店首页
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
    $router->controller('pay', 'YeepayController'); //易宝

    $router->group(['prefix' => 'personal', 'namespace' => 'Personal'], function ($router) {
        $router->get('shop', 'ShopController@index');          //商家信息
        $router->get('info', 'InfoController@index');          //商家信息
        $router->get('password', 'PasswordController@index');          //修改密码
        $router->resource('bank', 'UserBankController', ['only' => ['edit', 'index', 'create']]);          //提现账号
        $router->resource('delivery-man', 'DeliveryManController', ['only' => ['edit', 'index', 'create']]); //配送人员
        $router->controller('finance', 'FinanceController'); //账户余额
        $router->get('customer/{user_type}', 'CustomerController@index'); // 客户列表
        $router->controller('chat', 'ChatController'); // 消息列表
        $router->resource('shipping-address', 'ShippingAddressController',
            ['only' => ['edit', 'index', 'create']]);          //提现账号
        $router->get('delivery','DeliveryController@historyDelivery');
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
    $router->delete('promoter/batch', 'PromoterController@deleteBatch');    //批量删除推广人员
    $router->resource('promoter', 'PromoterController');             //推广人员管理
    $router->resource('operation-record', 'OperationRecordController');    //运维操作记录
    $router->resource('version-record', 'VersionRecordController');    //版本更新记录
    $router->post('app-url', 'AppUrlController@postAppUrl');//app下载地址管理
    $router->controller('data-statistics', 'DataStatisticsController');    //运营数据统计
    $router->controller('statistics', 'StatisticsController');    //运营数据统计(时间段)
    $router->resource('shop-column', 'ShopColumnController');    //店铺栏目
    $router->controller('goods-column', 'GoodsColumnController');    //商品栏目
    $router->delete('barcode-without-images/batch', 'BarcodeWithoutImagesController@batch'); //批量删除前台用户添加商品时没有图片的条形码
    $router->get('barcode-without-images/export', 'BarcodeWithoutImagesController@export'); //导出没有图片的条形码
    $router->resource('barcode-without-images', 'BarcodeWithoutImagesController'); //前台用户添加商品时没有图片的条形码
    $router->resource('notice', 'NoticeController'); //前台用户添加商品时没有图片的条形码
    $router->get('chat', 'ChatController@index'); // 消息
    $router->controller('cache', 'CacheController'); // 消息
    $router->get('goods/import','GoodsController@import');//批量导入商品

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
        $router->get('shop/shops', 'ShopController@shops');                        //热门店铺
        $router->get('shop/{shop}', 'ShopController@detail')->where('shop', '[0-9]+');                        //店铺详细
        $router->get('shop/{shop}/goods', 'ShopController@goods')->where('shop', '[0-9]+');                  //店铺商品
        $router->get('shop/{shop}/extend', 'ShopController@extend')->where('shop', '[0-9]+');                   //店铺商品
        $router->get('shop/get-shops-by-ids', 'ShopController@getShopsByids');                   //店铺列表
        $router->get('shop/all', 'ShopController@allShops');
        $router->get('version', 'VersionInfoController@getIndex');
        $router->controller('file', 'FileController');                              // 文件上传
        $router->get('categories/{id}/attrs', 'CategoryController@getAttr');         //获取标签
        $router->get('attr/{id}/second', 'AttrController@secondAttr');         //获取二级分类
        $router->get('categories', 'CategoryController@getCategory');         //获取标签
        $router->post('categories/all', 'CategoryController@getAllCategory');         //获取所有标签
        $router->put('my-goods/shelve', 'MyGoodsController@shelve');                //商品上下架
        $router->put('my-goods/batch-shelve', 'MyGoodsController@batchShelve');     //商品批量上下架
        $router->get('my-goods/images', 'MyGoodsController@getImages');
        $router->post('my-goods/import', 'MyGoodsController@import');
        $router->resource('my-goods', 'MyGoodsController');


        $router->group(['prefix' => 'personal', 'namespace' => 'Personal'], function ($router) {
            $router->put('shop/{shop}', 'ShopController@shop');          //商家信息
            $router->put('password', 'PasswordController@password');          //修改密码
            $router->put('bank-default/{bank}', 'UserBankController@bankDefault');//设置默认提现账号
            $router->get('bank-info', 'UserBankController@banks');  //所有银行信息

            $router->resource('bank', 'UserBankController',
                ['only' => ['index', 'store', 'update', 'destroy']]);          //提现账号
            $router->resource('delivery-man', 'DeliveryManController',
                ['only' => ['index', 'store', 'update', 'destroy']]);          //配送人员

            $router->put('shipping-address/default/{address}', 'ShippingAddressController@addressDefault');
            $router->resource('shipping-address', 'ShippingAddressController');          //收货地址

            $router->controller('finance', 'FinanceController');    //提现相关操作

        });
        $router->controller('cart', 'CartController');
        $router->controller('order', 'OrderController');
        $router->controller('like', 'LikeController');
        $router->post('address/street', 'AddressController@street');
        $router->post('address/province-id', 'AddressController@getProvinceIdByName');
        $router->controller('auth', 'AuthController');
        $router->controller('push', 'PushController');//推送设备
        //获取支付charge
        $router->get('pay/charge/{order_id}', 'PayController@charge')->where('order_id', '[0-9]+');
        $router->any('pay/refund/{order_id}', 'PayController@refund')->where('order_id', '[0-9]+');
        $router->get('pay/success-url', 'PayController@successUrl');
        $router->controller('pos', 'PosController');             //pos机付款
        $router->post('js-errors', ['uses' => 'PublicController@jsErrorStore']); // 前端JS错误记录
        //获取移动端广告
        $router->get('advert', 'AdvertController@index');
        $router->post('feedback' , 'FeedbackController@index'); //意见反馈
        //司机版APP接口
        $router->group(['prefix' => 'delivery'], function ($router) {
            $router->get('index','DeliveryController@index');//配送人员登陆页面 测试用
            $router->post('login','DeliveryController@login');//处理配送人员登陆
            $router->get('orders','DeliveryController@orders');//已分配的订单信息
            $router->get('history-orders','DeliveryController@historyOrders');//已配送历史订单信息
            $router->get('detail','DeliveryController@detail');//配送订单详情
            $router->get('deal-delivery','DeliveryController@dealDelivery');//处理完成配送
            $router->get('logout','DeliveryController@logout');//退出登陆
        });

    });
});