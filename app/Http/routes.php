<?php

/**
 * 前台
 */
$router->group(['namespace' => 'Index'], function ($router) {
    $router->get('/', ['as' => 'index', 'uses' => 'HomeController@index']);                     // 首页
});


/**
 * 后台
 */
$router->group(['prefix' => 'admin', 'namespace' => 'Admin'], function ($router) {
    // 首页
    $router->get('/', ['uses' => 'HomeController@getIndex']);  // 后台首页
    $router->delete('admin/batch', 'AdminController@deleteBatch');//批量删除
    $router->get('admin/password', 'AdminController@getPassword');//获取修改密码表单
    $router->put('admin/password', 'AdminController@putPassword');//修改当前管理员密码
    $router->put('admin/switch', 'AdminController@putSwitch');//管理员状态切换
    $router->resource('admin', 'AdminController');          //管理员管理
    $router->resource('advert', 'advertController');         //广告相关管理
    $router->resource('role', 'RoleController');
    $router->delete('user/batch', 'UserController@deleteBatch');//批量删除用户
    $router->put('user/switch', 'UserController@putSwitch');//批量修改用户
    $router->resource('user', 'UserController');            //用户管理
    $router->post('getCategory', 'CategoryController@getCategory');  //获取属性
    $router->resource('category', 'CategoryController');            //属性管理
    $router->post('getAttr', 'AttrController@getAttr');             //获取标签
    $router->resource('attr', 'AttrController');                    //标签管理
    $router->get('attr/create/{id}', 'AttrController@create')->where('id', '[0-9]+'); //添加子标签
    $router->resource('images', 'GoodsImagesController');                    //商品图片管理
    $router->resource('shop', 'ShopController', ['only' => ['edit', 'update']]); //店铺管理
    $router->controller('trade', 'SystemTradeInfoController');
});


/**
 * 接口
 */
$router->group(['prefix' => 'api', 'namespace' => 'Api'], function ($router) {
    /**
     * v2 版本
     */
    $router->group(['prefix' => 'v1', 'namespace' => 'v1'], function ($router) {
        // 接口地址
        $router->get('/', [
            'as' => 'api.v1.root',
            function () {
                return redirect('/');
            }
        ]);

        $router->controller('file', 'FileController');                              // 文件上传
        $router->get('categories/{id}/attrs', 'CategoryController@getAttr');         //获取标签
        $router->get('categories', 'CategoryController@getCategory');         //获取标签

    });
});