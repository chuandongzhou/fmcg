@extends('master')

@section('title')@yield('subtitle') | 订百达 - 订货首选@stop

@include('includes.chat')

@if(!request()->cookie('province_id'))
    @include('includes.first-load-model')
@endif

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
    <link href="{{ asset('css/shop-navigator.css') }}" rel="stylesheet">
    @stop

@section('header')
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
                        <i class="fa fa-map-marker"></i> 所在地：
                        <a href="#" class="location-text">
                            <span class="city-value" title="{{  $addressData['address_name'] }}">
                                {{  $addressData['address_name'] }}
                            </span>
                            <span class="fa fa-angle-down up-down"></span>
                        </a>
                    </div>
                    <div class="city-list clearfix">
                        <ul id="myTab" class="nav nav-tabs">
                            <li>
                                <a href="#deliveryProvince" data-class="deliveryProvince" data-toggle="tab"
                                   class="delivery-province" data-id="{{ $addressData['province_id'] }}">请选择</a>
                            </li>
                            <li class="active">
                                <a href="#deliveryCity" data-class="deliveryCity" data-toggle="tab"
                                   class="delivery-city" data-id="{{ $addressData['city_id'] }}">请选择</a>
                            </li>
                        </ul>
                        <div class="list-wrap tab-content" id="myTabContent">
                            <div class="tab-pane fade deliveryProvince">

                            </div>

                            <div class="tab-pane fade  in active deliveryCity">

                            </div>
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
                            @if((isset($user) && $user->type <= cons('user.type.wholesaler')) || is_null($user))
                                <li><a href="{{ url('/') }}" class="home"><span class="fa fa-home"></span> 订百达首页</a>
                                </li>
                            @endif
                            <li><a href="{{ url('personal/info') }}"><span class="fa fa-star-o"></span> 管理中心</a></li>
                            <li>
                                <a href="{{ isset($user) && $user->type > cons('user.type.retailer') ? url('order-sell') : url('order-buy') }}">
                                    <span class="fa fa-file-text-o"></span> 我的订单
                                </a>
                            </li>
                            <li><a href="{{ url('help') }}"><span class="fa fa-question-circle"></span> 帮助中心</a></li>
                            <li><a href="{{ url('personal/chat') }}">消息(<span
                                            class="red total-message-count">0</span>)</a></li>
                            @if((isset($user) && $user->type < cons('user.type.supplier')) || is_null($user))
                                <li class="collect-select">
                                    <a class="collect-selected"><span class="selected">收藏夹</span> <span
                                                class="fa fa-angle-down"></span></a>
                                    <ul class="select-list">
                                        <li><a href="{{ url('like/shops') }}">店铺收藏</a></li>
                                        <li><a href="{{ url('like/goods') }}">商品收藏</a></li>
                                    </ul>
                                </li>
                            @endif
                            @if(isset($user))
                                <li class="user-name-wrap">
                                    <a href="{{ url('personal/info') }}" class="name-panel"><span
                                                class="user-name">{{ $user->shop->name }}</span>( {{ cons()->valueLang('user.type' , $user->type) }}
                                        )</a>
                                    <a href="{{ url('auth/logout') }}" class="exit"><i class="fa fa-sign-out"></i>
                                        退出</a>
                                </li>
                            @else
                                <li class="user-name-wrap">
                                    <a href="{{ url('auth/login') }}" class="red">登录</a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('body')
    @yield('container')

    @if(isset($user))
        <audio id="myaudio" src="{{ asset('images/notice.wav') }}" style="opacity:0;">
        </audio>
        <div class="msg-channel" id="alert-div">
            <div class="title"><span class="pull-left">你有新消息</span><a class="close-btn fa fa-remove pull-right"></a></div>
            <a class="check" href="#">点击查看>>>></a>
        </div>
        <!--右侧贴边导航quick_links.js控制-->
        <div class="quick-wrap">
            <div class="quick_links_panel">
                <div id="quick_links" class="quick_links">
                    <li>
                        <a href="javascript:;" class="my_qlinks"><i class="setting"></i></a>
                        @if(isset($user))
                        <div class="ibar_login_box status_login">
                            <div class="avatar_box">
                                <p class="avatar_imgbox"><img src="{{ $user->shop->logo_url }}"/></p>
                                <ul class="user_info">
                                    <li>店铺名：{{ $user->shop->name }}</li>
                                    <li>类&nbsp;型：{{ cons()->valueLang('user.type' , $user->type) }}</li>
                                </ul>
                            </div>
                            <div class="login_btnbox">
                                <a href="{{ isset($user) && $user->type > cons('user.type.retailer') ? url('order-sell') : url('order-buy') }}" class="login_order">我的订单</a>
                                <a href="{{ url('like/goods') }}" class="login_favorite">我的收藏</a>
                            </div>
                            <i class="icon_arrow_white"></i>
                        </div>
                        @endif
                    </li>
                    <li id="shopCart">
                        <a href="javascript:;" class="message_list pop-show-link"><i class="message"></i>

                            <div class="span">购物车</div>
                            @if($carts = (new \App\Services\CartService)->cartDetail())
                                <span class="cart_num">{{ $carts['count'] }}</span></a>
                        @endif
                    </li>
                    <li id="coupon">
                        <a href="javascript:;" class="history_list pop-show-link"><i class="view"></i></a>

                        <div class="mp_tooltip" style=" visibility:hidden;">我的资产<i class="icon_arrow_right_black"></i>
                        </div>
                    </li>
                    <li>
                        <a href="{{ url('like/goods') }}" class="mpbtn_wdsc"><i class="wdsc"></i></a>

                        <div class="mp_tooltip">我的收藏<i class="icon_arrow_right_black"></i></div>
                    </li>
                </div>
                <div class="quick_toggle">
                    <li><a href="javascript:;" class="return_top"><i class="top"></i></a></li>
                </div>
            </div>
            <div id="quick_links_pop" class="quick_links_pop ">
                <!--购物车开始-->
                {{--<a href="javascript:;" class="ibar_closebtn" title="关闭"></a>--}}
                {{--<div class="ibar_plugin_title"><h3>购物车</h3></div>--}}
                {{--<div class="loading-img" style="display:none;"><img src="../images/loading.gif" /></div>--}}
                {{--<div class="pop_panel cart-panel" style="display:none;">--}}
                {{--<div class="ibar_plugin_content">--}}
                {{--<div class="ibar_cart_group ibar_cart_product" >--}}
                {{--<ul>--}}
                {{----}}
                {{--</ul>--}}
                {{--</div>--}}
                {{--<div class="cart_handler">--}}
                {{--<div class="cart_handler_header"><span class="cart_handler_left">共<span--}}
                {{--class="cart_price cart_num">1</span>件商品</span><span class="cart_handler_right">￥569.00</span>--}}
                {{--</div>--}}
                {{--<a href="{{ url('cart') }}" class="cart_go_btn" target="_blank">去购物车结算</a></div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="arrow"><i></i></div>--}}
                {{--<div class="fix_bg"></div>--}}
                        <!--购物车结束-->
                <!--优惠券领取开始-->
                <a href="javascript:;" class="ibar_closebtn" title="关闭"></a>
                <div class="ibar_plugin_title">
                    <h3>
                        我的资产
                    </h3>
                </div>
                <div class="loading-img" style="display:none;"><img src="../images/loading.gif" /></div>
                <div class="pop_panel coupon-panel" style="display:none;">
                    <div class="ibar_plugin_content">
                        <div class="ia-head-list">
                            <a href="http://192.168.2.66/coupons" target="_blank" class="pl">
                                <div class="my-coupon-num">
                                    2
                                </div>
                                <div class="text">
                                    优惠券
                                </div>
                            </a>
                        </div>
                        <div class="ga-expiredsoon">
                            <div class="es-head">
                                即将过期优惠券
                            </div>
                            <div class="coupon-wrap my-coupon-wrap">
                                <div class="coupon-loading-img" style="display:none;"><img src="../images/loading.gif" /></div>
                                {{--<div class="coupon bgc-blue">--}}
                                    {{--<div class="validity">--}}
                                        {{--<p>--}}
                                            {{--<!--有效期-->--}}
                                        {{--</p>--}}
                                        {{--<p>--}}
                                            {{--2016-07-01--}}
                                        {{--</p>--}}
                                        {{--<p>--}}
                                            {{--2016-08-31--}}
                                        {{--</p>--}}
                                    {{--</div>--}}
                                    {{--<ul>--}}
                                        {{--<li>--}}
                                            {{--<a href=" http://192.168.2.66/shop/3" target="_blank">--}}
                                                {{--无限极旗舰店--}}
                                            {{--</a>--}}
                                        {{--</li>--}}
                                        {{--<li>--}}
                                            {{--￥5.00--}}
                                        {{--</li>--}}
                                        {{--<li>--}}
                                            {{--满50.00使用--}}
                                        {{--</li>--}}
                                    {{--</ul>--}}
                                {{--</div>--}}
                                {{--<div class="coupon bgc-red">--}}
                                    {{--<div class="expiration">--}}
                                        {{--<span>--}}
                                        {{--10天后过期--}}
                                        {{--</span>--}}
                                    {{--</div>--}}
                                    {{--<ul>--}}
                                        {{--<li>--}}
                                            {{--<a href=" http://192.168.2.66/shop/3" target="_blank">--}}
                                                {{--无限极旗舰店--}}
                                            {{--</a>--}}
                                        {{--</li>--}}
                                        {{--<li>--}}
                                            {{--￥20.00--}}
                                        {{--</li>--}}
                                        {{--<li>--}}
                                            {{--满200.00使用--}}
                                        {{--</li>--}}
                                    {{--</ul>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                        <div class="ga-expiredsoon recevie-coupon-head" style="display:none;">
                            <div class="es-head">
                                可领取优惠券
                            </div>
                            <div class="coupon-wrap my-recevie-coupon-wrap">
                                <div class="coupon-loading-img" style="display:none;"><img src="../images/loading.gif" /></div>
                                {{--<div class="coupon bgc-orange">--}}
                                    {{--<div class="receive-wrap"><a class="not-receive">立即领取</a><a class="already-receive"><span--}}
                                                    {{--class="fa fa-check"></span>已领</a></div>--}}
                                    {{--<div class="validity"><p>有效期</p>--}}

                                        {{--<p>2016.07.18 00.00</p>--}}

                                        {{--<p>2016.07.28 23.59</p></div>--}}
                                    {{--<ul>--}}
                                        {{--<li>xxx店铺</li>--}}
                                        {{--<li>￥10</li>--}}
                                        {{--<li>满5使用</li>--}}
                                    {{--</ul>--}}
                                {{--</div>--}}

                            </div>
                        </div>
                    </div>
                </div>
                <div class="pop_panel cart-panel" style="display:none;">
                    <div class="ibar_plugin_content"></div>
                </div>
                <div class="arrow"><i></i></div>
                <div class="fix_bg"></div>
                <!--优惠券领取结束-->
            </div>
        </div>
    @endif
@stop

@section('footer')
    <div class="footer">
        @yield('join-us')
        <footer class="panel-footer footer">
            <div class="container text-center text-muted">
                <div class="row text-center">
                    <div class="col-xs-6">
                        <ul class="list-inline">
                            <li><a href="{{ url('about') }}" class="icon about">关于我们</a></li>
                            <li>
                                <div class="contact-panel">
                                    <a href="javascript:;" class="icon contact-information">联系方式</a>
                                </div>
                                <div class="contact-content content hidden">
                                    <div>{{ cons('system.company_tel') . '&nbsp;&nbsp;&nbsp;&nbsp;' . cons('system.company_mobile') }}</div>
                                    <div>{{ cons('system.company_addr') }}</div>
                                </div>
                            </li>
                            <li>
                                <div class="feedback-panel">
                                    <a class="feedback icon" href="javascript:;">意见反馈</a>
                                </div>
                                <div class="content hidden">
                                    <form class="ajax-form" method="post" action="{{ url('api/v1/feedback') }}"
                                          accept-charset="UTF-8" data-help-class="error-msg text-center"
                                    >
                                        <div>
                                            <textarea placeholder="请填写您的反馈意见" name="content"></textarea>
                                        </div>
                                        <div>
                                            <div class="input-group">
                                            <span class="input-group-addon" id="feedback-contact"><i
                                                        class="fa fa-envelope-o"></i></span>
                                                <input type="text" class="form-control" placeholder="留个邮箱或者别的联系方式呗"
                                                       aria-describedby="feedback-contact" name="contact">
                                                <span class="input-group-btn">
                                                <button class="btn btn-primary btn-submit" type="submit"
                                                        data-done-then="none" data-done-text="反馈提交成功">提交
                                                </button>
                                            </span>
                                            </div>
                                            <!-- /input-group -->
                                        </div>
                                    </form>
                                </div>
                            </li>
                            <li>
                                <div id="qr-content-panel">
                                    <a href="javascript:;" class="app-down icon">APP下载</a>
                                </div>
                                <div class="content hidden">
                                    <div class="qr-panel">
                                        <div class="dbd item">
                                            <div class="qr-code dbd-qr-code"></div>
                                            <div class="text text-center">订百达</div>
                                        </div>
                                        <div class="driver-helper item">
                                            <div class="qr-code helper"></div>
                                            <div class="text text-center">司机助手</div>
                                        </div>
                                        <div class="driver-helper item">
                                            <div class="qr-code field"></div>
                                            <div class="text text-center">外勤</div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-xs-6">
                        <p>
                            Copyright &copy; {!! cons('system.company_name') . '&nbsp;&nbsp;&nbsp;&nbsp;' . cons('system.company_record') !!} </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
    <script type="text/javascript" src="{{ asset('js/index.js?v=1.0.0') }}"></script>
    <script type="text/javascript" src="{{ asset('js/address-for-delivery.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/ajax-polling.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/shop-navigator.js') }}"></script>
@stop

@section('js')
    <script type="text/javascript">
        $(function () {
            //意见反馈
            $('.feedback-panel > a').popover({
                container: '.feedback-panel',
                placement: 'top',
                html: true,
                content: function () {
                    return $(this).parent().siblings('.content').html();
                }
            })

            //扫二维码下载app
            tooltipFunc('#qr-content-panel > a', '#qr-content-panel');
            //联系方式
            tooltipFunc('.contact-panel > a', '.contact-panel');

            //调用tooltip插件
            function tooltipFunc(item, container) {
                $(item).tooltip({
                    container: container,
                    placement: 'top',
                    html: true,
                    title: function () {
                        return $(this).parent().siblings('.content').html();
                    }
                })
            }

            var flip = 0;
            var couponFlip = 0;
            //点击购物车图标显示弹框
            $("#shopCart").click(function (e) {
                e.stopPropagation();
                couponFlip = 0;
                if (flip++ % 2 === 0) {

                    $(".quick_links_pop").animate({left: -320, queue: true});
                    $(".quick_links_pop").css("zIndex", "-1");
                    $('.coupon-panel').css('display','none');
                    $('.ibar_plugin_title h3').html('购物车');
                    $('.loading-img').css('display', 'block');
                    $('.cart-panel').css('display', 'none');

                    $.ajax({
                        url: '/api/v1/cart',
                        method: 'get'
                    }).done(function (data) {
                        var html = '', cartNum = 0, cartPrices = 0;
                        html += '<div class="ibar_cart_group ibar_cart_product" >'+
                            '<ul>';
                        for (var shops in data) {
                            var detailShops = data[shops];
                            for (var shop in detailShops) {

                                html += '<li class="cart_item">' +
                                        '<div class="store-name">' + detailShops[shop].name + '</div>';
                                cartPrices += detailShops[shop].sum_price;
                                for (var goods in detailShops[shop].cart_goods) {
                                    cartNum++;
                                    var detailGoods = detailShops[shop].cart_goods[goods];

                                    html += '<div class="store-panel">' +
                                            '<div class="cart_item_pic"><a href="/goods/' + detailGoods.goods_id + '"><img src="' + detailGoods.image + '"></a></div>' +
                                            '<div class="cart_item_desc"><a href="#" class="cart_item_name">' + detailGoods.goods.name + '</a>' +
                                            '<div class="cart_item_price"><span class="cart_price">￥' + detailGoods.goods.price + '</span></div>' +
                                            '</div>' +
                                            '</div>';
                                }
                                html += '</li>';
                            }
                        }
                        html += '</ul>'+
                                '</div>'+
                                '<div class="cart_handler">'+
                                    '<div class="cart_handler_header"><span class="cart_handler_left">共<span'+
                                    'class="cart_price cart_num">'+cartNum+'</span>件商品</span><span class="cart_handler_right">￥'+cartPrices+'</span>'+
                                    '</div>'+
                                   '<a href="{{ url('cart') }}" class="cart_go_btn" target="_blank">去购物车结算</a></div>';

                        $('.cart-panel .ibar_plugin_content').html(html);
                        $('.cart_num').html(cartNum);
                        $('.cart_handler_right').html('￥' + cartPrices);
                        $('.loading-img').css('display', 'none');
                        $('.cart-panel').css('display', 'block');

                    });
                } else {
                    $(".quick_links_pop").animate({left: -40, queue: true});
                    $(".quick_links_pop").css("zIndex", "-1")
                }
            })
            //点击我的资产图标显示弹框
            $("#coupon").click(function (e) {
                e.stopPropagation();
                flip = 0;
                if (couponFlip++ % 2 === 0) {

                    $(".quick_links_pop").animate({left: -320, queue: true});
                    $(".quick_links_pop").css("zIndex", "-1");
                    $('.cart-panel').css('display', 'none');
                    var html = '';
                    $('.ibar_plugin_title h3').html('我的资产');
                    $('.coupon-panel').css('display', 'block');
                    $('.coupon-loading-img').css('display','block');
                    $.ajax({
                        url: '/api/v1/coupon/user-coupon',
                        method: 'get'
                    }).done(function (data) {
                        data = data.coupons;
                        $('.my-coupon-num').html(data.length);
                        for(var i=0;i<data.length;i++){
                            var dateTo = new Date(data[i].end_at);
                            var dateFrom = new Date();
                            var diff = dateTo.valueOf() - dateFrom.valueOf();
                            var diff_day = parseInt(diff/(1000*60*60*24));
                            if(diff_day>10){
                                html +=  '<div class="coupon bgc-blue">'+
                                            '<div class="validity">'+
                                                '<p>'+
                                                ' 有效期'+
                                                ' </p>'+
                                                ' <p>'+
                                                data[i].start_at+
                                                '</p>'+
                                                '<p>'+
                                                data[i].end_at+
                                                '</p>'+
                                            '</div>'+
                                            ' <ul>'+
                                                ' <li>'+
                                                    ' <a href=" shop/'+data[i].shop.id+'" target="_blank">'+
                                                   data[i].shop.name+
                                                    ' </a>'+
                                                ' </li>'+
                                                ' <li>'+
                                                ' ￥'+data[i].discount+
                                                '</li>'+
                                                '<li>'+
                                                '满'+data[i].full+'使用'+
                                                '</li>'+
                                            ' </ul>'+
                                        '</div>';
                            }else{
                                html += '<div class="coupon bgc-red">'+
                                            ' <div class="expiration">'+
                                                '<span>'+
                                                    diff_day+'天后过期'+
                                                ' </span>'+
                                            ' </div>'+
                                            '  <ul>'+
                                                '<li>'+
                                                ' <a href=" shop/'+data[i].shop.id+'" target="_blank">'+
                                                data[i].shop.name+
                                                '</a>'+
                                                '</li>'+
                                                ' <li>'+
                                                ' ￥'+data[i].discount+
                                                ' </li>'+
                                                '<li>'+
                                                ' 满'+data[i].full+'使用'+
                                                ' </li>'+
                                            ' </ul>'+
                                        '</div>';
                            }
                        }
                        $('.my-coupon-wrap').html(html);
                        $('.coupon-loading-img').css('display','none');


                    });
                   @if(request()->is('shop/*'))
                    var pattern=/.*shop\/(\d).*/g;
                    var shop = (pattern.exec(window.location.href))[1];
                    var url = '/api/v1/coupon/'+shop;
                    $.ajax({
                        url: url,
                        method: 'get'
                    }).done(function (data) {

                        var h ='';
                        data = data.coupons;

                        for(var i=0;i<data.length;i++){
                            h +=  '<div class="coupon bgc-orange">'+
                                    '<div class="receive-wrap" data-id="'+data[i].id+'"><a class="not-receive">立即领取</a><a class="already-receive"><span'+
                                    ' class="fa fa-check"></span>已领</a></div>'+
                                    '<div class="validity"><p>有效期</p>'+

                                    '<p>'+data[i].start_at+'</p>'+

                                    '<p>'+data[i].end_at+'</p></div>'+
                                    '<ul>'+
                                    '<li>'+data[i].shop.name+'</li>'+
                                    '<li>￥'+data[i].discount+'</li>'+
                                    '<li>满'+data[i].full+'使用</li>'+
                                    '</ul>'+
                                    '</div>';

                        }
                        $('.recevie-coupon-head').css('display','block');
                        $('.my-recevie-coupon-wrap').html(h);

                    });
                    @endif

                } else {
                    $(".quick_links_pop").animate({left: -40, queue: true});
                    $(".quick_links_pop").css("zIndex", "-1")
                }
            })
            //点击按钮关闭弹框
            $(".ibar_closebtn").click(function (e) {
                flip = 0;
                couponFlip = 0;
                $(".quick_links_pop").animate({left: -40, queue: true});
                $(".quick_links_pop").css("zIndex", "-1")
            })
            //用户头像显示信息
            $(".my_qlinks").mouseenter(function () {
                $(this).siblings(".ibar_login_box").show();
            })
            //点击其他地方隐藏div
            $(".my_qlinks").parent("li").mouseleave(function () {
                $(".ibar_login_box").hide();
            })
            $(document).click(function (e) {
                e = e || window.event;
                flip = 0;
                couponFlip = 0;
                if (e.target != $('.quick-wrap')[0] && e.target != $(".quick_links_pop")[0]) {
                    $(".quick_links_pop").animate({left: -40, queue: true});
                    $(".quick_links_pop").css("zIndex", "-1")
                }
            });
            $(".quick-wrap").click(function (event) {
                event.stopPropagation();
            });
            //滚动条离开顶部一定的高度 显示回到顶部按钮
            $(window).scroll(function () {
                if ($(window).scrollTop() > 100) {
                    $(".quick_toggle").addClass('quick_links_allow_gotop');
                } else {
                    $(".quick_toggle").removeClass('quick_links_allow_gotop');
                }
            })
            //点击回到顶部按钮
            $(".return_top").click(function () {
                $('html, body').animate({scrollTop: 0}, 'slow');
            });
            //领取优惠券
            $("#quick_links_pop").on("click", ".receive-wrap", function () {
                var coupon_id = $(this).data('id');
                var obj = $(this);

                $.ajax({
                    url: '/api/v1/coupon/receive/'+coupon_id,
                    data: {coupon: coupon_id},
                    method: 'post'
                }).done(function () {
                    obj.children(".not-receive").css("display", "none").siblings().css("display", "inline-block");
                    setTimeout(function () {
                        obj.css("display", "none")
                    }, 500)
                });

            });
        });
    </script>
@stop