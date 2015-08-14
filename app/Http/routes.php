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
    $router->put('admin/switch', 'AdminController@putSwitch');//
    $router->resource('admin', 'AdminController');          //管理员管理
    $router->resource('role', 'RoleController');
    $router->delete('user/batch', 'UserController@deleteBatch');//批量删除用户
    $router->put('user/switch', 'UserController@putSwitch');//批量修改用户
    $router->resource('user', 'UserController');
    $router->resource('category','CategoryController');
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
    });
});