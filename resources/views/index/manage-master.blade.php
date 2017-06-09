@extends('master')
@include('includes.chat', ['shopId' => auth()->user() ? auth()->user()->shop_id : 0])
@section('title')@yield('subtitle') | 订百达@stop

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
    <link href="{{ asset('css/css.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css"/>
    <link href="{{ asset('css/layout.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/blue.min.css') }}" rel="stylesheet" type="text/css" id="style_color"/>
    @stop
@section('header')
    <!--[if lt IE 9]>
    <div class="ie-warning alert alert-warning alert-dismissable fade in">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        您的浏览器不是最新的，您正在使用 Internet Explorer 的一个<strong>老版本</strong>。 为了获得最佳的浏览体验，我们建议您选用其它浏览器。
        <a class="btn btn-primary" href="http://browsehappy.com/" target="_blank" rel="nofollow">立即升级</a>
    </div>
    <![endif]-->
    <!-- 对于IE 10 以下版本placeholder的兼容性调整 -->
    <!--[if lt IE 10]>
    <script type="text/javascript">
        $(document).ready(function () {
            $('[placeholder]').removeAttr("placeholder");
        })
    </script>
    <![endif]-->
    <div class="page-header navbar navbar-fixed-top">
        <div class="page-header-inner ">
            @if(auth()->check())
                <div class="page-logo">
                    <a href="{{ url('/') }}">
                        <span class="link-index logo-default">订百达首页</span>
                    </a>
                    <div class="menu-toggler sidebar-toggler">
                        <!-- 折叠菜单按钮 -->
                    </div>
                </div>
        @endif
        <!-- BEGIN 响应式菜单切换 -->
            <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse"
               data-target=".navbar-collapse"> </a>

            <div class="page-top">
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
                        <!--帮助中心-->

                        <li class="dropdown dropdown-extended dropdown-notification">
                            <a href="{{ url('help') }}" class="dropdown-toggle" data-hover="dropdown"
                               data-close-others="true">
                                <i class="fa fa-question-circle" title="帮助中心"></i> &nbsp;
                            </a>
                        </li>
                        <!--购物车-->
                        @if(auth()->check() && $user->type < cons('user.type.supplier'))
                            <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                                <a href="{{ url('cart') }}" class="dropdown-toggle" data-toggle="dropdown"
                                   data-hover="dropdown" data-close-others="true">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="badge badge-default cart-badge">{{ $cartNum }}</span>

                                </a>
                                <ul class="dropdown-menu">
                                    <li class="external">
                                        <h3> 最新加入商品</h3>
                                        <a href="{{ url('cart') }}">去购物车查看</a>
                                    </li>
                                    <li>
                                        <ul class="dropdown-menu-list scroller cart-detail" style="height: 250px;"
                                            data-handle-color="#637283">
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                    @endif
                    <!--最新消息-->
                        <li class="dropdown dropdown-extended dropdown-inbox quick-sidebar-toggler drop-newmsg">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"
                               data-hover="dropdown"
                               data-close-others="true">
                                <i class="fa fa-commenting-o "></i>
                                <span class="badge badge-default total-message-count hide">0 </span>
                            </a>
                            <br>
                        </li>
                        <!--登录名-->
                        <li class="dropdown dropdown-user">
                            <a href="{{ url('personal/info') }}" class="dropdown-toggle" data-hover="dropdown"
                               data-close-others="true">
                                <img alt="" class="img-circle" src="{{ $user->shop->logo_url }}"/>
                                <span class="username username-hide-on-mobile">{{ $user->shop_name }}</span>
                            </a>
                        </li>
                        <!--退出登录-->
                        <li class="dropdown dropdown-extended drop-exit ">
                            <a href="{{ url('auth/logout') }}">
                                <i class="icon-logout"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
@stop

@section('body')
    <div class="page-container public-personal contents">
        @yield('container')
        <div class="msg-channel control-center-channel" id="alert-div">
            <div class="title"><span class="pull-left">你有新消息</span><a class="close-btn  pull-right"><i
                            class="fa fa-remove"></i></a>
            </div>
            <a class="check" href="#">点击查看>>>></a>
        </div>
        <!--登出按钮-->
        <a href="javascript:;" class="page-quick-sidebar-toggler">
            <i class="icon-login"></i>
        </a>
        <!--登出按钮下的聊天-->
        <div class="page-quick-sidebar-wrapper" data-close-on-body-click="false">
            <div class="page-quick-sidebar">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="javascript:;" data-target="#quick_sidebar_tab_1"
                           data-toggle="tab"> {{ $user->shop_name }}
                            <span class="badge badge-danger total-message-count hide">0</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active page-quick-sidebar-chat" id="quick_sidebar_tab_1">
                        <div class="page-quick-sidebar-chat-users" data-rail-color="#ddd"
                             data-wrapper-class="page-quick-sidebar-list">

                        </div>
                        <div class="page-quick-sidebar-item">
                            <div class="page-quick-sidebar-chat-user">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')
    @include('includes.footer', ['class' => 'page-footer'])
@stop

@section('js-lib')
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
    <script src="{{ asset('js/index.js?v=1.0.0') }}"></script>
    <script src="{{ asset('js/ajax-polling.js') }}"></script>
    <script src="{{ asset('js/js.cookie.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery.blockui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/morris.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/echarts.common.min.js') }}"></script>
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
            });

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

            cartData();
        });
    </script>
@stop
