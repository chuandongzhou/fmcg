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
    $router->resource('admin', 'AdminController');          //管理员管理
    $router->resource('role', 'RoleController');
    $router->resource('user', 'UserController');
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