<?php

namespace WeiHeng\ResponseBak;

class Response
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Illuminate\Routing\ResponseFactory
     */
    protected $response;

    /**
     * 初始化函数
     */
    public function __construct()
    {
        $this->request = app('request');
        $this->response = response();
    }
}