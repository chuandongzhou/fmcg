    <!--[if lt IE 9]>
<div class="ie-warning alert alert-warning alert-dismissable fade in">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    您的浏览器不是最新的，您正在使用 Internet Explorer 的一个<strong>老版本</strong>。 为了获得最佳的浏览体验，我们建议您选用其它浏览器。
    <a class="btn btn-primary" href="http://browsehappy.com/" target="_blank" rel="nofollow">立即升级</a>
</div>
<![endif]-->

<div class="dealer-top-header">
    <div class="container ">
        <div class="row">
            <div class="col-sm-4 city-wrap">
                <div class="location-panel">
                    <i class="fa fa-map-marker"></i> 所在地：<a href="#" class="location-text"><span
                                class="city-value">{{  $provinces[\Request::cookie('province_id')] or head($provinces) }}</span> <span
                                class="fa fa-angle-down up-down"></span></a>
                </div>
                <div class="city-list clearfix">
                    <div class="list-wrap">
                        @foreach($provinces as $provinceId => $province)
                            <div class="item">
                                <a title="{{ $province }}"
                                   class="{{ \Request::cookie('province_id') == $provinceId ? 'selected' : '' }}"
                                   href="javascript:void(0)" data-id="{{ $provinceId }}">{{ $province }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed navbar-button" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-9" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="navbar-collapse collapse top-nav-list" id="bs-example-navbar-collapse-9"
                     aria-expanded="false" style="height: 1px;">
                    <ul class="nav navbar-nav navbar-right operating-wrap">
                        @if(auth()->user()->type == cons('user.type.wholesaler'))
                            <li><a href="{{ url('shop/' .auth()->user()->shop->id) }}"><span
                                            class="fa fa-heart-o"></span>我的店面</a>
                            </li>
                        @endif
                        <li><a href="{{ url('personal/shop') }}"><span class="fa fa-heart-o"></span>个人中心</a></li>
                        @if(auth()->user()->type == cons('user.type.retailer'))
                            <li><a href="{{ url('order-buy') }}"><span class="fa fa-file-text-o"></span> 我的订单</a></li>
                        @else
                            <li><a href="{{ url('order-sell') }}"><span class="fa fa-file-text-o"></span> 我的订单</a></li>
                        @endif
                        <li class="collect-select">
                            <a class="collect-selected"><span class="selected">收藏夹</span> <span
                                        class="fa fa-angle-down"></span></a>
                            <ul class="select-list">
                                <li><a href="{{ url('like/shops') }}">店铺收藏</a></li>
                                <li><a href="{{ url('like/goods') }}">商品收藏</a></li>
                            </ul>
                        </li>
                        <li class="user-name-wrap">
                            <a href="{{ url('personal/shop') }}" class="name-panel"><span
                                        class="user-name">{{ auth()->user()->shop->name }}</span>({{ cons()->valueLang('user.type' , auth()->user()->type) }}
                                )</a>
                            <a href="{{ url('auth/logout') }}" class="exit"><span class="fa fa-ban"></span> 退出</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<nav class="navbar dealer-header">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed navbar-button" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand logo-icon" href="{{ url('/') }}"><img src="{{ asset('images/logo.png') }}"/> </a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <form action="{{ url('search') }}" class="navbar-form navbar-left search text-center" role="search"
                  type="get">
                <div class="input-group">
                    <div class="select-role pull-left">
                        <a href="javascript:void(0)" class="selected"><span>商品</span><i
                                    class="fa fa-angle-down"></i></a>
                        <ul class="select-list">
                            <li><a href="javascript:void(0)" data-url="search">商品</a></li>
                            @if($user->type == cons('user.type.retailer'))
                                <li><a href="javascript:void(0)" data-url="shop?type=wholesaler">批发商</a></li>
                            @endif
                            <li><a href="javascript:void(0)" data-url="shop?type=supplier">供应商</a></li>
                        </ul>
                    </div>
                    <input type="text" name="name" class="control pull-right" aria-describedby="course-search">
                    <span class="input-group-btn btn-primary">
                        <button class="btn btn-primary search-btn" type="submit">搜索</button>
                    </span>
                </div>
            </form>
            <ul class="nav navbar-nav navbar-right right-btn">
                <li><a href="{{ url('cart') }}" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> 购物车</a></li>
            </ul>
        </div>
    </div>
</nav>