<?php

namespace App\Http\Controllers\Admin;

class WeixinArticleBannerController extends WeixinArticleController
{
    protected $params = [
        'type' => 'banner',
        'size' => ['width' => '640', 'height' => '320'],
        'route' => 'article-banner'
    ];

}
