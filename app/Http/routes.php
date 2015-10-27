<?php

$router->get('pusher', 'TestController@pusher');
$router->get('pushIos', 'TestController@pushIos');
$router->group(['prefix' => 'auth', 'namespace' => 'Auth'], function ($router) {
    $router->get('login', 'AuthController@login');
    $router->get('register', 'AuthController@register');
    $router->get('logout', 'AuthController@logout');
});

/**
 * 前台
 */
$router->group(['namespace' => 'Index', 'middleware' => 'auth'], function ($router) {
    $router->get('/', 'HomeController@index');              //商家管理首页

    $router->get('shop/{shop}/search', 'ShopController@search')->where('shop', '[0-9]+');          //商家商店搜索
    $router->get('shop/{shop}/detail', 'ShopController@detail')->where('shop', '[0-9]+');          //商家商店详情
    $router->get('shop/{shop}/{sort?}', 'ShopController@shop')->where('shop', '[0-9]+');          //商家商店首页
    $router->get('shop/{sort?}', 'ShopController@index')->where('shop', '\d+');                   //商家

    $router->controller('order', 'OrderController');//订单统计
    $router->controller('order-buy', 'OrderBuyController');  //买家订单管理
    $router->controller('order-sell', 'OrderSellController');//卖家订单管理
    $router->resource('my-goods', 'MyGoodsController');          //商品管理
    $router->get('goods/{goods}', 'GoodsController@detail')->where('goods', '[0-9]+');          //商品详情
    $router->get('cart', 'CartController@index');          // 购物车
    $router->controller('order', 'OrderController');       //订单
    $router->get('search', 'SearchController@index');      //商品搜索页
    $router->controller('like', 'LikeController');//收藏夹
    $router->controller('pay', 'YeepayController'); //易宝

    $router->group(['prefix' => 'personal', 'namespace' => 'Personal'], function ($router) {
        $router->get('shop', 'ShopController@index');          //商家信息
        $router->get('password', 'PasswordController@index');          //修改密码
        $router->resource('bank', 'UserBankController', ['only' => ['edit', 'index', 'create']]);          //提现账号
        $router->resource('delivery-man', 'DeliveryManController', ['only' => ['edit', 'index', 'create']]); //配送人员
        $router->get('balance', 'BalanceController@index'); //账户余额
        $router->controller('withdraw', 'WithdrawController');//提现相关操作
        $router->resource('shipping-address', 'ShippingAddressController',
            ['only' => ['edit', 'index', 'create']]);          //提现账号
    });


});


/**
 * 后台
 */
$router->group(['prefix' => 'admin', 'namespace' => 'Admin'], function ($router) {

    $router->get('auth/login', ['uses' => 'AuthController@login']);  // 后台首页
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
    $router->resource('role', 'RoleController');
    $router->delete('user/batch', 'UserController@deleteBatch');//批量删除用户
    $router->put('user/switch', 'UserController@putSwitch');//批量修改用户
    $router->resource('user', 'UserController');            //用户管理
    $router->post('getCategory', 'CategoryController@getCategory');  //获取属性
    $router->resource('category', 'CategoryController');            //属性管理
    $router->post('getAttr', 'AttrController@getAttr');             //获取标签
    $router->post('attr/save', 'AttrController@save');             //绑定标签到分类
    $router->resource('attr', 'AttrController');                    //标签管理
    $router->get('attr/create/{id}', 'AttrController@create')->where('id', '[0-9]+'); //添加子标签
    $router->resource('images', 'ImagesController');                    //商品图片管理
    $router->resource('shop', 'ShopController', ['only' => ['edit', 'update']]); //店铺管理
    $router->controller('system-trade', 'SystemTradeInfoController');        //系统交易信息
    $router->controller('feedback', 'FeedbackController');             //反馈管理
    $router->controller('trade', 'TradeController');        //交易信息
    $router->delete('promoter/batch', 'PromoterController@deleteBatch');    //批量删除推广人员
    $router->resource('promoter', 'PromoterController');             //推广人员管理
    $router->resource('operation-record', 'OperationRecordController');    //运维操作记录
    $router->controller('data-statistics', 'DataStatisticsController');    //运营数据统计
    $router->resource('column', 'HomeColumnController');    //首页栏目
});


/**
 * 接口
 */
$router->group(['prefix' => 'api', 'namespace' => 'Api'], function ($router) {
    $router->get('/', function () {
        return view('api.index');
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
        $router->get('shop/{shop}', 'ShopController@detail')->where('shop', '[0-9]+');;                        //店铺详细
        $router->get('shop/{shop}/goods', 'ShopController@goods')->where('shop', '[0-9]+');;                   //店铺商品
        $router->get('shop/{shop}/extend', 'ShopController@extend')->where('shop', '[0-9]+');;                   //店铺商品


        $router->controller('file', 'FileController');                              // 文件上传
        $router->get('categories/{id}/attrs', 'CategoryController@getAttr');         //获取标签
        $router->get('attr/{id}/second', 'AttrController@secondAttr');         //获取二级分类
        $router->get('categories', 'CategoryController@getCategory');         //获取标签
        $router->post('categories/all', 'CategoryController@getAllCategory');         //获取所有标签

        $router->put('my-goods/shelve/{goods_id}', 'MyGoodsController@shelve');
        $router->get('my-goods/images', 'MyGoodsController@getImages');
        $router->resource('my-goods', 'MyGoodsController');


        $router->group(['prefix' => 'personal', 'namespace' => 'Personal'], function ($router) {
            $router->put('shop/{shop}', 'ShopController@shop');          //商家信息
            $router->post('password', 'PasswordController@password');          //修改密码
            $router->post('bank-default/{bank}', 'UserBankController@bankDefault');
            $router->resource('bank', 'UserBankController', ['only' => ['store', 'update', 'destroy']]);          //提现账号
            $router->put('shipping-address-default/{address}', 'ShippingAddressController@addressDefault');
            $router->resource('shipping-address', 'ShippingAddressController');          //收货地址

            $router->resource('delivery-man', 'DeliveryManController',
                ['only' => ['index', 'store', 'update', 'destroy']]);          //提现账号
        });
        $router->controller('cart', 'CartController');
        $router->controller('order', 'OrderController');
        $router->controller('like', 'LikeController');
        $router->post('address/street', 'AddressController@street');
        $router->controller('auth', 'AuthController');
    });
});